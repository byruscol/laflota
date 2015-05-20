<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once "../../../helpers/Grid.php";
require_once "../../class.buildView.php";
header('Content-type: text/javascript');
global $pluginURL;
global $siteURL;

$user = wp_get_current_user();
$currentUserRoles = (array) $user->roles;

$adminRoleAllow = array("administrator");
$comercialRoleAllow = array("contributor");

if( array_intersect($adminRoleAllow, $currentUserRoles ) )
    $CRUD = array("add" => true, "edit" => true, "del" => false, "view" => true, "files" => false, "excel"=>true);
elseif ( array_intersect($comercialRoleAllow, $currentUserRoles ) ) 
    $CRUD = array("add" => false, "edit" => false, "del" => false, "view" => true, "files" => false, "excel"=>true);


$onSelectRow = 'function(id) {
    if(id != null) {';
        if( array_intersect($adminRoleAllow, $currentUserRoles ) ){
            $onSelectRow .= 'postDataObj = jQuery("#vehiculos").jqGrid("getGridParam","postData");
            postDataObj["filter"] = id;
            postDataObj["parent"] = "'.$_GET["view"].'";
                jQuery("#vehiculos").jqGrid("setGridParam",{postData: postDataObj})
                            .trigger("reloadGrid");
            
            postDataObj = jQuery("#clientesUsuarios").jqGrid("getGridParam","postData");
            postDataObj["filter"] = id;
            postDataObj["parent"] = "'.$_GET["view"].'";
            jQuery("#clientesUsuarios").jqGrid("setGridParam",{postData: postDataObj})
                            .trigger("reloadGrid");';
        }
        $onSelectRow .= '    postDataObj = jQuery("#miFlota").jqGrid("getGridParam","postData");
            postDataObj["filter"] = id;
            jQuery("#miFlota").jqGrid("setGridParam",{postData: postDataObj})
                            .trigger("reloadGrid");

            jQuery.ajax({
                    type: "POST",
                    url: "'.$siteURL.'/wp-admin/admin-ajax.php",
                    data: {action:"action", filter: id, id:"miFlota", method: "getExtendedCriticalVehicles"},
                    success: extendedCritial
                  });
    }
}';

$params = array("numRows" => 10
                , "sortname" => "propietario"
                , "CRUD" => $CRUD
                , "actions" => array(
                                        array("type" => "onSelectRow"
                                                  ,"function" => $onSelectRow
                                                )
                                    )
            );
$view = new buildView($_GET["view"], $params, "clientes");
?>
