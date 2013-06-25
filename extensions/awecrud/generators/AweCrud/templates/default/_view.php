<?php
/**
 * The following variables are available in this template:
 * - $this: the BootCrudCode object
 */
/** @var AweCrudCode $this */
?>
<?php echo "<?php".PHP_EOL; ?>
/** @var <?php echo $this->controllerClass; ?> $this */
/** @var <?php echo $this->modelClass; ?> $data */
?>
<div class="view">
<?php /*
    <b><?php echo "<?php echo CHtml::encode(\$data->getAttributeLabel('{$this->tableSchema->primaryKey}')); ?>:" ?></b>
    <?php echo
    "\t<?php echo CHtml::link(CHtml::encode(\$data->{$this->tableSchema->primaryKey}),array('view','id'=>\$data->{$this->tableSchema->primaryKey})); ?>\n\t<br />\n\n"
    ;
    $count = 0;
    foreach ($this->tableSchema->columns as $column) {
        if ($column->isPrimaryKey) {
            continue;
        }
        if (++$count == 7) {
            echo "\t<?php /*\n";
        }
        echo "\t<b><?php echo CHtml::encode(\$data->getAttributeLabel('{$column->name}')); ?>:</b>\n";
        echo "\t<?php echo CHtml::encode(\$data->{$column->name}); ?>\n\t<br />\n\n";
    }
    if ($count >= 7) {
        echo "\t* / ?>\n";
    }*/
    ?>
    <?php foreach ($this->getTableSchema()->columns as $column) : ?>
    <?php if (!$column->isPrimaryKey && !in_array(
        strtolower($column->name),
        $this->passwordFields
    )
    ): ?>
        <?php
        $columnName = $column->name;
        if ($column->isForeignKey) {
            $relations = $this->getRelations();
            foreach ($relations as $relationName => $relation) {
                if ($relation[2] == $columnName) {
                    $relatedModel = CActiveRecord::model($relation[1]);
                    $columnName = $relationName . '->' . AweCrudCode::getIdentificationColumnFromTableSchema(
                        $relatedModel->tableSchema
                    );
                }
            }
        }
        ?>

<?php if (!in_array($column->dbType, $this->booleanTypes)): ?>
        <?php echo "<?php if (!empty(\$data->{$columnName})): ?>".PHP_EOL; ?>
<?php endif; ?>
        <div class="field">
            <div class="field_name">
                <b><?php echo "<?php echo CHtml::encode(\$data->getAttributeLabel('{$column->name}')); ?>" ?>:</b>
            </div>
            <div class="field_value">
<?php if (in_array($column->dbType, $this->dateTypes)): ?>
                <?php echo "<?php echo Yii::app()->getDateFormatter()->formatDateTime(\$data->{$columnName}, 'medium', 'medium'); ?>" . PHP_EOL; ?>
            <br/>
                <?php echo " <?php echo date('D, d M y H:i:s', strtotime(\$data->" . $columnName . ")); ?>" . PHP_EOL; ?>
                <?php elseif (in_array($column->dbType, $this->booleanTypes)): ?>
                <?php echo "<?php echo CHtml::encode(\$data->{$columnName} == 1 ? 'True' : 'False'); ?>" . PHP_EOL; ?>
                <?php elseif (in_array(strtolower($columnName), $this->emailFields)): ?>
                <?php echo "<?php echo CHtml::mailto(\$data->{$columnName}); ?>" . PHP_EOL; ?>
                <?php elseif (in_array($column->dbType, array('longtext'))): ?>
                <?php echo "<?php echo nl2br(\$data->{$columnName}); ?>" . PHP_EOL; ?>
                <?php elseif (in_array(strtolower($columnName), $this->imageFields)): ?>
                <?php echo "<img alt=\"<?php echo \$data ?>\" title=\"<?php echo \$data ?>\" src=\"<?php echo \$data->{$columnName}; ?>\" />" . PHP_EOL; ?>
                <?php elseif (in_array(strtolower($columnName), $this->urlFields)) : ?>
                <?php echo "<?php echo AweHtml::formatUrl(\$data->{$columnName}, true); ?>" . PHP_EOL; ?>
                <?php else : ?>
                <?php echo "<?php echo CHtml::encode(\$data->{$columnName}); ?>" . PHP_EOL; ?>
<?php endif; ?>
            </div>
        </div>

    <?php if (!in_array($column->dbType, $this->booleanTypes)): ?>
    <?php echo "<?php endif; ?>\n" ?>
    <?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>
</div>