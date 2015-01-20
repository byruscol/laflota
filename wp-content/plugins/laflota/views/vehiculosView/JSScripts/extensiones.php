<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once "../../commonVehiculosGrid.php";
$params["sortname"] = "kilometraje";
$params["sortorder"] = "desc";
$params["CRUD"] = array("add" => true, "edit" => true, "del" => true, "view" => false, "excel" => true);
$view = new buildView("extensiones", $params, "extensiones");
?>
