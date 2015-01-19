<?php
require_once "../../commonVehiculosGrid.php";
$params["postData"]["method"] = "getVehiculosCliente";
$params["sortname"] = "date_entered";
$params["CRUD"] = array("add" => true, "edit" => true, "del" => true, "view" => true, "excel" => true);
$view = new buildView("vehiculos", $params, "vehiculos");
?>
