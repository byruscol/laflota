<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class miFlota extends DBManagerModel{
   
    public function getList($params = array()){
        $entity = $this->entity();
        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT `vehiculoId`,
                        `placa`,
                        `tipoMotorId`,
                        `marcaMotorId`,
                        `marcaVehiculoId`,
                        `des_modelo`
                    FROM ".$entity["tableName"]." i"
                . " WHERE clienteId = ".$params["filter"];
        
        if(array_key_exists('where', $params)){
            if (is_array( $params["where"]->rules )){
                $countRules = count($params["where"]->rules);
                for($i = 0; $i < $countRules; $i++){
                    switch($params["where"]->rules[$i]->field ){
                        case "cliente": $params["where"]->rules[$i]->field = "clienteId"; break;
                    }
                }
            }
            
           $query .= " AND (". $this->buildWhere($params["where"]) .")";
        }
        
        return $this->getDataGrid($query, $start, $params["limit"] , $params["sidx"], $params["sord"]);
    }
    public function geMyFleet($params) {
        $params["filter"] = $this->currentUser->ID;
        return $this->getList($params);
    }
    
    public function report(){
        print_r($_GET);
    }
    
    public function add(){}
    public function edit(){}
    public function del(){}

    public function detail($params = array()){}
    
    public function entity($CRUD = array())
    {
        $data = array(
                    "tableName" => $this->pluginPrefix."vehiculos"
                    ,"entityConfig" => $CRUD
                    ,"atributes" => array(
                        "vehiculoId" => array("label" => "id", "hidden" => true ,"type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true, "toolTip" => array("type" => "cell", "cell" => 2) )
                        ,"placa" => array("label" => "placa" ,"type" => "varchar", "required" => true)
                        ,"tipoMotorId" => array("label" => "tipoMotorId", "hidden" => true ,"type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."tipoMotor", "id" => "tipoMotorId", "text" => "tipoMotor"))
                        ,"marcaMotorId" => array("label" => "marcaMotor", "hidden" => true ,"type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."marcaMotores", "id" => "marcaMotorId", "text" => "marcaMotor"))
                        ,"marcaVehiculoId" => array("label" => "marcaVehiculo", "hidden" => true ,"type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."marcaVehiculos", "id" => "marcaVehiculoId", "text" => "marcaVehiculo"))
                        ,"des_modelo" => array("label" => "modelo", "hidden" => true ,"type" => "varchar", "required" => true)
                        ,"motor" => array("type" => "int","required" => false, "readOnly" => true, "isTableCol" => false,"formatter" => "@function(cellvalue, options, rowObject){" 
                                            . "return '<a title=\"'+cellvalue+'\" href=\"".$this->pluginURL."downloadReport.php?controller=miFlota&type=motor&id='+rowObject[0]+'\" target=\"_blank\"><img src=\"".$this->pluginURL."images/informes_motor_btn.png\"/> </a>';}@")
                        ,"caja" => array("type" => "int","required" => false, "readOnly" => true, "isTableCol" => false,"formatter" => "@function(cellvalue, options, rowObject){" 
                                            . "return '<a title=\"'+cellvalue+'\" href=\"".$this->pluginURL."downloadReport.php?controller=miFlota&type=caja&id='+rowObject[0]+'\" target=\"_blank\"><img src=\"".$this->pluginURL."images/informes_caja_btn.png\"/> </a>';}@")
                        ,"diferencial" => array("type" => "int","required" => false, "readOnly" => true, "isTableCol" => false,"formatter" => "@function(cellvalue, options, rowObject){" 
                                            . "return '<a title=\"'+cellvalue+'\" href=\"".$this->pluginURL."downloadReport.php?controller=miFlota&type=diferencial&id='+rowObject[0]+'\" target=\"_blank\"><img src=\"".$this->pluginURL."images/informes_diferencial_btn.png\"/> </a>';}@")
                    )
                );  
        return $data;
    }
}
?>