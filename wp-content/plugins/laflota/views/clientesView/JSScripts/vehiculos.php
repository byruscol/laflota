<?php
require_once "../../commonVehiculosGrid.php";
$user = wp_get_current_user();
$currentUserRoles = (array) $user->roles;
$adminRoleAllow = array("administrator");
if( array_intersect($adminRoleAllow, $currentUserRoles ) ){
$params["postData"]["method"] = "getVehiculosCliente";
$params["sortname"] = "placa";
$params["CRUD"] = array("add" => true, "edit" => true, "del" => false, "view" => false, "excel" => true);
$view = new buildView("vehiculos", $params, "vehiculos");
}
?>
