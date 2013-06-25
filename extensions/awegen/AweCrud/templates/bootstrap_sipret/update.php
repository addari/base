<?php
echo "<?php\n";
$nameColumn=$this->getIdentificationColumn();
$label_plural="Yii::t('title','".$this->modelClass."s')";
$label_singular="Yii::t('title','".$this->modelClass."')";
echo "\$this->breadcrumbs=array(
	$label_plural=>array('index'),
	\$model->{$nameColumn}=>array('view','id'=>\$model->{$this->tableSchema->primaryKey}),
	Yii::t('app','Update'),
);\n";
?>

?>

<h1><?php echo "<?php echo Yii::t('app','Update').' '.$label_singular.': '.\$model->{$nameColumn}; ?>"; ?></h1>
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
		array('label'=>'Create', 'icon'=>'icon-plus', 'url'=>Yii::app()->controller->createUrl('create'), 'linkOptions'=>array()),
                array('label'=>Yii::t('app','List'), 'icon'=>'icon-th-list', 'url'=>Yii::app()->controller->createUrl('index'), 'linkOptions'=>array()),
                array('label'=>Yii::t('app','Update'), 'icon'=>'icon-edit', 'url'=>Yii::app()->controller->createUrl('update',array('id'=>$model->id)),'active'=>true, 'linkOptions'=>array()),
	),
));
$this->endWidget();
?>
<?php echo "<?php echo \$this->renderPartial('_form',array('model'=>\$model)); ?>"; ?>