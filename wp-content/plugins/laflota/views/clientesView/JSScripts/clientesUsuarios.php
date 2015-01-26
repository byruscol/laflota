<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once "../../commonVehiculosGrid.php";
$params["sortname"] = "clientesUsuariosId";
$params["CRUD"] = array("add" => true, "edit" => true, "del" => true, "view" => false, "excel" => true);
$view = new buildView("clientesUsuarios", $params, "clientesUsuarios");
?>
