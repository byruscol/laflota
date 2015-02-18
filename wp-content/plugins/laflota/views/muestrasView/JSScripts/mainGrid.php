<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once "../../commonVehiculosGrid.php";
$params["sortname"] = "muestraId";
$params["sortorder"] = "desc";
$params["CRUD"] = array("add" => true, "edit" => true, "del" => true, "view" => true, "excel" => true, "formCols" => 4);
$view = new buildView("muestras", $params, "muestras");
?>