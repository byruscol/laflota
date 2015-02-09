<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class clientes extends DBManagerModel{
   
    public function getList($params = array()){
        $entity = $this->entity();
        
        //Migracion de usuarios.
       /* $query = "SELECT `identificacion`,
                        `propietario`
                    FROM cliente c
                    where not exists(
                        SELECT 1 FROM  wp_users u
                        where c.identificacion = u.user_login
                    )";  
        
        $results = $this->conn->get_results($query);
        
       foreach ($results as $dataObject){
           $userdata = array(
                'user_login'  =>  $dataObject->identificacion,
                'user_pass'   =>  $dataObject->identificacion,
                'user_nicename' => $dataObject->propietario,
                'display_name' => $dataObject->propietario
            ); 
           wp_insert_user($userdata);
           $userdata = array();
       }
        */
        
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
    
    public function existRecord($params) {
        
        $query = "SELECT ". $params["col"] 
                . "FROM ". $params["table"]
                ." WHERE ". $params["key"] ." = '". $params["value"]."'";
        
        $id = $this->get($query, "var");
        
        return $id;
    }
    
    public function buildMd5($linea){
        return md5($linea[1].$linea[2].$linea[3].$linea[4].$linea[5].$linea[10].$linea[11]);
    }    
    
    public function addMasterData($param){
        $entity = $this->entity();
        switch($param){
            case "clientes":
                    $query = "INSERT INTO `". $this->pluginPrefix ."ciudades`(`ciudad`)
                                SELECT u.ciudad 
                                FROM ". $this->pluginPrefix ."clientesUploadTmp u
                                      LEFT JOIN ". $this->pluginPrefix ."ciudades c ON u.ciudad = c.ciudad
                                WHERE c.ciudadId IS NULL
                                GROUP BY u.ciudad"; break;
            case "tipomotor":
                    $query = "INSERT INTO `". $this->pluginPrefix ."tipoMotor`(`tipoMotor`)
                                SELECT u.tipomotor 
                                FROM ". $this->pluginPrefix ."clientesUploadTmp u
                                          LEFT JOIN ". $this->pluginPrefix ."tipoMotor t ON u.tipomotor = t.tipoMotor
                                WHERE t.tipoMotorId IS NULL
                                GROUP BY u.tipomotor"; break;
            case "marcamotor":      
                    $query = "INSERT INTO `". $this->pluginPrefix ."marcaMotores`(`marcaMotor`)
                                SELECT u.marcamotor 
                                FROM ". $this->pluginPrefix ."clientesUploadTmp u
                                          LEFT JOIN ". $this->pluginPrefix ."marcaMotores t ON u.marcamotor = t.marcaMotor
                                WHERE t.marcaMotorId IS NULL
                                GROUP BY u.marcamotor"; break;
            case "marcavehiculo":
                    $query = "INSERT INTO `". $this->pluginPrefix ."marcaVehiculos`(`marcaVehiculo`)
                                SELECT u.marcavehiculo 
                                FROM ". $this->pluginPrefix ."clientesUploadTmp u
                                          LEFT JOIN ". $this->pluginPrefix ."marcaVehiculos t ON u.marcavehiculo = t.marcaVehiculo
                                WHERE t.marcaVehiculoId IS NULL
                                GROUP BY u.marcavehiculo"; break;
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
                
                    $query = "SELECT cedulaNit,propietario
                                FROM ". $this->pluginPrefix ."clientesUploadTmp u
                                         JOIN(
                                                        SELECT MAX(ut.clientesUploadId) clientesUploadId
                                                        FROM ". $this->pluginPrefix ."clientesUploadTmp ut
                                                                        LEFT JOIN ". $this->pluginPrefix ."clientes c ON c.cedulaNit = ut.cedulaNit
                                                        WHERE c.clienteId IS NULL
                                                        GROUP BY ut.cedulaNit
                                        )f ON f.clientesUploadId = u.clientesUploadId";  
        
                    $results = $this->conn->get_results($query);

                   foreach ($results as $dataObject){
                       $userdata = array(
                            'user_login'  =>  $dataObject->cedulaNit,
                            'user_pass'   =>  $dataObject->cedulaNit,
                            'user_nicename' => $dataObject->propietario,
                            'display_name' => $dataObject->propietario
                        ); 
                       wp_insert_user($userdata);
                       $userdata = array();
                   }
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
                    $this->conn->query($query);
                    $query = "INSERT INTO `". $this->pluginPrefix ."clientesUsuarios`(clienteId, ID)" 
                            . "SELECT clienteId,ID "
                            . " FROM ".$this->wpPrefix ."users u"
                            . "     JOIN ".$entity["tableName"]."clientes c on c.cedulaNit = u.user_login";
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
            case "vehiculosInsert": 
                $query = "INSERT INTO `laflota`.`wp_lf_vehiculos`
                            (`clienteId`,
                            `placa`,
                            `tipoMotorId`,
                            `marcaMotorId`,
                            `marcaVehiculoId`,
                            `des_modelo`,
                            `md5`,
                            `date_entered`,
                            `created_by`)
                            SELECT clienteId, u.placa, tipoMotorId, marcaMotorId, marcaVehiculoId, des_modelo, MD5(CONCAT(clienteId, u.placa, tipoMotorId, marcaMotorId, marcaVehiculoId, des_modelo)),u.date_entered,u.created_by
                            FROM ". $this->pluginPrefix ."clientesUploadTmp u
                                            JOIN (
                                                    SELECT MAX(ut.clientesUploadId) clientesUploadId
                                                    FROM ". $this->pluginPrefix ."clientesUploadTmp ut
                                                                                    LEFT JOIN ". $this->pluginPrefix ."vehiculos v ON v.placa = ut.placa
                                                    WHERE v.vehiculoId IS NULL
                                                    GROUP BY ut.placa
                                            ) d ON d.clientesUploadId = u.clientesUploadId
                                            JOIN ". $this->pluginPrefix ."clientes c ON c.cedulaNit = u.cedulaNit
                                            JOIN ". $this->pluginPrefix ."tipoMotor t ON t.tipoMotor = u.tipomotor"
                    . "                     JOIN ". $this->pluginPrefix ."marcaMotores mm ON mm.marcaMotor = u.marcamotor"
                    . "                     JOIN ". $this->pluginPrefix ."marcaVehiculos mv ON mv.marcaVehiculo = u.marcavehiculo";break;
        
            case "vehiculosUpdate":
                 $query = "UPDATE ". $this->pluginPrefix ."vehiculos veh
                                JOIN (
                                    SELECT v.vehiculoId, c.clienteId, u.placa, t.tipoMotorId, marcaMotorId, marcaVehiculoId, des_modelo, MD5(CONCAT(clienteId, u.placa, tipoMotorId, marcaMotorId, marcaVehiculoId, des_modelo)) COLLATE utf8_general_ci md5, u.date_entered, u.created_by
                                    FROM ". $this->pluginPrefix ."clientesUploadTmp u
                                                    JOIN (
                                                                    SELECT MAX(ut.clientesUploadId) clientesUploadId
                                                                    FROM ". $this->pluginPrefix ."clientesUploadTmp ut
                                                                             JOIN ". $this->pluginPrefix ."vehiculos vh ON vh.placa = ut.placa
                                                                    GROUP BY ut.placa
                                                    ) d ON d.clientesUploadId = u.clientesUploadId
                                                    JOIN ". $this->pluginPrefix ."clientes c ON c.cedulaNit = u.cedulaNit
                                                    JOIN ". $this->pluginPrefix ."tipoMotor t ON t.tipoMotor = u.tipomotor
                                                    JOIN ". $this->pluginPrefix ."marcaMotores mm ON mm.marcaMotor = u.marcamotor"
                    . "                             JOIN ". $this->pluginPrefix ."marcaVehiculos mv ON mv.marcaVehiculo = u.marcavehiculo
                                                    JOIN ". $this->pluginPrefix ."vehiculos v ON v.placa = u.placa AND v.md5 !=  MD5(CONCAT(clienteId, u.placa, tipoMotorId, marcaMotorId, marcaVehiculoId, des_modelo)) COLLATE utf8_general_ci
                             ) dat ON dat.vehiculoId = veh.vehiculoId
                            SET veh.clienteId = dat.clienteId
                                ,veh.tipoMotorId = dat.tipoMotorId
                                ,veh.marcaMotorId = dat.marcaMotorId
                                ,veh.marcaVehiculoId = dat.marcaVehiculoId
                                ,veh.des_modelo = dat.des_modelo
                                ,veh.md5 = dat. md5
                                ,veh.date_entered = dat.date_entered
                                ,veh.created_by = dat.created_by"; break;
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
                                , "propietario", "comercial", "tipomotor", "marcamotor"
                                , "marcavehiculo", "des_modelo", "email", "confirmacion", "md5"
                                , "date_entered", "created_by");
        	
        if($_FILES["file"]["type"]=="text/csv" || $_FILES["file"]["type"]=="application/octet-stream" || $_FILES["file"]["type"]=="application/vnd.ms-excel"){
            $file = $target_path.$fileName.".".$ext;
            if(move_uploaded_file($_FILES['file']['tmp_name'], $file)) {
                $arrayFile = file($file);
                $validate = $this->validatorFile($arrayFile, array(  array("column" => "PLACA", "type" => "string", "required" => true)
                                                                    ,array("column" => "CIUDAD", "type" => "string", "required" => true)
                                                                    ,array("column" => "TIPO", "type" => "string", "required" => true)
                                                                    ,array("column" => "CEDULA / NIT", "type" => "string", "required" => true)
                                                                    ,array("column" => "PROPIETARIO", "type" => "string", "required" => true)
                                                                    ,array("column" => "COMERCIAL SIG", "type" => "string", "required" => true)
                                                                    ,array("column" => "TIPO DE MOTOR", "type" => "string", "required" => true)
                                                                    ,array("column" => "MARCA MOTOR", "type" => "string", "required" => true)
                                                                    ,array("column" => "MARCA VEHICULO", "type" => "string", "required" => true)
                                                                    ,array("column" => "MODELO", "type" => "string", "required" => true)
                                                                    ,array("column" => "EMAIL", "type" => "email", "required" => false)
                                                                    ,array("column" => "CONFIRMACION DE ENVIO", "type" => "option", "options" => array("S","N"), "required" => true)

                                                            )
                                                );
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
                                    `marcamotor`,
                                    `marcavehiculo`,
                                    `des_modelo`,
                                    `email`,
                                    `confirmacion`,
                                    `md5`,
                                    `date_entered`,
                                    `created_by`) VALUES " .$dataInsert;
                        $lines = $this->conn->query($query);
                        if($lines == count($validate["arrayResult"])){
                            $this->addMasterData("clientes");
                            $this->addMasterData("tipousuario");
                            $this->addMasterData("tipomotor");
                            $this->addMasterData("marcamotor");
                            $this->addMasterData("marcavehiculo");
                            $this->addMasterData("comercial");
                            $this->addMasterData("clientesInsert");
                            $this->addMasterData("clientesUpdate");
                            $this->addMasterData("vehiculosInsert");
                            $this->addMasterData("vehiculosUpdate");
                            
                            echo $this->resource->getWord("fileUploaded");
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
        $entity = $this->entity();
        $md5 = $this->setMd5(); 
        $this->addRecord($entity, $_POST, array("md5" => $md5,"date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
        if($this->LastId > 0 && !empty($this->LastId)){
            $userdata = array(
                            'user_login'  =>  $_POST["cedulaNit"],
                            'user_pass'   =>  $_POST["cedulaNit"],
                            'user_nicename' => $_POST["propietario"],
                            'display_name' => $_POST["propietario"]
                        ); 
            $user_id = wp_insert_user($userdata);
            
            $this->addRecord($entity["relationship"]["clientesUsuarios"], array("clienteId" => $this->LastId, "ID" => $user_id), array("date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
        }
    }
    public function edit(){
        $md5 = $this->setMd5(); 
        $this->updateRecord($this->entity(), $_POST, array("clienteId" => $_POST["clienteId"]), null, array("md5" => $md5));
    }
    public function del(){
        //wp_delete_user( $current_user->ID );
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
                    "tableName" => $this->pluginPrefix."clientes"
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
                    ,"relationship" => array(
                        "clientesUsuarios" => array(
                            "tableName" => $this->pluginPrefix."clientesUsuarios"
                            ,"atributes" => array(
                                "clientesUsuariosId" => array("type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true, "toolTip" => array("type" => "cell", "cell" => 2) )
                                ,"clienteId" => array("type" => "int", "required" => true)
                                ,"ID" => array("type" => "int", "required" => true)
                            )
                        )
                    )
                );
        return $data;
    }
}
?>