<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once "../../commonVehiculosGrid.php";
header('Content-type: text/javascript');
global $siteURL;
$params["sortname"] = "placa";
$params["altclass"] = "stripingRows";
$params["postData"]["method"] = "geMyFleet";
$params["CRUD"] = array("add" => false, "edit" => false, "search" => false, "del" => false, "view" => false, "excel" => false);
$view = new buildView("miFlota", $params, "miFlota");
?>
jQuery("#gview_miFlota > .ui-jqgrid-titlebar").hide()
jQuery('#searchPlacaButton').on('click', function (e) {
    var val = jQuery("#placaInput").val();
    filters = '';
    if(val != ""){
        filters = '{"groupOp":"AND","rules":[{"field":"placa","op":"cn","data":"'+val+'"}]}';
    }
    
    postDataObj = jQuery("#miFlota").jqGrid("getGridParam","postData");
    postDataObj["filters"] = filters;
    	
    jQuery("#miFlota").jqGrid("setGridParam",{postData: postDataObj})
                    .trigger("reloadGrid");
});

jQuery(document).ready(function(){
    jQuery.ajax({
        type: "POST",
        url: "<?php echo $siteURL;?>/wp-admin/admin-ajax.php",
        data: {action:"action", id:"miFlota", method: "getExtendedCriticalVehicles", type:"user"},
        success: extendedCritial
     });
});