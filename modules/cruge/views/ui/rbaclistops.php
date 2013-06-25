<h1><?php echo ucwords(CrugeTranslator::t("operaciones"));?></h1>

<div class='auth-item-create-button'>
<?php echo CHtml::link(CrugeTranslator::t("Crear Nueva Operacion")
	,Yii::app()->user->ui->getRbacAuthItemCreateUrl(CAuthItem::TYPE_OPERATION));?>
</div>

<?php 
	echo CrugeTranslator::t("Filtrar por Controlador:");

	$list = array();
	$list[0] = '-ver todo-';
	$list[1] = '-Otras-';
	$list[2] = '-Cruge-';
	$list[3] = '-Controladoras-';
	foreach(Yii::app()->user->rbac->enumControllers() as $c)
		$list[$c] = $c;
	$curFilter = isset($_GET['filter']) ? $_GET['filter'] : '';
	echo "&nbsp;&nbsp;".CHtml::dropDownList("controllers",$curFilter
			,$list);

	$url = CHtml::normalizeUrl(array('/cruge/ui/rbaclistops'));
	Yii::app()->clientScript->registerScript('filtrocruge', 
	"
		function _CrugeFilterChange() {
			var filter = $('#controllers').val();
			var flag=0;
			document.location.href = '{$url}&filter='+filter+'&menuonly='+flag;
		}
		$('#controllers').change(_CrugeFilterChange);
	");
?>

<?php $this->renderPartial('_listauthitems'
	,array('dataProvider'=>$dataProvider)
	,false
	);?>
