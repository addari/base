<?php
echo "<?php\n";
$label_plural="Yii::t('title','".$this->modelClass."s')";
$label_singular="Yii::t('title','".$this->modelClass."')";
echo "\$this->breadcrumbs=array(
	$label_plural,
);\n";
?>

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').slideToggle('fast');
    return false;
});
$('.search-form form').submit(function(){
    $.fn.yiiGridView.update('<?php echo $this->class2id($this->modelClass); ?>-grid', {
        data: $(this).serialize()
    });
    return false;
});
");

?>

<h2><?php echo '<?php echo '.$label_plural.'; ?>'; ?></h2>
<hr />

<?php echo "<?php"; ?> 
$this->beginWidget('zii.widgets.CPortlet', array(
	'htmlOptions'=>array(
		'class'=>''
	)
));
$this->widget('bootstrap.widgets.TbMenu', array(
	'type'=>'pills',
	'items'=>array(
		array('label'=>Yii::t('app','Create'), 'icon'=>'icon-plus', 'url'=>Yii::app()->controller->createUrl('create'), 'linkOptions'=>array()),
        array('label'=>Yii::t('app','List'), 'icon'=>'icon-th-list', 'url'=>Yii::app()->controller->createUrl('index'),'active'=>true, 'linkOptions'=>array()),
		array('label'=>Yii::t('app','Search'), 'icon'=>'icon-search', 'url'=>'#', 'linkOptions'=>array('class'=>'search-button')),
		array('label'=>Yii::t('app','Export to PDF'), 'icon'=>'icon-download', 'url'=>Yii::app()->controller->createUrl('GeneratePdf'), 'linkOptions'=>array('target'=>'_blank'), 'visible'=>true),
		array('label'=>Yii::t('app','Export to CSV'), 'icon'=>'icon-download', 'url'=>Yii::app()->controller->createUrl('GenerateExcel'), 'linkOptions'=>array('target'=>'_blank'), 'visible'=>true),
	),
));
$this->endWidget();
?>



<div class="search-form" style="display:none">
<?php echo "<?php \$this->renderPartial('_search',array(
	'model'=>\$model,
)); ?>\n"; ?>
</div><!-- search-form -->
<?php echo '<?php'; ?> 
$provider=$model->search();
$provider->pagination->pageSize=10;
$this->widget('bootstrap.widgets.TbExtendedGridView', array(
'id' => '<?php echo $this->class2id($this->modelClass); ?>-grid',
'type'=>'striped bordered condensed',
'dataProvider' => $provider,
'filter' => $model,
'columns' => array(
<?php
foreach ($this->tableSchema->columns as $column) {
  echo  $this->generateGridViewColumn($column).",\n";

}
?>
array(
'class'=>'bootstrap.widgets.TbButtonColumn',
'htmlOptions'=>array('style'=>'width: 55px'),
),
),
)); ?>
