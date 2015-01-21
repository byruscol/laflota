<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class muestras extends DBManagerModel{
   
    public function getList($params = array()){
        $entity = $this->entity();
        $start = $params["limit"] * $params["page"] - $params["limit"];
            $query = "SELECT `muestraId`,
                            `vehiculoId`,
                            `nromuestra`,
                            `estadoMuestraId`,
                            `tipoMuestraId`,
                            `componentenumero`,
                            `ftoma`,
                            `kanterior`,
                            `klactual`,
                            `marcaMotorId`,
                            `tipoMotorId`,
                            `marcaVehiculoId`,
                            `des_modelo`,
                            `vis100`,
                            `maxvis`,
                            `vis40`,
                            `fe`,
                            `maxfe`,
                            `cr`,
                            `maxcr`,
                            `pb`,
                            `maxpb`,
                            `al`,
                            `maxal`,
                            `cu`,
                            `maxcu`,
                            `si`,
                            `maxsi`,
                            `hollin`,
                            `maxhollin`,
                            `tbn`,
                            `maxtbn`,
                            `agua`,
                            `maxagua`,
                            `combustible`,
                            `maxcombustible`,
                            `escritica`,
                            `observaciones`
                    FROM ".$entity["tableName"]." i";
        
        if(array_key_exists('where', $params)){
            if (is_array( $params["where"]->rules )){
                $countRules = count($params["where"]->rules);
                for($i = 0; $i < $countRules; $i++){
                    switch($params["where"]->rules[$i]->field ){
                        case "vehiculo": $params["where"]->rules[$i]->field = "vehiculoId"; break;
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
                        `tipoMotorId`
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
        $query = "SELECT t.placa FROM laflota.wp_lf_extensionesUploadTmp t
                    WHERE NOT EXISTS(
                                            SELECT 1 FROM laflota.wp_lf_vehiculos v
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
        
        $tableTmpCols = array("placa", "ciudad", "tipousuario", "cedulaNit"
                                , "propietario", "comercial", "tipomotor"
                                , "email", "confirmacion", "md5", "date_entered", "created_by");
        	
        if($_FILES["file"]["type"]=="text/csv"){
            $file = $target_path.$fileName.".".$ext;
            if(move_uploaded_file($_FILES['file']['tmp_name'], $file)) {
                $arrayFile = file($file);
                $validate = $this->validatorFile($arrayFile, array("PLACA","KILOMETRAJE"));
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
        return md5($_POST["clienteId"].$_POST["placa"].$_POST["tipoMotorId"]);
    }    
    
    public function add(){
        $md5 = $this->setMd5(); 
        $this->addRecord($this->entity(), $_POST, array("md5" => $md5,"date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
    }
    public function edit(){
        $md5 = $this->setMd5(); 
        $this->updateRecord($this->entity(), $_POST, array("muestraId" => $_POST["muestraId"]), null, array("md5" => $md5));
    }
    public function del(){
        $this->eliminateRecord($this->entity(), array("muestraId" => $_POST["id"]));
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
                    "tableName" => $this->pluginPrefix."muestras"
                    ,"entityConfig" => $CRUD
                    ,"atributes" => array(
                        "muestraId" => array("type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true, "toolTip" => array("type" => "cell", "cell" => 2) )
                        ,"vehiculo" => array("type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."vehiculos", "id" => "vehiculoId", "text" => "placa"))
                        ,"nromuestra" => array("type" => "varchar", "required" => true)
                        ,"estadoMuestraId" => array("type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."estadoMuestras", "id" => "estadoMuestraId", "text" => "estadoMuestra"))
                        ,"tipoMuestraId" => array("type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."tipoMuestras", "id" => "tipoMuestraId", "text" => "tipoMuestra"))
                        ,"componentenumero" => array("type" => "int", "required" => true)
                        ,"ftoma" => array("type" => "date", "required" => true)
                        ,"kanterior" => array("type" => "int", "required" => true)
                        ,"klactual" => array("type" => "int", "required" => true)
                        ,"vis100" => array("type" => "number", "required" => true)
                        ,"maxvis" => array("type" => "number", "required" => true)
                        ,"vis40" => array("type" => "number", "required" => true)
                        ,"fe" => array("type" => "number", "required" => true)
                        ,"maxfe" => array("type" => "number", "required" => true)
                        ,"cr" => array("type" => "number", "required" => true)
                        ,"maxcr" => array("type" => "number", "required" => true)
                        ,"pb" => array("type" => "number", "required" => true)
                        ,"maxpb" => array("type" => "number", "required" => true)
                        ,"al" => array("type" => "number", "required" => true)
                        ,"maxal" => array("type" => "number", "required" => true)
                        ,"cu" => array("type" => "number", "required" => true)
                        ,"maxcu" => array("type" => "number", "required" => true)
                        ,"si" => array("type" => "number", "required" => true)
                        ,"maxsi" => array("type" => "number", "required" => true)
                        ,"hollin" => array("type" => "number", "required" => true)
                        ,"maxhollin" => array("type" => "number", "required" => false)
                        ,"tbn" => array("type" => "number", "required" => true)
                        ,"maxtbn" => array("type" => "number", "required" => false)
                        ,"agua" => array("type" => "number", "required" => false)
                        ,"maxagua" => array("type" => "number", "required" => false)
                        ,"combustible" => array("type" => "number", "required" => true)
                        ,"maxcombustible" => array("type" => "number", "required" => false)
                        ,"escritica" => array("type" => "enum", "required" => false)
                        ,"observaciones" => array("type" => "text", "required" => false)
                    )
                );  
        return $data;
    }
}
?>