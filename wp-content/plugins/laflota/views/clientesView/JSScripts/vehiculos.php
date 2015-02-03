<?php
require_once "../../commonVehiculosGrid.php";
$params["postData"]["method"] = "getVehiculosCliente";
$params["sortname"] = "placa";
$params["CRUD"] = array("add" => true, "edit" => true, "del" => false, "view" => false, "excel" => true);
$view = new buildView("vehiculos", $params, "vehiculos");
?>
