<?php
echo "<?php\n";
$nameColumn = $this->getIdentificationColumn();
$label_plural = "Yii::t('title','" . $this->modelClass . "s')";
$label_singular = "Yii::t('title','" . $this->modelClass . "')";
echo "\$this->breadcrumbs=array(
	$label_plural=>array('index'),
	\$model->{$nameColumn},
);\n";
?>
?>
<h2><?php echo "<?php echo Yii::t('app','View').' '.$label_singular.': '.\$model->{$nameColumn}; ?>"; ?></h2>
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
array('label'=>Yii::t('app','List'), 'icon'=>'icon-th-list', 'url'=>Yii::app()->controller->createUrl('index'), 'linkOptions'=>array()),
array('label'=>Yii::t('app','Update'), 'icon'=>'icon-edit', 'url'=>Yii::app()->controller->createUrl('update',array('id'=>$model->id)), 'linkOptions'=>array()),
//array('label'=>Yii::t('app','Search'), 'icon'=>'icon-search', 'url'=>'#', 'linkOptions'=>array('class'=>'search-button')),
array('label'=>Yii::t('app','Print'), 'icon'=>'icon-print', 'url'=>'javascript:void(0);return false', 'linkOptions'=>array('onclick'=>'printDiv();return false;')),

)));
$this->endWidget();
?>
<div class='printableArea'>

    <?php echo '<?php'; ?> $this->widget('bootstrap.widgets.TbDetailView', array(
    'data' => $model,
    'attributes' => array(
    <?php
    foreach ($this->tableSchema->columns as $column) {
        if ($column->isForeignKey) {
            echo "\t\tarray(\n";
            echo "\t\t\t'name'=>'{$column->name}',\n";
            foreach ($this->relations as $key => $relation) {
                if ((($relation[0] == "CHasOneRelation") || ($relation[0] == "CBelongsToRelation")) && $relation[2] == $column->name) {
                    $relatedModel = CActiveRecord::model($relation[1]);
                    $identificationColumn = AweCrudCode::getIdentificationColumnFromTableSchema($relatedModel->tableSchema);
                    $controller = $this->resolveController($relation);
                    $value = "(\$model->{$key} !== null)?";

                    $value .= "CHtml::link(\$model->{$key}->$identificationColumn, array('" . Awecms::getPrimaryKey($relatedModel) . "/view','" . Awecms::getPrimaryKey($relatedModel) . "'=>\$model->{$key}->" . Awecms::getPrimaryKey($relatedModel) . ")).' '";
                    //$value .= ".CHtml::link(Yii::t('app','Update'), array('{$controller}/update','{$relatedModel->tableSchema->primaryKey}'=>\$model->{$key}->{$relatedModel->tableSchema->primaryKey}), array('class'=>'edit'))";
                    $value .= ":'n/a'";

                    echo "\t\t\t'value'=>{$value},\n";
                    echo "\t\t\t'type'=>'html',\n";
                    break;
                } else {
                    echo "\t\t\t'#value'=>{$value},\n";
                    echo "\t\t\t'#type'=>'html',\n";
                }
            }
            echo "\t\t),\n";
        }
        else
            echo "\t\tarray(\n";
            echo "\t\t\t'name'=>'{$this->getDetailViewAttribute($column)}',\n";
            echo "\t\t\t'label'=>'{$this->getDetailViewAttribute($column)}',\n";
            echo "\t\t\t'value'=>'$model->{$this->getDetailViewAttribute($column)}',\n";
            echo "\t\t\t'type'=>'html',\n";
            echo "\t\t),\n";
            //echo $this->getDetailViewAttribute($column);
    }
    echo ")));";

    echo "?>";
    ?>
</div>
<style type="text/css" media="print">
    body {visibility:hidden;}
    .printableArea{visibility:visible;} 
</style>
<script type="text/javascript">
    function printDiv()
    {

        window.print();

    }
</script>
<?php
foreach (CActiveRecord::model(Yii::import($this->model))->relations() as $key => $relation) {

    $controller = $this->resolveController($relation);
    $relatedModel = CActiveRecord::model($relation[1]);
    $pk = Awecms::getPrimaryKey($relatedModel);

    if ($relation[0] == 'CManyManyRelation' || $relation[0] == 'CHasManyRelation') {
        $relatedModel = CActiveRecord::model($relation[1]);
        $identificationColumn = AweCrudCode::getIdentificationColumnFromTableSchema($relatedModel->tableSchema);
        echo '<h3>';
        echo "<?php echo CHtml::link(Yii::t('app','" . ucfirst($key) . "'), array('" . $controller . "'));?>";
        echo "</h3>\n";
        echo CHtml::openTag('ul');
        echo "
			<?php if (is_array(\$model->{$key})) foreach(\$model->{$key} as \$foreignobj) { \n
					echo '<li>';
					echo CHtml::link(\$foreignobj->{$identificationColumn}, array('{$controller}/view','{$pk}'=>\$foreignobj->{$pk}));\n							
					}
						?>";
        echo CHtml::closeTag('ul');
    }
}
?>