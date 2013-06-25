
<?php
$cs=Yii::app()->clientScript;
$cs->scriptMap=array(
    'jquery.js'=>false,
    'jquery.ajaxqueue.js'=>false,
    'jquery.metadata.js'=>false,
);
?>
<?php echo CGoogleApi::init(); ?>
 
<?php echo CHtml::script(
    CGoogleApi::load('jquery','1') . "\n" .
    CGoogleApi::load('jquery.ajaxqueue.js') . "\n" .
    CGoogleApi::load('jquery.metadata.js'). "\n" .
    CGoogleApi::load('jqueryui.js')

    
); ?>

