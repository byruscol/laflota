<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once "../../commonVehiculosGrid.php";
header('Content-type: text/javascript');
$params["sortname"] = "placa";
$params["altclass"] = "stripingRows";
$params["CRUD"] = array("add" => false, "edit" => false, "search" => false, "del" => false, "view" => false, "excel" => false);
$view = new buildView("miFlota", $params, "miFlota");
?>

jQuery('#flotaClienteModal').on('show.bs.modal', function (e) {
    jQuery("#gview_miFlota > .ui-jqgrid-titlebar").hide()
    
    var rowId =jQuery("#clientes").jqGrid('getGridParam','selrow');  
    var rowData = jQuery("#clientes").getRowData(rowId);
    var colData = rowData['propietario'];
    
    jQuery("#myModalLabel").text(colData);
})

jQuery('#searchPlacaButton').on('click', searchDataByPlaca)
