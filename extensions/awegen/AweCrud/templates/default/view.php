<?php
$label = $this->pluralize($this->class2name($this->modelClass));
echo "<?php\n";
echo "\$this->breadcrumbs = array(
    Yii::t('app', '$label') => array('index'),
    Yii::t('app', \$model->{$this->getIdentificationColumn()}),
);";
?>
if(!isset($this->menu) || $this->menu === array()) {
$this->menu=array(
<<<<<<< HEAD
array('label'=>Yii::t('app', 'View')),
array('label'=>Yii::t('app', 'Update') , 'url'=>array('update', 'id'=>$model-><?php echo $this->tableSchema->primaryKey; ?>)),
array('label'=>Yii::t('app', 'Delete') , 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model-><?php echo $this->tableSchema->primaryKey; ?>),'confirm'=>'Are you sure you want to delete this item?')),
array('label'=>Yii::t('app', 'List') , 'url'=>array('index')),
array('label'=>Yii::t('app', 'Create') , 'url'=>array('create')),
array('label'=>Yii::t('app', 'Manage') , 'url'=>array('manage')),
=======
	array('label'=>Yii::t('app', 'Update') , 'url'=>array('update', 'id'=>$model-><?php echo Awecms::getPrimaryKey($this); ?>)),
	array('label'=>Yii::t('app', 'Delete') , 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model-><?php echo Awecms::getPrimaryKey($this); ?>),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>Yii::t('app', 'Create') , 'url'=>array('create')),
	array('label'=>Yii::t('app', 'Manage') , 'url'=>array('admin')),
	/*array('label'=>Yii::t('app', 'List') , 'url'=>array('index')),*/
>>>>>>> b298667... handle  composite foreign keys
);
}
?>

<h1><?php echo "<?php echo \$model->{$this->getIdentificationColumn()}; ?>"; ?></h1>

<?php echo '<?php'; ?> $this->widget('zii.widgets.CDetailView', array(
'data' => $model,
'attributes' => array(
<?php
foreach ($this->tableSchema->columns as $column) {
    if ($column->isForeignKey) {
<<<<<<< HEAD
        echo "\t\tarray(\n";
        echo "\t\t\t'name'=>'{$column->name}',\n";
        foreach ($this->relations as $key => $relation) {
            if ((($relation[0] == "CHasOneRelation") || ($relation[0] == "CBelongsToRelation")) && $relation[2] == $column->name) {
                $relatedModel = CActiveRecord::model($relation[1]);
                $identificationColumn = AweCrudCode::getIdentificationColumnFromTableSchema($relatedModel->tableSchema);
                $controller = $this->resolveController($relation);
                $value = "(\$model->{$key} !== null)?";
                $value .= "CHtml::link(\$model->{$key}->$identificationColumn, array('{$controller}/view','{$relatedModel->tableSchema->primaryKey}'=>\$model->{$key}->{$relatedModel->tableSchema->primaryKey})).' '";
                //$value .= ".CHtml::link(Yii::t('app','Update'), array('{$controller}/update','{$relatedModel->tableSchema->primaryKey}'=>\$model->{$key}->{$relatedModel->tableSchema->primaryKey}), array('class'=>'edit'))";
                $value .= ":'n/a'";

                echo "\t\t\t'value'=>{$value},\n";
                echo "\t\t\t'type'=>'html',\n";
                break;
            }
        }
        echo "\t\t),\n";
=======
    echo "\t\tarray(\n";
			echo "\t\t\t'name'=>'{$column->name}',\n";
			foreach ($this->relations as $key => $relation) {
			if ((($relation[0] == "CHasOneRelation") || ($relation[0] == "CBelongsToRelation")) && $relation[2] == $column->name) {
			$relatedModel = CActiveRecord::model($relation[1]);
                        $identificationColumn = AweCrudCode::getIdentificationColumnFromTableSchema($relatedModel->tableSchema);
			$controller = $this->resolveController($relation);
			$value = "(\$model->{$key} !== null)?";
      $primaryKey = Awecms::getPrimaryKey($relatedModel);
			$value .= "CHtml::link(\$model->{$key}->$identificationColumn, array('{$controller}/view','{$primaryKey}'=>\$model->{$key}->{$primaryKey})).' '";
			//$value .= ".CHtml::link(Yii::t('app','Update'), array('{$controller}/update','{$relatedModel->tableSchema->primaryKey}'=>\$model->{$key}->{$relatedModel->tableSchema->primaryKey}), array('class'=>'edit'))";
			$value .= ":'n/a'";
			
			echo "\t\t\t'value'=>{$value},\n";
			echo "\t\t\t'type'=>'html',\n";
                        break;
			}
			}
			echo "\t\t),\n";
>>>>>>> b298667... handle  composite foreign keys
    }
    else
        echo $this->getDetailViewAttribute($column);
}
echo ")));";

echo "?>";
<<<<<<< HEAD
=======
        
	
	foreach (CActiveRecord::model(Yii::import($this->model))->relations() as $key => $relation) {
		
		$controller = $this->resolveController($relation);
		$relatedModel = CActiveRecord::model($relation[1]);
		$pk = Awecms::getPrimaryKey($relatedModel);
		
		if ($relation[0] == 'CManyManyRelation' || $relation[0] == 'CHasManyRelation') {
                        $relatedModel = CActiveRecord::model($relation[1]);
                        $identificationColumn = AweCrudCode::getIdentificationColumnFromTableSchema($relatedModel->tableSchema);
			echo '<h2>';
			echo "<?php echo CHtml::link(Yii::t('app','" . ucfirst($key) . "'), array('".$controller."'));?>";
			echo "</h2>\n";
			echo CHtml::openTag('ul');
			echo "
			<?php if (is_array(\$model->{$key})) foreach(\$model->{$key} as \$foreignobj) { \n
					echo '<li>';
					echo CHtml::link(\$foreignobj->{$identificationColumn}, array('{$controller}/view','{$pk}'=>\$foreignobj->{$pk}));\n							
					}
						?>";
			echo CHtml::closeTag('ul');
>>>>>>> b298667... handle  composite foreign keys


foreach (CActiveRecord::model(Yii::import($this->model))->relations() as $key => $relation) {

    $controller = $this->resolveController($relation);
    $relatedModel = CActiveRecord::model($relation[1]);
    $pk = $relatedModel->tableSchema->primaryKey;

    if ($relation[0] == 'CManyManyRelation' || $relation[0] == 'CHasManyRelation') {
        $relatedModel = CActiveRecord::model($relation[1]);
        $identificationColumn = AweCrudCode::getIdentificationColumnFromTableSchema($relatedModel->tableSchema);
        echo "
        <?php if (count(\$model->{$key})) { ?>
                            <h2>";
        echo "<?php echo CHtml::link(Yii::t('app', Awecms::pluralize('Sub-Page', '" . ucfirst($key) . "', count(\$model->{$key}))), array('" . $controller . "'));?>";
        echo "</h2>\n";
        echo CHtml::openTag('ul');
        echo "
            <?php if (is_array(\$model->{$key})) foreach(\$model->{$key} as \$foreignobj) { \n
                    echo '<li>';
                    echo CHtml::link(\$foreignobj->{$identificationColumn}, array('{$controller}/view','{$pk}'=>\$foreignobj->{$pk}));\n							
                    }
                        ?>";
        echo CHtml::closeTag('ul');
        echo '
            <?php } ?>';
    }
}
?>
