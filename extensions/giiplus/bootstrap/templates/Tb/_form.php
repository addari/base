<?php
/**
 * The following variables are available in this template:
 * - $this: the BootCrudCode object
 */
?>
<div class="form">
<?php echo "<?php \$form=\$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'".$this->class2id($this->modelClass)."-form',
	'enableAjaxValidation'=>false,
        'method'=>'post',
	'type'=>'horizontal',
	'htmlOptions'=>array(
		'enctype'=>'multipart/form-data'
	)
)); ?>\n"; ?>
     	<fieldset>
		<legend>
			<p class="note"><?php echo "<?php echo Yii::t('app','Fields with') ?>" ?> <span class="required">*</span> <?php echo "<?php echo Yii::t('app','are required.') ?>";?> </p>
		</legend>

	<?php echo "<?php echo \$form->errorSummary(\$model,'Opps!!!', null,array('class'=>'alert alert-error span12')); ?>\n"; ?>
        		
	<div class="control-group">		
		<div class="span4">
			<?php
			foreach($this->tableSchema->columns as $column)
			{
				if($column->autoIncrement)
					continue;
			?>
				<?php echo "<?php echo ".$this->generateActiveRow($this->modelClass,$column)."; ?>\n"; ?>

			<?php
			}
			?>
		</div>   
	</div>

	<div class="form-actions">
		<?php echo "<?php \$this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
            'icon'=>'ok white',  
			'label'=>\$model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Save'),
			)
		); ?>\n"; ?>
		<?php echo "<?php \$this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'reset',
            'icon'=>'remove',  
			'label'=>Yii::t('app','Reset'),
			)
		); ?>\n"; ?>
	</div>
</fieldset>

<?php echo "<?php \$this->endWidget(); ?>\n"; ?>

</div>
