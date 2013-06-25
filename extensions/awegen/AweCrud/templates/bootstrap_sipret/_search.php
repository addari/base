<?php /*<div class="wide form">

<?php echo "<?php \$form = \$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'action' => Yii::app()->createUrl(\$this->route),
	'method' => 'get',
)); ?>\n"; ?>

<?php foreach($this->tableSchema->columns as $column): ?>
<?php
	if (in_array(strtolower($column->name), $this->passwordFields))
		continue;
?>
  <div class="control-group">
    <?php echo "<?php echo \$form->labelEx(\$model,'{$column->name}',array('class'=>'control-label')) ; ?>\n"; ?>
    <div class="controls">
      <?php echo "<?php " . $this->generateField($column,$this->modelClass, true)."; ?>\n"; ?>
    </div>
  </div>

<?php endforeach; ?>
	<div class="row buttons">
		<?php echo "<?php echo CHtml::submitButton(Yii::t('app', 'Search')); ?>\n"; ?>
	</div>

<?php echo "<?php \$this->endWidget(); ?>\n"; ?>

</div><!-- search-form --> */ ?>


<?php echo "<?php "; ?> $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
        'id'=>'search-<?php echo $this->class2id($this->modelClass); ?>-form',
         'type' => 'horizontal',
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
));  ?>
<div class="well well-small">
  <strong><?php echo "<?php echo Yii::t('app','Information');?>";?>:</strong> <?php echo "<?php echo Yii::t('app','You may optionally enter a comparison operator');?>";?> (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
<?php echo "<?php echo Yii::t('app','or');?>";?> <b>=</b>) <?php echo "<?php echo Yii::t('app','at the beginning of each of your search values to specify how the comparison should be done.');?>";?>
</div>


<?php foreach($this->tableSchema->columns as $column): ?>
<?php
	if (in_array(strtolower($column->name), $this->passwordFields))
		continue;
?>
  <div class="control-group">
    <?php echo "<?php echo \$form->labelEx(\$model,'{$column->name}',array('class'=>'control-label')) ; ?>\n"; ?>
    <div class="controls">
      <?php echo "<?php " . $this->generateField($column,$this->modelClass, true)."; ?>\n"; ?>
    </div>
  </div>

<?php endforeach; ?>
	<div class="form-actions">
		<?php echo "<?php"; ?> $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'icon'=>'search white',
			'label'=>Yii::t('app','Search')
			)
		); ?>
    	<?php echo "<?php"; ?> $this->widget('bootstrap.widgets.TbButton', array(
    		#'buttonType'=>'button',
        	'buttonType'=>'reset',
        	#'icon'=>'icon-remove-sign white',
        	'icon'=>'remove',
        	'label'=>Yii::t('app','Reset'),
        	#'htmlOptions'=>array(
        	#	'class'=>'btnreset btn-small'
        	#	)
        	)
    	); ?>
	</div>

<?php echo "<?php \$this->endWidget(); ?>\n"; ?>


<?php echo "<?php "; ?>
$cs = Yii::app()->getClientScript();
$cs->registerCoreScript('jquery');
$cs->registerCoreScript('jquery.ui');
$cs->registerCssFile(Yii::app()->request->baseUrl.'/css/bootstrap/jquery-ui.css');
?>	
   <script>
	$(".btnreset").click(function(){
		$(":input","#search-<?php echo $this->class2id($this->modelClass); ?>-form").each(function() {
		var type = this.type;
		var tag = this.tagName.toLowerCase(); // normalize case
		if (type == "text" || type == "password" || tag == "textarea") this.value = "";
		else if (type == "checkbox" || type == "radio") this.checked = false;
		else if (tag == "select") this.selectedIndex = "";
	  });
	});
   </script>

