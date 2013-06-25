<?php

/**
 * AweActiveRecord class file.
 *
 * @author Ricardo Obregón <ricardo@obregon.co>
 * @link http://awecrud.obregon.co/
 * @copyright Copyright &copy; 2012 Ricardo Obregón
 * @license http://awecrud.obregon.org/license/ New BSD License
 */

/**
 * AweActiveRecord is the base class for the generated AR (base) models.
 *
 * @author Ricardo Obregón <ricardo@obregon.co>
 * @package AweCrud.components
 */
abstract class AweActiveRecord extends CActiveRecord
{
    public $oldValues = array();

    /**
     * @var string The separator (delimiter) used to separate the primary keys values in a
     * string representation of the pks of a composite pk record. Usually a character.
     */
    public $pkSeparator = '-';
    /**
     * @var string The separator (delimiter) used to separate the {@link representingColumn}
     * values when there are multiple representing columns while building the
     * string representation of the record in {@link __toString}.
     */
    public $repColumnsSeparator = '-';

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * The active record label.
     * The active record label is the user friendly name displayed in the views.
     * Each active record class should override this method and explicitly specify the label.
     * See the documentation when overriding: http://www.yiiframework.com/doc/guide/1.1/en/topics.i18n#plural-forms-format
     * @param integer $n The number value. This is used to support plurals. Defaults to 1 (means singular).
     * Notice that this number doesn't necessarily corresponds to the number (count) of items.
     * @return string The label.
     * @throws CException If the method wasn't overriden.
     * @see getRelationLabel
     */
    public static function label($n = 1)
    {
        throw new CException(Yii::t('AweCrud.messages', 'This method should be overriden by the Active Record class.'));
    }

