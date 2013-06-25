<?php $this->pageTitle = Yii::app()->name; ?>

<h1>IReport 1.0  Example</h1><br>

<p><B>Example:</B></p>
<?php
foreach(array('ireport','jasphp','phpjasper') as $type) {
print "Type $type<br />";
?>
<li><?php echo CHtml::link('Sample 0', array('index','type'=>$type, 'file' => 'sample0','p'=>'1')) ?></li>
<li><?php echo CHtml::link('Sample 1', array('index','type'=>$type, 'file' => 'sample1','p'=>'1')) ?></li>
<li><?php echo CHtml::link('Sample 2', array('index','type'=>$type, 'file' => 'sample2','p'=>'1') ) ?></li>
<li><?php echo CHtml::link('Sample 3', array('index','type'=>$type, 'file' => 'sample3','p'=>'1') ) ?></li>
<li><?php echo CHtml::link('Sample 5', array('index','type'=>$type, 'file' => 'sample5','p'=>'1')) ?></li>
<li><?php echo CHtml::link('Sample 6', array('index','type'=>$type, 'file' => 'sample6','p'=>'1')) ?></li>

<li><?php echo CHtml::link('Sample 8', array('index','type'=>$type, 'file' => 'sample8','p'=>'1')) ?></li>
<br/>
<?php
}
?>
</html>
