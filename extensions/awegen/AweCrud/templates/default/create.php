<?php
echo "<?php\n";
$label="Yii::t('app','".$this->class2name($this->modelClass)."')";
echo "\$this->breadcrumbs=array(
	$label=>array('index'),
	Yii::t('app','Create'),
);\n";
?>
?>
<h2><?php echo "<?php echo Yii::t('app','Create').' '.Yii::t('app','".$this->modelClass."');?>";?></h2>
<hr/>
<?php echo "<?php"; ?> 
$this->beginWidget('zii.widgets.CPortlet', array(
	'htmlOptions'=>array(
		'class'=>''
	)
));
$this->widget('bootstrap.widgets.TbMenu', array(
	'type'=>'pills',
	'items'=>array(
		array('label'=>Yii::t('app','Create'), 'icon'=>'icon-plus', 'url'=>Yii::app()->controller->createUrl('create'),'active'=>true, 'linkOptions'=>array()),
                array('label'=>Yii::t('app','List'), 'icon'=>'icon-th-list', 'url'=>Yii::app()->controller->createUrl('index'), 'linkOptions'=>array()),
		#array('label'=>Yii::t('app','Search'), 'icon'=>'icon-search', 'url'=>'#', 'linkOptions'=>array('class'=>'search-button')),
	),
));
$this->endWidget();
?>
<?php echo "<?php\n"; ?>
$this->renderPartial('_form', array(
            'model' => $model,
            'buttons' => 'create'));

?>