
<?php
$cs=Yii::app()->clientScript;
$cs->scriptMap=array(
    'jquery.js'=>false,
    'jquery.ajaxqueue.js'=>true,
    'jquery.metadata.js'=>true,
);
?>
<?php echo CGoogleApi::init(); ?>
 
<?php echo CHtml::script(
    CGoogleApi::load('jquery','1') . "\n" .
    /*CGoogleApi::load('jquery.ajaxqueue.js') . "\n" .
    CGoogleApi::load('jquery.metadata.js'). "\n" .*/
    CGoogleApi::load('jqueryui.js')

    
); ?>

