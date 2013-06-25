<?php 
$this->widget('zii.widgets.CListView', array(
	'id'=>'list-auth-items',
    'dataProvider'=>$dataProvider,
    'itemView'=>'_authitem',
    'sortableAttributes'=>array(
        'name',
    ),
));	
	$url_updater = CHtml::normalizeUrl(array('/cruge/ui/ajaxrbacitemdescr'));
	$loading = Yii::app()->user->ui->getResource('loading.gif');
	$loading = "<img src='{$loading}'>";
?>
<script>
	$('#list-auth-items .referencias').each(function(){
		$(this).click(function(){
			$(this).parent().find('ul').toggle('slow');
		});
	});
	// actualizador de la descripcion del authitem en base a reglas de 
	// sintaxis.
	$('#list-auth-items select').each(function(){
		$(this).change(function(){
			var action = $(this).val();
			var parent = $(this).attr('alt');
			if(action != ''){
				// hace la actualizacion via ajax y actualiza la descripcion
				// del item
				var url = '<?php echo $url_updater; ?>';
				var dateObject = new Date();
                var nocache = '&nocache='+dateObject.getTime();
				url += '&action='+action;
				url += '&itemname='+parent;
				url += nocache;
				var descrSpan = $(this).parent().parent().find('span.description');
				descrSpan.html("<?php echo $loading;?>");
				$.getJSON(url, function(data) {
					// actualiza la descripcion segun la respuesta del ajax
					
					descrSpan.html(data['description']);
				}).error(function(x){
					descrSpan.html('error: '+x.responseText);
				});
			}
		});
	});
</script>
