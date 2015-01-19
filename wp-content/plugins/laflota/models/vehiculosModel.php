<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class clientes extends DBManagerModel{
   
    public function getList($params = array()){
        $entity = $this->entity();
        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT `vehiculoId`,
                        `clienteId`,
                        `placa`,
                        `tipoMotorId`
                    FROM ".$entity["tableName"]." i";
        
        if(array_key_exists('where', $params)){
            if (is_array( $params["where"]->rules )){
                $countRules = count($params["where"]->rules);
                for($i = 0; $i < $countRules; $i++){
                    switch($params["where"]->rules[$i]->field ){
                        case "created_by": $params["where"]->rules[$i]->field = "display_name"; break;
                        case "edad": $params["where"]->rules[$i]->field = "DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), fechaNacimiento)), '%Y')+0"; break;
                    }
                }
            }
            
           $query .= " WHERE (". $this->buildWhere($params["where"]) .")";
        }
        
        return $this->getDataGrid($query, $start, $params["limit"] , $params["sidx"], $params["sord"]);
    }

    public function getVehiculosCliente($params = array()){
        $entity = $this->entity();
        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT `vehiculoId`,
                        `clienteId`,
                        `placa`,
                        `tipoMotorId`
                    FROM ".$entity["tableName"]." i"
                . "WHERE  `clienteId` = " . $params["filter"];
        
        if(array_key_exists('where', $params)){
            if (is_array( $params["where"]->rules )){
                $countRules = count($params["where"]->rules);
                for($i = 0; $i < $countRules; $i++){
                    switch($params["where"]->rules[$i]->field ){
                        case "created_by": $params["where"]->rules[$i]->field = "display_name"; break;
                        case "edad": $params["where"]->rules[$i]->field = "DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), fechaNacimiento)), '%Y')+0"; break;
                    }
                }
            }
            
           $query .= " AND (". $this->buildWhere($params["where"]) .")";
        }
        
        return $this->getDataGrid($query, $start, $params["limit"] , $params["sidx"], $params["sord"]);
    }
    
    public function buildMd5($linea){
        return md5($linea[1].$linea[2].$linea[3].$linea[4].$linea[5].$linea[7].$linea[8]);
    }    
    
    public function add(){
        $md5 = $this->setMd5(); 
        $this->addRecord($this->entity(), $_POST, array("md5" => $md5,"date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
    }
    public function edit(){
        $md5 = $this->setMd5(); 
        $this->updateRecord($this->entity(), $_POST, array("clienteId" => $_POST["clienteId"]), null, array("md5" => $md5));
    }
    public function del(){
        $this->eliminateRecord($this->entity(), array("clienteId" => $_POST["id"]));
    }

    public function detail($params = array()){
        $entity = $this->entity();
        $query = "  SELECT `clienteId`,
                            `ciudad` ciudadId,
                            `tipoUsuario` tipousuarioId,
                            `cedulaNit`,
                            `propietario`,
                            `comercial` comercialId,
                            `email`
                    FROM ".$entity["tableName"]." c
                            JOIN ".$this->pluginPrefix."ciudades i ON i.ciudadId = c.ciudadId
                            JOIN ".$this->pluginPrefix."tipousuario u ON u.tipousuarioId = c.tipousuarioId
                            JOIN ".$this->pluginPrefix."comerciales co ON co.comercialId = c.comercialId
                    WHERE c.`clienteId` = " . $params["filter"];
        $this->queryType = "row";
        return $this->getDataGrid($query);
    }
    
    public function entity($CRUD = array())
    {
        $data = array(
                    "tableName" => $this->pluginPrefix."vehiculos"
                    ,"entityConfig" => $CRUD
                    ,"atributes" => array(
                        "vehiculoId" => array("type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true, "toolTip" => array("type" => "cell", "cell" => 2) )
                        ,"clienteId" => array("type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."clientes", "id" => "clienteId", "text" => "cliente"))
                        ,"placa" => array("type" => "varchar", "required" => true)
                        ,"tipoMotorId" => array("type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."tipoMotor", "id" => "tipoMotorId", "text" => "tipoMotor"))
                        ,"parentId" => array("type" => "int","required" => false, "hidden" => true, "isTableCol" => false)
                        ,"parentRelationShip" => array("type" => "varchar","required" => false, "hidden" => true, "isTableCol" => false)
                    )
                );  
        return $data;
    }
}
?>