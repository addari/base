<?php
/**
 * This is the template for generating the model class of a specified table.
 * In addition to the default model Code, this adds the CSaveRelationsBehavior
 * to the model class definition.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $modelClass: the model class name
 * - $columns: list of table columns (name=>CDbColumnSchema)
 * - $labels: list of attribute labels (name=>label)
 * - $rules: list of validation rules
 * - $relations: list of relations (name=>relation declaration)
 */
?>
<?php echo "<?php\n"; ?>

/**
 * This is the model base class for the table "<?php echo $tableName; ?>".
 *
 * Columns in table "<?php echo $tableName; ?>" available as properties of the model:
 
<?php 
    foreach($columns as $key => $column): ?>
      * @property <?php echo $column->type.' $'.$column->name."\n"; ?>
<?php 
    endforeach; ?>
 *
<?php if(count($relations)>0): ?>
 * Relations of table "<?php echo $tableName; ?>" available as properties of the model:
<?php else: ?>
 * There are no model relations.
<?php endif; ?>
<?php foreach($relations as $name=>$relation): ?>
 * @property <?php
    if (preg_match("~^array\(self::([^,]+), '([^']+)', '([^']+)'\)$~", $relation, $matches))
    {
        $relationType = $matches[1];
        $relationModel = $matches[2];

        switch($relationType){
            case 'HAS_ONE':
                echo $relationModel.' $'.$name."\n";
            break;
            case 'BELONGS_TO':
                echo $relationModel.' $'.$name."\n";
            break;
            case 'HAS_MANY':
                echo $relationModel.'[] $'.$name."\n";
            break;
            case 'MANY_MANY':
                echo $relationModel.'[] $'.$name."\n";
            break;
            default:
                echo 'mixed $'.$name."\n";
        }
    }
    ?>
<?php endforeach; ?>
 */
abstract class <?php echo 'Base' . $modelClass; ?> extends <?php echo $this->baseClass; ?> {
    
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '<?php echo $tableName; ?>';
    }

    public function rules() {
        return array(

        <?php
            foreach($rules as $rule):
                            //Elininacion de Campos reervados
                            //$campos_reservados = array(', user_create, user_modified, date_create, date_modified, ip_create, ip_modified', ', ip_create, ip_modified');
                            $campos_reservados = array(', user_create',', user_update',', date_create',', date_update',', ip_create',', ip_update',', timestamp');
                            $blanco  = '';
                            $text    = $rule;
                            $output  = str_replace($campos_reservados, $blanco, $text);
                            echo "			".$output.",\n";
                            //echo $rule.",\n";
            endforeach;
                            //Elininacion de Campos reervados
                            $text    = implode(', ', array_keys($columns));
                            $output  = str_replace($campos_reservados, $blanco, $text);
        ?>
                        // The following rule is used by search().
                        // Please remove those attributes that should not be searched.
                        array('<?php echo $output; ?>', 'safe', 'on'=>'search'),   
        );
    }
    
    public function __toString() {
        return (string) $this-><?php
            $found = false;
        foreach($columns as $name => $column) {
            if(!$found 
                    && $column->type != 'datetime'
                    && $column->type==='string' 
                    && !$column->isPrimaryKey){
                echo $column->name;
                $found = true;
            }
        }
        // if the columns contains no column of type 'string', return the
        // first column (usually the primary key)
        if(!$found)
            echo reset($columns)->name; 
        ?>;
    }

    public function behaviors() {
        <?php
            $behaviors = 'return array(';
                    foreach($columns as $name => $column) {
                    if(in_array($column->name, array(
//                                'create_time',
//                                'createtime',
//                                'created_at',
//                                'createdat',
//                                'changed',
//                                'changed_at',
//                                'updatetime',
//                                'update_time',
                                'user_create',
                                'user_update',
                                'date_create',
                                'date_update',
                                'ip_create',
                                'ip_update',
                                'timestamp'))) {
                    $behaviors .= sprintf("\n                    'CTimestampBehavior' => array(
                        'class' => 'zii.behaviors.CTimestampBehavior',
                        'createAttribute' => %s,
                        'updateAttribute' => %s,
                    ),\n", $this->getCreatetimeAttribute($columns),
                        $this->getUpdatetimeAttribute($columns));
                    break; // once a column is found, we are done
                    }
                    }
                                        
//                    foreach($columns as $name => $column) {
//                        if(in_array($column->name, array(
//                                        'user_id',
//                                        'userid',
//                                        'ownerid',
//                                        'owner_id',
//                                        'created_by',
//                                        'createdby'))) {
//                            $behaviors .= sprintf("\n        'OwnerBehavior' => array(
//                                'class' => 'OwnerBehavior',
//                            'ownerColumn' => '%s',
//                                ),\n", $column->name);
//                            break; // once a column is found, we are done
//
//                        }
//                    }
                    
                    if (count($relations)){
                        $behaviors .= "\n        'activerecord-relation' => array('class' => 'EActiveRecordRelationBehavior')"."\n";
                            
                    }


                    $behaviors .= ");\n";
                    echo $behaviors;
                    ?>
    }

    public function relations() {
        return array(
<?php
        foreach($relations as $name=>$relation) {
            echo "            '$name' => $relation,\n";
        }
?>
        );
    }

    public function attributeLabels() {
        return array(
<?php
        foreach($labels as $name=>$label) {
            #echo "'$name' => Yii::t('app', '$label'),\n";
            echo "'$name' => Yii::t('label','$modelClass.$name'),\n"; // linea agregada para componer etiquetas con modelos y nombres de campos para traducción
        }
?>
        );
    }

    public function search() {
        $criteria = new CDbCriteria;

<?php
        foreach($columns as $name=>$column)
        {
            if($column->type==='string' and !$column->isForeignKey)
            {
                echo "        \$criteria->compare('$name', \$this->$name, true);\n";
            }
            else
            {
                echo "        \$criteria->compare('$name', \$this->$name);\n";
            }
        }
        echo "\n";
?>
        return new CActiveDataProvider(get_class($this), array(
                    'criteria' => $criteria,
                ));
    }
    
}