<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class clientes extends DBManagerModel{
   
    public function getList($params = array()){
        $entity = $this->entity();
        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT `clienteId`,
                        `ciudadId`,
                        `tipousuarioId`,
                        `cedulaNit`,
                        `propietario`,
                        `comercialId`,
                        `email`,
                        `confirmacion`
                        `md5`
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

    public function getCities($params){
        $query = "SELECT ciudadId, ciudad 
                  FROM ".$this->pluginPrefix."ciudades c
                  WHERE `departamentoId` = ". $params["filter"];
        
        $cities = $this->getDataGrid($query, NULL, NULL , "ciudad", "ASC");
        $responce = array("metaData" => array("key" => "ciudadId", "value" => "ciudad"), "data" => $cities["data"]);
        return $cities;
    }
    
    public function validatorFile($arrayFile){
        $msj = "";
        $arraHeader = array("PLACA","CIUDAD","TIPO","CEDULA / NIT","PROPIETARIO","COMERCIAL SIG","TIPO DE MOTOR","EMAIL","CONFIRMACION DE ENVIO");
        $countheader = count($arraHeader);
        $arrayResult = array();
        foreach ($arrayFile as $num_linea => $linea) {
            $cols = explode(";", $linea);
            $numCols = count($cols);
            
            if($numCols != $countheader )
            {
                $msj .= "Error en la linea " . $num_linea.": deben ser ". $countheader ." columnas";
            }
            
            if($num_linea == 0){
                for($i = 0; $i < $countheader; $i++){
                    if(strtolower($arraHeader[$i]) != strtolower(trim($cols[$i]))){
                        $msj .= "Error en la linea " . $num_linea.": ".$arraHeader[$i] ." diferente a ". $cols[$i];
                    }
                }
            }
            else{
                foreach ($cols as $num_col => $col){
                    $cols[$num_col] = trim($col);
                }
                $arrayResult[] = $cols;
            }
        }
        
        if(empty($msj)){
            $result = true;
        }
        else {
            $result = false; 
            $arrayResult = array();
        }
        
        return array("result" => $result, "msj" => $msj, "arrayResult" => $arrayResult, "cantCols" => $countheader);
    }
    
    public function existRecord($params) {
        
        $query = "SELECT ". $params["col"] 
                . "FROM ". $params["table"]
                ." WHERE ". $params["key"] ." = '". $params["value"]."'";
        
        $id = $this->get($query, "var");
        
        return $id;
    }
    
    public function buildMd5($linea){
        return md5($linea[1].$linea[2].$linea[3].$linea[4].$linea[5].$linea[7].$linea[8]);
    }    
    
    public function addMasterData($param){
        
        switch($param){
            case "clientes":
                    $query = "INSERT INTO `". $this->pluginPrefix ."ciudades`(`ciudad`)
                                SELECT u.ciudad 
                                FROM ". $this->pluginPrefix ."clientesUploadTmp u
                                      LEFT JOIN ". $this->pluginPrefix ."ciudades c ON u.ciudad = c.ciudad
                                WHERE c.ciudadId IS NULL
                                GROUP BY u.ciudad"; break;
            case "tipousuario":
                    $query = "INSERT INTO `". $this->pluginPrefix ."tipousuario`(`tipoUsuario`)
                                SELECT u.tipousuario 
                                FROM ". $this->pluginPrefix ."clientesUploadTmp u
                                          LEFT JOIN ". $this->pluginPrefix ."tipousuario t ON u.tipousuario = t.tipoUsuario
                                WHERE t.tipousuarioId IS NULL
                                GROUP BY u.tipousuario"; break;   
            case "comercial":
                    $query = "INSERT INTO `". $this->pluginPrefix ."comerciales`(`comercial`)
                                SELECT u.comercial 
                                FROM ". $this->pluginPrefix ."clientesUploadTmp u
                                          LEFT JOIN ". $this->pluginPrefix ."comerciales c ON c.comercial = u.comercial
                                WHERE c.comercialId IS NULL
                                GROUP BY u.comercial"; break; 
            case "clientesInsert":
                    $query = "INSERT INTO `". $this->pluginPrefix ."clientes`
                                (`ciudadId`,
                                `tipousuarioId`,
                                `cedulaNit`,
                                `propietario`,
                                `comercialId`,
                                `email`,
                                `confirmacion`,
                                `date_entered`,
                                `created_by`,
                                `md5`)
                                SELECT ciudadId,tipousuarioId,cedulaNit,propietario,comercialId,email,confirmacion,date_entered,created_by,md5
                                FROM ". $this->pluginPrefix ."clientesUploadTmp u
                                         JOIN(
                                                        SELECT MAX(ut.clientesUploadId) clientesUploadId
                                                        FROM ". $this->pluginPrefix ."clientesUploadTmp ut
                                                                        LEFT JOIN ". $this->pluginPrefix ."clientes c ON c.cedulaNit = ut.cedulaNit
                                                        WHERE c.clienteId IS NULL
                                                        GROUP BY ut.cedulaNit
                                        )f ON f.clientesUploadId = u.clientesUploadId
                                        JOIN ". $this->pluginPrefix ."ciudades ci ON ci.ciudad = u.ciudad
                                        JOIN ". $this->pluginPrefix ."tipousuario t ON t.tipoUsuario = u.tipousuario
                                        JOIN ". $this->pluginPrefix ."comerciales co ON co.comercial = u.comercial";break;
        
            case "clientesUpdate":
                    $query = "UPDATE ". $this->pluginPrefix ."clientes c
                                JOIN (
                                                SELECT cl.clienteId,ci.ciudadId,t.tipousuarioId,u.cedulaNit,u.propietario,co.comercialId,u.email,u.confirmacion,u.date_entered,u.created_by,u.md5
                                                FROM ". $this->pluginPrefix ."clientesUploadTmp u
                                                         JOIN(
                                                                        SELECT MAX(clientesUploadId) clientesUploadId
                                                                        FROM ". $this->pluginPrefix ."clientesUploadTmp ut
                                                                                        JOIN ". $this->pluginPrefix ."clientes c ON c.cedulaNit = ut.cedulaNit 
                                                                        GROUP BY ut.cedulaNit
                                                        )f ON f.clientesUploadId = u.clientesUploadId
                                                        JOIN ". $this->pluginPrefix ."clientes cl ON cl.cedulaNit = u.cedulaNit AND cl.md5 != u.md5
                                                        JOIN ". $this->pluginPrefix ."ciudades ci ON ci.ciudad = u.ciudad
                                                        JOIN ". $this->pluginPrefix ."tipousuario t ON t.tipoUsuario = u.tipousuario
                                                        JOIN ". $this->pluginPrefix ."comerciales co ON co.comercial = u.comercial
                                        ) d ON d.clienteId = c.clienteId
                                SET
                                c.`ciudadId` = d.ciudadId,
                                c.`tipousuarioId` = d.tipousuarioId,
                                c.`cedulaNit` = d.cedulaNit,
                                c.`propietario` = d.propietario,
                                c.`comercialId` = d.comercialId,
                                c.`email` = d.email,
                                c.`confirmacion` = d.confirmacion,
                                c.`date_entered` = d.date_entered,
                                c.`created_by` = d.created_by,
                                c.`md5` = d.md5";break;
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
                $validate = $this->validatorFile($arrayFile);
                if($validate["result"]){
                    $table = $this->pluginPrefix."clientesUploadTmp";
                    if($this->truncateTable($table)){
                        $lines = 0;
                        $record = array();
                        
                        $date_entered = date("Y-m-d H:i:s");
                        $created_by = $this->currentUser->ID;
                        foreach ($validate["arrayResult"] as $num_linea => $linea){
                            $md5 = $this->buildMd5($linea);
                            $record[] = "'".trim(implode("','",$linea))."','".$md5."','".$date_entered."','".$created_by."'";
                        }
                        $dataInsert = '('.implode("),(",$record).')';
                        $query = "INSERT INTO ".$table."
                                    (`placa`,
                                    `ciudad`,
                                    `tipousuario`,
                                    `cedulaNit`,
                                    `propietario`,
                                    `comercial`,
                                    `tipomotor`,
                                    `email`,
                                    `confirmacion`,
                                    `md5`,
                                    `date_entered`,
                                    `created_by`) VALUES " .$dataInsert;
                        $lines = $this->conn->query($query);
                        if($lines == count($validate["arrayResult"])){
                            $this->addMasterData("clientes");
                            $this->addMasterData("tipousuario");
                            $this->addMasterData("comercial");
                            $this->addMasterData("clientesInsert");
                            $this->addMasterData("clientesUpdate");
                        }
                    }
                }
                else{
                    echo $validate["msj"];
                }
                unlink($file);
            } 
            else
                echo "There was an error uploading the file, please try again!";
        }
    }
    
    public function getIntegrantesFamiliares($params = array()){
        require "familiaresModel.php";
        $familiares = new familiares();
        return $familiares->getIntegrantesFamiliares($params);
    }
    
    public function setMd5(){
        $entity = $this->entity();
        $ciudad = $this->getRelationshipDescriptionData($entity["ciudadId"]["references"], $_POST["ciudadId"]);
        $tipo = $this->getRelationshipDescriptionData($entity["tipousuarioId"]["references"], $_POST["tipousuarioId"]);
        $comercial = $this->getRelationshipDescriptionData($entity["comercialId"]["references"], $_POST["comercialId"]);
        $linea = array("", $ciudad, $tipo, $_POST["cedulaNit"], $_POST["propietario"],$comercial,"", $_POST["email"], $_POST["confirmacion"]);
        return $this->buildMd5($linea); 
    }
    
    public function add(){
        $md5 = $this->setMd5(); 
        $this->addRecord($this->entity(), $_POST, array("md5" => $md5,"date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
    }
    public function edit(){
        $this->updateRecord($this->entity(), $_POST, array("integranteId" => $_POST["integranteId"])/*, array("columnValidateEdit" => "assigned_user_id")*/);
    }
    public function del(){
        $this->delRecord($this->entity(), array("integranteId" => $_POST["id"])/*, array("columnValidateEdit" => "assigned_user_id")*/);
    }

    public function detail($params = array()){
        $entity = $this->entity();
        $query = "  SELECT `integranteId`, tipoIdentificacion, `identificacion`, activo, `nombre`, `apellido`
                                    , `genero`, `rhId`, `fechaNacimiento`, DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), fechaNacimiento)), '%Y')+0 edad
                                    , telefono, celular
                                    ,  email, emailPersonal, `direccion`, d.departamento
                                    ,  c.ciudad ciudadRecidenciaId, `localidad`
                                    , `barrio` 
                    FROM ".$entity["tableName"]." i
                       JOIN ".$this->pluginPrefix."ciudades c ON c.ciudadId = i.ciudadRecidenciaId
                       JOIN ".$this->pluginPrefix."departamentos d ON d.departamentoId = c.departamentoId
                    WHERE i.`integranteId` = " . $params["filter"];
        $this->queryType = "row";
        return $this->getDataGrid($query);
    }
    
    public function entity($CRUD = array())
    {
        $data = array(
                    "tableName" => $this->pluginPrefix."clientes"
                    //,"columnValidateEdit" => "assigned_user_id"
                    ,"entityConfig" => $CRUD
                    ,"atributes" => array(
                        "clienteId" => array("type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true, "toolTip" => array("type" => "cell", "cell" => 2) )
                        ,"ciudadId" => array("type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."ciudades", "id" => "ciudadId", "text" => "ciudad"))
                        ,"tipousuarioId" => array("type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."tipousuario", "id" => "tipousuarioId", "text" => "tipoUsuario"))
                        ,"cedulaNit" => array("type" => "varchar", "required" => true)
                        ,"propietario" => array("type" => "varchar", "required" => true)
                        ,"comercialId" => array("type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."comerciales", "id" => "comercialId", "text" => "comercial"))
                        ,"email" => array("type" => "email", "required" => true)
                        ,"confirmacion" => array("type" => "enum", "required" => true)
                    )
                );
        return $data;
    }
}
?>