    /**
     * PHP getter magic method.
     * This method is overridden so that AR attributes can be accessed like properties.
     * @param string $name property name
     * @return mixed property value
     * @see getAttribute
     */
    public function __get($name) {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter))
            return $this->$getter();
        return parent::__get($name);
    }

    /**
     * PHP setter magic method.
     * This method is overridden so that AR attributes can be accessed like properties.
     * @param string $name property name
     * @param mixed $value property value
     */
    public function __set($name, $value) {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter))
            return $this->$setter($value);
        parent::__set($name, $value);
    }

    public function afterFind() {
        parent::afterFind();
        $this->oldValues = $this->getAttributes();
    }

    /**
     * Returns the text label for the specified active record relation, attribute or class property.
     * The labels are the user friendly names displayed in the views.
     * If defined in the model, the label for its attribute, property or relation is returned.
     * If not defined in the model (in {@link CModel::attributeLabels}),
     * the label is generated using the related active record class label (via {@link AweActiveRecord::label}) (for FK attributes and relations)
     * or using {@link CModel::generateAttributeLabel} (for other attributes and class properties).
     * @param string $relationName The relation, attribute or class property name.
     * This method supports chained relations in the form of "post.author.name".
     * @param integer $n The number value. This is used to support plurals.
     * In the default implementation, when this argument is null, if the relation is BELONGS_TO or HAS_ONE, the singular form is returned.
     * If the relation is HAS_MANY or MANY_MANY, the plural form is returned.
     * If this argument is null and the relation is not one of the types listed above, the singular form is returned.
     * For most languages, 1 means singular and all other values mean plural.
     * Defaults to null.
     * Note: It is not supported when returning labels for attributes or class properties.
     * @param boolean $useRelationLabel Whether to use the relation label for the FK attribute.
     * When true, if the specified attribute name is a FK, the corresponding related AR label will be used.
     * Defaults to true.
     * Note: this will only work when there is no label defined in {@link CModel::attributeLabels} for this attribute.
     * @return string The label.
     * @throws InvalidArgumentException If an attribute name is found and is not the last item in the relationName parameter.
     * @uses label
     */
    public function getRelationLabel($relationName, $n = null, $useRelationLabel = true)
    {
        // Exploding the chained relation names.
        $relNames = explode('.', $relationName);

        // Everything starts with this object.
        $relClassName = get_class($this);

        // The item index.
        $relIndex = 0;

        // Get the count of relation names;
        $countRelNames = count($relNames);

        // Walk through the chained relations.
        foreach ($relNames as $relName) {
            // Increments the item index.
            $relIndex++;

            // Get the related static class.
            $relStaticClass = self::model($relClassName);

            // If is is the last name and the label is explicitly defined, return it.
            if ($relIndex === $countRelNames) {
                $labels = $relStaticClass->attributeLabels();
                if (isset($labels[$relName])) {
                    return $labels[$relName];
                }
            }

            // Get the relations for the current class.
            $relations = $relStaticClass->relations();

            // Check if there is (not) a relation with the current name.
            if (!isset($relations[$relName])) {
                // There is no relation with the current name. It is an attribute or a property.
                // It must be the last name.
                if ($relIndex === $countRelNames) {
                    // Check if it is an attribute.
                    $attributeNames = $relStaticClass->attributeNames();
                    $isAttribute = in_array($relName, $attributeNames);
                    // If it is an attribute and the attribute is a FK and $useRelationLabel is true, return the related AR label.
                    if ($isAttribute && $useRelationLabel && (($relData = self::findRelation(
                        $relStaticClass,
                        $relName
                    )) !== null)
                    ) {
                        // This will always be a BELONGS_TO, then singular.
                        return self::model($relData[3])->label(1);
                    } else {
                        // There's no label for this attribute or property, generate one.
                        return $relStaticClass->generateAttributeLabel($relName);
                    }
                } else {
                    // It is not the last item.
                    throw new InvalidArgumentException(Yii::t(
                        'AweCrud.messages',
                        'The attribute "{attribute}" should be the last name.',
                        array('{attribute}' => $relName)
                    ));
                }
            }

            // Change the current class name: walk to the next relation.
            $relClassName = $relations[$relName][1];
        }

        // Automatically apply the correct number if requested.
        if ($n === null) {
            // Get the type of the last relation from the last but one class.
            $relType = $relations[end($relNames)][0];

            switch ($relType) {
                case self::HAS_MANY:
                case self::MANY_MANY:
                    $n = 2;
                    break;
                case self::BELONGS_TO:
                case self::HAS_ONE:
                default :
                    $n = 1;
            }
        }

        // Get and return the label from the related AR.
        return self::model($relClassName)->label($n);
    }

    /**
     * Returns the text label for the specified attribute.
     * Also supported: relations and chained relations in the form of "post.author.name".
     * This method just calls {@link getRelationLabel}.
     * @param string $attribute The attribute name.
     * @return string The attribute label.
     * @see CActiveRecord::getAttributeLabel
     * @see getRelationLabel
     */
    public function getAttributeLabel($attribute)
    {
        return $this->getRelationLabel($attribute);
    }

    /**
     * The specified column(s) is(are) the responsible for the
     * string representation of the model instance.
     * The column is used in the {@link __toString} default implementation.
     * Every model must specify the attributes used to build their
     * string representation by overriding this method.
     * This method must be overriden in each model class
     * that extends this class.
     * @return string|array The name of the representing column for the table (string) or
     * the names of the representing columns (array).
     * @see __toString
     */
    public static function representingColumn()
    {
        return null;
    }

    /**
     * Returns a string representation of the model instance, based on
     * {@link representingColumn}.
     * When you override this method, all model attributes used to build
     * the string representation of the model must be specified in
     * {@link representingColumn}.
     * @return string The string representation for the model instance.
     * @throws CException If {@link representingColumn} is not defined.
     * @uses representingColumn
     * @uses repColumnsSeparator
     */
    public function __toString()
    {
        $representingColumn = $this->representingColumn();

        if (empty($representingColumn)) {
            throw new CException(Yii::t(
                'AweCrud.messages',
                'The representing column for the active record "{model}" is not set.',
                array(
                    '{model}' => get_class($this),
                )
            ));
        }

        if (is_array($representingColumn)) {
            $repValues = array();
            foreach ($representingColumn as $repColumn_item) {
                $repValues[] = ((($repColumn_item_value = $this->$repColumn_item) === null) ? '' : (string)$repColumn_item_value);
            }
            return implode($this->repColumnsSeparator, $repValues);
        } else {
            return ((($repColumn_value = $this->$representingColumn) === null) ? '' : (string)$repColumn_value);
        }
    }

    /**
     * Finds the relation of the specified column.
     * @param string|AweActiveRecord $modelClass The model class name or a model instance.
     * @param string|CDbColumnSchema $column The column.
     * @return array The relation. The array will have 3 values:
     * 0: the relation name,
     * 1: the relation type (will always be AweActiveRecord::BELONGS_TO),
     * 2: the foreign key (will always be the specified column),
     * 3: the related active record class name.
     * Or null if no matching relation was found.
     */
    public static function findRelation($modelClass, $column)
    {
        if (is_string($modelClass)) {
            $staticModelClass = self::model($modelClass);
        } else {
            $staticModelClass = self::model(get_class($modelClass));
        }

        if (is_string($column)) {
            $column = $staticModelClass->getTableSchema()->getColumn($column);
        }

        if (!$column->isForeignKey) {
            return null;
        }

        $relations = $staticModelClass->relations();
        // Find the relation for this attribute.
        foreach ($relations as $relationName => $relation) {
            // For attributes on this model, relation must be BELONGS_TO.
            if (($relation[0] === AweActiveRecord::BELONGS_TO) && ($relation[2] === $column->name)) {
                return array(
                    $relationName, // the relation name
                    $relation[0], // the relation type
                    $relation[2], // the foreign key
                    $relation[1] // the related active record class name
                );
            }
        }
        // None found.
        return null;
    }

    /**
     * @param $relationName
     * @param $data PKs of the currently related records
     * @throws CDbException
     */
    public function saveManyMany($relationName, $data)
    {
        if($this->getIsNewRecord())
            throw new CDbException(Yii::t('yii','The active record cannot be updated because it is new.'));

        /** @var CDbCommandBuilder $commandBuilder */
        $commandBuilder=$this->dbConnection->commandBuilder;
        $relations = $this->relations();
        $relation = $relations[$relationName];

        if($relation[0]!==self::MANY_MANY){
            throw new CDbException(Yii::t('AweCrud.app', 'This is NOT a MANY_MANY relation'));
        }

        Yii::trace('Updating MANY_MANY table for relation ' . get_class($this) . '.' . $relationName, 'system.db.ar.CActiveRecord');

        // get table and fk information
        list($relationTable, $fks) = $this->parseManyManyFk($relationName, $relation);

        // 1. Delete relation table entries for records that have been removed from relation
        // @warning WE DON'T SUPPORT COMPOSITE PKs (yet)
        $criteria = new CDbCriteria();
        //var_dump($fks,$currentPKs, $this->primaryKey, $data);die();
        $criteria->addNotInCondition($fks[1], $data/*$currentPKs*/)
            ->addColumnCondition(array($fks[0] => $this->getPrimaryKey()));
        $deleted = $commandBuilder->createDeleteCommand($relationTable, $criteria)->execute();

        // 2. Gather the current records
        $newCriteria = new CDbCriteria(array(
            'select' => $fks[1], // $fks?
        ));
        $newCriteria->addColumnCondition(array($fks[0] => $this->primaryKey));
        $currentPKs = $commandBuilder->createFindCommand($relationTable, $newCriteria)->queryColumn();
        if($currentPKs===false){
            $currentPKs = array();
        }

        // 3. add new entries to relation table
        // @warning WE DON'T SUPPORT COMPOSITE PK's (yet)
        foreach ($data as $fk) {
            if (!in_array($fk, $currentPKs)) {
                $commandBuilder->createInsertCommand(
                    $relationTable,
                    array(
                        $fks[0] => $this->getPrimaryKey(),
                        $fks[1] => $fk,
                    )
                )->execute();
                $currentPKs[] = $fk;
            }
        }

        // refresh relation data
        //$this->getRelated($name, true); // will come back with github issue #4
    }

    /**
     * Parses the foreign key definition of a MANY_MANY relation
     *
     * the first 7 lines are copied from CActiveFinder:561-568
     * https://github.com/yiisoft/yii/blob/2353e0adf98c8a912f0faf29cc2558c0ccd6fec7/framework/db/ar/CActiveFinder.php#L561
     *
     * @todo this method should be removed and using code should implement solution of https://github.com/yiisoft/yii/issues/508 when it is fixed
     *
     * @throws CDbException
     * @param string $name name of the relation
     * @param array $relation relation definition
     * @return array ($joinTable, $fks)
     *               joinTable is the many-many-relation-table
     *               fks are primary key of that table defining the relation
     */
    protected function parseManyManyFk($name, $relation)
    {
        if(!preg_match('/^\s*(.*?)\((.*)\)\s*$/',$relation[2],$matches))
            throw new CDbException(Yii::t('yii','The relation "{relation}" in active record class "{class}" is specified with an invalid foreign key. The format of the foreign key must be "joinTable(fk1,fk2,...)".',
                array('{class}'=>get_class($this),'{relation}'=>$name)));
        if(($joinTable=$this->dbConnection->schema->getTable($matches[1]))===null)
            throw new CDbException(Yii::t('yii','The relation "{relation}" in active record class "{class}" is not specified correctly: the join table "{joinTable}" given in the foreign key cannot be found in the database.',
                array('{class}'=>get_class($this), '{relation}'=>$name, '{joinTable}'=>$matches[1])));
        $fks=preg_split('/\s*,\s*/',$matches[2],-1,PREG_SPLIT_NO_EMPTY);

        return array($joinTable, $fks);
    }

    /**
     * Returns all primary keys of the currently assigned records
     *
     * @throws CDbException
     * @param string $relationName name of the relation
     * @return array
     */
    private function getNewManyManyPks($relationName)
    {

        $newRelatedRecords=$this->getRelated($relationName, false);
        if (!is_array($newRelatedRecords)) {
            throw new CDbException('A MANY_MANY relation needs to be an array of records or primary keys!');
        }
        // get new related records primary keys
        return $this->objectsToPrimaryKeys($newRelatedRecords);
    }

    /**
     * converts an array of AR objects or primary keys to only primary keys
     *
     * @throws CDbException
     * @param CActiveRecord[] $records
     * @return array
     */
    private function objectsToPrimaryKeys($records)
    {
        $pks=array();
        foreach($records as $record) {
            if (is_object($record) && $record->isNewRecord)
                throw new CDbException('You can not save a record that has new related records!');

            $pks[]=is_object($record) ? $record->getPrimaryKey() : $record;
        }
        return $pks;
    }

}