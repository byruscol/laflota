<?php
require_once "../../commonVehiculosGrid.php";
$params["sortname"] = "placa";
$params["CRUD"] = array("add" => true, "edit" => true, "del" => true, "view" => false, "excel" => true);
$params["actions"] = array(
                            array("type" => "onSelectRow"
                                      ,"function" => 'function(id) {
                                                        if(id != null) {
                                                                postDataObj = jQuery("#extensiones").jqGrid("getGridParam","postData");
                                                                postDataObj["filter"] = id;
                                                                postDataObj["parent"] = "'.$_GET["view"].'";
                                                                jQuery("#extensiones").jqGrid("setGridParam",{postData: postDataObj})
                                                                                .trigger("reloadGrid");
                                                        }
                                                    }'
                                    )
                        );
$view = new buildView("vehiculos", $params, "vehiculos");
?>
