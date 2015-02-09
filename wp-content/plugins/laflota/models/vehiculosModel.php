<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class vehiculos extends DBManagerModel{
   
    public function getList($params = array()){
        $entity = $this->entity();
        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT `vehiculoId`,
                        `clienteId`,
                        `placa`,
                        `tipoMotorId`,
                        `marcaMotorId`,
                        `marcaVehiculoId`,
                        `des_modelo`
                    FROM ".$entity["tableName"]." i";
        
        if(array_key_exists('where', $params)){
            if (is_array( $params["where"]->rules )){
                $countRules = count($params["where"]->rules);
                for($i = 0; $i < $countRules; $i++){
                    switch($params["where"]->rules[$i]->field ){
                        case "cliente": $params["where"]->rules[$i]->field = "clienteId"; break;
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
                        `clienteId` cliente,
                        `placa`,
                        `tipoMotorId`,
                        `marcaMotorId`,
                        `marcaVehiculoId`,
                        `des_modelo`
                    FROM ".$entity["tableName"]." i "
                . "WHERE  `clienteId` = " . $params["filter"];
        
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
    
    private function validateVehicles(){
        $msj = "";
        $query = "SELECT t.placa FROM ". $this->pluginPrefix ."extensionesUploadTmp t
                    WHERE NOT EXISTS(
                                            SELECT 1 FROM ". $this->pluginPrefix ."vehiculos v
                                            WHERE   v.placa = t.placa
                                    );";

        $result = $this->conn->get_col($query);
        
        
        foreach ($result as $num_linea => $linea){
            $msj = $linea.": ".$this->resource->getWord("vehiculoNoExiste")."\n";
        }
        return $msj;
    }
    
    public function addMasterData($param){
        
        switch($param){
            case "extensiones":
                    $query = "INSERT INTO `laflota`.`". $this->pluginPrefix ."extensiones`
                                    (`vehiculoId`,
                                    `kilometraje`,
                                    `date_entered`,
                                    `created_by`)
                                SELECT vehiculoId, t.kilometraje, t.date_entered, t.created_by
                                FROM ". $this->pluginPrefix ."extensionesUploadTmp t
                                        JOIN ". $this->pluginPrefix ."vehiculos v ON v.placa = t.placa"; break;
        }
        $this->conn->query($query);
    }
    
    
    public function load(){
        $entity = $this->entity();
        $target_path = $this->pluginPath."/uploadedFiles/";
        $fileName = $_FILES['file']['name'];
        $nameParts = explode(".", $fileName);
        $ext = end($nameParts);
        $nameArray = array_pop($nameParts);
        $fileName = implode("_",$nameParts);
        $fileName = str_replace(array("'",".",",","*","@","?","!"), "_",$fileName);
        	
        if($_FILES["file"]["type"]=="text/csv" || $_FILES["file"]["type"]=="application/octet-stream" || $_FILES["file"]["type"]=="application/vnd.ms-excel"){
            $file = $target_path.$fileName.".".$ext;
            if(move_uploaded_file($_FILES['file']['tmp_name'], $file)) {
                $arrayFile = file($file);
                $validate = $this->validatorFile($arrayFile, array( array("column" => "PLACA", "type" => "string", "required" => true)
                                                                    ,array("column" => "KILOMETRAJE", "type" => "number", "required" => true)
                                                                )
                                                );
                if($validate["result"]){
                    $table = $this->pluginPrefix."extensionesUploadTmp";
                    if($this->truncateTable($table)){
                        $lines = 0;
                        $record = array();
                        
                        $date_entered = date("Y-m-d H:i:s");
                        $created_by = $this->currentUser->ID;
                        foreach ($validate["arrayResult"] as $num_linea => $linea){
                            $record[] = "'".trim(implode("','",$linea))."','".$date_entered."','".$created_by."'";
                        }
                        $dataInsert = '('.implode("),(",$record).')';
                        $query = "INSERT INTO ".$table."
                                    (`placa`,
                                    `kilometraje`,
                                    `date_entered`,
                                    `created_by`) VALUES " .$dataInsert;
                        $lines = $this->conn->query($query);
                        if($lines == count($validate["arrayResult"])){
                            $msj = $this->validateVehicles();
                            if(empty($msj)){
                                $this->addMasterData("extensiones");
                                echo $this->resource->getWord("fileUploaded");
                            }
                            else
                                echo $msj;

                        }
                    }
                }
                else{
                    echo $validate["msj"];
                }
                unlink($file);
            } 
            else
                echo $this->resource->getWord("fileUploadError");
        }
    }
    public function setMd5(){
        return md5($_POST["clienteId"].$_POST["placa"].$_POST["tipoMotorId"].$_POST["marcaMotorId"].$_POST["marcaVehiculoId"].$_POST["des_modelo"]);
    }    
    
    public function add(){
        $md5 = $this->setMd5(); 
        $this->addRecord($this->entity(), $_POST, array("md5" => $md5,"date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
    }
    public function edit(){
        $md5 = $this->setMd5(); 
        $this->updateRecord($this->entity(), $_POST, array("vehiculoId" => $_POST["vehiculoId"]), null, array("md5" => $md5));
    }
    public function del(){
        $this->eliminateRecord($this->entity(), array("vehiculoId" => $_POST["id"]));
    }

    public function detail($params = array()){}
    
    public function entity($CRUD = array())
    {
        $data = array(
                    "tableName" => $this->pluginPrefix."vehiculos"
                    ,"entityConfig" => $CRUD
                    ,"atributes" => array(
                        "vehiculoId" => array("label" => "id" ,"type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true, "toolTip" => array("type" => "cell", "cell" => 2) )
                        ,"clienteId" => array("label" => "cliente" ,"type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."clientes", "id" => "clienteId", "text" => "propietario"))
                        ,"placa" => array("label" => "placa" ,"type" => "varchar", "required" => true)
                        ,"tipoMotorId" => array("label" => "tipoMotorId" ,"type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."tipoMotor", "id" => "tipoMotorId", "text" => "tipoMotor"))
                        ,"marcaMotorId" => array("label" => "marcaMotor" ,"type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."marcaMotores", "id" => "marcaMotorId", "text" => "marcaMotor"))
                        ,"marcaVehiculoId" => array("label" => "marcaVehiculo" ,"type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."marcaVehiculos", "id" => "marcaVehiculoId", "text" => "marcaVehiculo"))
                        ,"des_modelo" => array("label" => "modelo" ,"type" => "varchar", "required" => true)
                        ,"parentId" => array("type" => "int","required" => false, "hidden" => true, "isTableCol" => false)
                        ,"parentRelationShip" => array("type" => "varchar","required" => false, "hidden" => true, "isTableCol" => false)
                    )
                );  
        return $data;
    }
}
?>