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
                            `minvis`,
                            `vis100`,
                            `maxvis`,
                            `vis40`,
                            `minfe`,
                            `fe`,
                            `maxfe`,
                            `mincr`,
                            `cr`,
                            `maxcr`,
                            `minpb`,
                            `pb`,
                            `maxpb`,
                            `minal`,
                            `al`,
                            `maxal`,
                            `mincu`,
                            `cu`,
                            `maxcu`,
                            `minsi`,
                            `si`,
                            `maxsi`,
                            `minhollin`,
                            `hollin`,
                            `maxhollin`,
                            `mintbn`,
                            `tbn`,
                            `maxtbn`,
                            `minagua`,
                            `agua`,
                            `maxagua`,
                            `mincombustible`,
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
    
    private function validateMuestras(){
        $msj = "";
        $query = "SELECT t.nromuestra FROM ". $this->pluginPrefix ."muestrasUploadTmp t
                    WHERE EXISTS(
                                            SELECT 1 FROM ". $this->pluginPrefix ."muestras v
                                            WHERE   v.nromuestra = t.nromuestra
                                    );";
        $result = $this->conn->get_col($query);
        
        
        foreach ($result as $num_linea => $linea){
            $msj .= $linea.": ".$this->resource->getWord("muestraExiste")."\n";
        }
        return $msj;
    }
    
    private function validateVehicles(){
        $msj = "";
        $query = "SELECT t.placav FROM ". $this->pluginPrefix ."muestrasUploadTmp t
                    WHERE NOT EXISTS(
                                            SELECT 1 FROM ". $this->pluginPrefix ."vehiculos v
                                            WHERE   v.placa = t.placav
                                    );";
        $result = $this->conn->get_col($query);
        
        
        foreach ($result as $num_linea => $linea){
            $msj .= $linea.": ".$this->resource->getWord("vehiculoNoExiste")."\n";
        }
        return $msj;
    }
    
    public function addMasterData($param){
        
        switch($param){
            case "estado":
                    $query = "INSERT INTO `". $this->pluginPrefix ."estadoMuestras`(`estadoMuestra`)
                                SELECT u.estado 
                                FROM ". $this->pluginPrefix ."muestrasUploadTmp u
                                      LEFT JOIN ". $this->pluginPrefix ."estadoMuestras c ON u.estado = c.estadoMuestra
                                WHERE c.estadoMuestraId IS NULL
                                GROUP BY u.estado"; break;
            case "tipomuestra":
                    $query = "INSERT INTO `". $this->pluginPrefix ."tipoMuestras`(`tipoMuestra`)
                                SELECT u.muestra 
                                FROM ". $this->pluginPrefix ."muestrasUploadTmp u
                                      LEFT JOIN ". $this->pluginPrefix ."tipoMuestras c ON u.muestra = c.tipoMuestra
                                WHERE c.tipoMuestraId IS NULL
                                GROUP BY u.muestra"; break;
            case "muestras":
                    $query = "INSERT INTO `". $this->pluginPrefix ."muestras`
                                    (`vehiculoId`,
                                        `nromuestra`,
                                        `estadoMuestraId`,
                                        `tipoMuestraId`,
                                        `componentenumero`,
                                        `ftoma`,
                                        `kanterior`,
                                        `klactual`,
                                        `minvis`,
                                        `vis100`,
                                        `maxvis`,
                                        `vis40`,
                                        `minfe`,
                                        `fe`,
                                        `maxfe`,
                                        `mincr`,
                                        `cr`,
                                        `maxcr`,
                                        `minpb`,
                                        `pb`,
                                        `maxpb`,
                                        `minal`,
                                        `al`,
                                        `maxal`,
                                        `mincu`,
                                        `cu`,
                                        `maxcu`,
                                        `minsi`,
                                        `si`,
                                        `maxsi`,
                                        `minhollin`,
                                        `hollin`,
                                        `maxhollin`,
                                        `mintbn`,
                                        `tbn`,
                                        `maxtbn`,
                                        `minagua`,
                                        `agua`,
                                        `maxagua`,
                                        `mincombustible`,
                                        `combustible`,
                                        `maxcombustible`,
                                        `escritica`,
                                        `observaciones`,
                                        `date_entered`,
                                        `created_by`)
                                SELECT `vehiculoId`,
                                        `nromuestra`,
                                        `estadoMuestraId`,
                                        `tipoMuestraId`,
                                        `componentenumero`,
                                        `ftoma`,
                                        `kanterior`,
                                        `klactual`,
                                        `minvis`,
                                        `vis100`,
                                        `maxvis`,
                                        `vis40`,
                                        `minfe`,
                                        `fe`,
                                        `maxfe`,
                                        `mincr`,
                                        `cr`,
                                        `maxcr`,
                                        `minpb`,
                                        `pb`,
                                        `maxpb`,
                                        `minal`,
                                        `al`,
                                        `maxal`,
                                        `mincu`,
                                        `cu`,
                                        `maxcu`,
                                        `minsi`,
                                        `si`,
                                        `maxsi`,
                                        `minhollin`,
                                        `hollin`,
                                        `maxhollin`,
                                        `mintbn`,
                                        `tbn`,
                                        `maxtbn`,
                                        `minagua`,
                                        `agua`,
                                        `maxagua`,
                                        `mincombustible`,
                                        `combustible`,
                                        `maxcombustible`,
                                    `escritica`,
                                    `observaciones`,
                                    mt.date_entered,
                                    mt.created_by
                                FROM `". $this->pluginPrefix ."muestrasUploadTmp` mt
                                        JOIN `". $this->pluginPrefix ."vehiculos` v ON v.placa = mt.placav
                                        JOIN `". $this->pluginPrefix ."estadoMuestras` e ON e.estadoMuestra = mt.estado
                                        JOIN `". $this->pluginPrefix ."tipoMuestras` t ON t.tipoMuestra = mt.muestra"; break;
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
        	
        if($_FILES["file"]["type"]=="text/csv" || $_FILES["file"]["type"]=="application/octet-stream" || $_FILES["file"]["type"]=="application/vnd.ms-excel"){
            $file = $target_path.$fileName.".".$ext;
            if(move_uploaded_file($_FILES['file']['tmp_name'], $file)) {
                $arrayFile = file($file);
                $validate = $this->validatorFile($arrayFile, array(array("column" => "placav", "type" => "string", "required" => true)
                                                                    ,array("column" => "nromuestra", "type" => "string", "required" => true)
                                                                    ,array("column" => "estado", "type" => "string", "required" => true)
                                                                    ,array("column" => "muestra", "type" => "string", "required" => true)
                                                                    ,array("column" => "componentenumero", "type" => "number", "required" => true)
                                                                    ,array("column" => "ftoma", "type" => "date", "format" => "dd\/mm\/yyyy", "required" => true)
                                                                    ,array("column" => "kanterior", "type" => "number", "required" => true)
                                                                    ,array("column" => "klactual", "type" => "number", "required" => true)
                                                                    ,array("column" => "minvis", "type" => "number", "required" => true)
                                                                    ,array("column" => "vis100", "type" => "number", "required" => false)
                                                                    ,array("column" => "maxvis", "type" => "number", "required" => true)
                                                                    ,array("column" => "vis40", "type" => "number", "required" => false)
                                                                    ,array("column" => "minfe", "type" => "number", "required" => true)
                                                                    ,array("column" => "fe", "type" => "number", "required" => false)
                                                                    ,array("column" => "maxfe", "type" => "number", "required" => true)
                                                                    ,array("column" => "mincr", "type" => "number", "required" => true)
                                                                    ,array("column" => "cr", "type" => "number", "required" => false)
                                                                    ,array("column" => "maxcr", "type" => "number", "required" => true)
                                                                    ,array("column" => "minpb", "type" => "number", "required" => true)
                                                                    ,array("column" => "pb", "type" => "number", "required" => false)
                                                                    ,array("column" => "maxpb", "type" => "number", "required" => true)
                                                                    ,array("column" => "minal", "type" => "number", "required" => true)
                                                                    ,array("column" => "al", "type" => "number", "required" => false)
                                                                    ,array("column" => "maxal", "type" => "number", "required" => true)
                                                                    ,array("column" => "mincu", "type" => "number", "required" => true)
                                                                    ,array("column" => "cu", "type" => "number", "required" => false)
                                                                    ,array("column" => "maxcu", "type" => "number", "required" => true)
                                                                    ,array("column" => "minsi", "type" => "number", "required" => true)
                                                                    ,array("column" => "si", "type" => "number", "required" => false)
                                                                    ,array("column" => "maxsi", "type" => "number", "required" => true)
                                                                    ,array("column" => "minhollin", "type" => "number", "required" => true)
                                                                    ,array("column" => "hollin", "type" => "number", "required" => false)
                                                                    ,array("column" => "maxhollin", "type" => "number", "required" => true)
                                                                    ,array("column" => "mintbn", "type" => "number", "required" => true)
                                                                    ,array("column" => "tbn", "type" => "number", "required" => false)
                                                                    ,array("column" => "maxtbn", "type" => "number", "required" => true)
                                                                    ,array("column" => "minagua", "type" => "number", "required" => true)
                                                                    ,array("column" => "agua", "type" => "number", "required" => false)
                                                                    ,array("column" => "maxagua", "type" => "number", "required" => true)
                                                                    ,array("column" => "mincombustible", "type" => "number", "required" => true)
                                                                    ,array("column" => "combustible", "type" => "number", "required" => false)
                                                                    ,array("column" => "maxcombustible", "type" => "number", "required" => true)
                                                                    ,array("column" => "escritica", "type" => "option", "options" => array("si","no"), "required" => true)
                                                                    ,array("column" => "observaciones", "type" => "string", "required" => true)
                                                                ));
                if($validate["result"]){
                    $table = $this->pluginPrefix."muestrasUploadTmp";
                    if($this->truncateTable($table)){
                        $lines = 0;
                        $record = array();
                        
                        $date_entered = date("Y-m-d H:i:s");
                        $created_by = $this->currentUser->ID;
                        foreach ($validate["arrayResult"] as $num_linea => $linea){
                            $ftoma = explode("/", $linea[5]);
                            $linea[5] = $ftoma[2]."-".$ftoma[1]."-".$ftoma[0];
                            $r = "'".trim(implode("','",$linea))."','".$date_entered."','".$created_by."'";
                            $r = str_replace("''", 'NULL', $r);
                                    
                            $record[] = $r;
                        }
                        $dataInsert = '('.implode("),(",$record).')';
                        $query = "INSERT INTO ".$table."
                                        (`placav`,
                                        `nromuestra`,
                                        `estado`,
                                        `muestra`,
                                        `componentenumero`,
                                        `ftoma`,
                                        `kanterior`,
                                        `klactual`,
                                        `minvis`,
                                        `vis100`,
                                        `maxvis`,
                                        `vis40`,
                                        `minfe`,
                                        `fe`,
                                        `maxfe`,
                                        `mincr`,
                                        `cr`,
                                        `maxcr`,
                                        `minpb`,
                                        `pb`,
                                        `maxpb`,
                                        `minal`,
                                        `al`,
                                        `maxal`,
                                        `mincu`,
                                        `cu`,
                                        `maxcu`,
                                        `minsi`,
                                        `si`,
                                        `maxsi`,
                                        `minhollin`,
                                        `hollin`,
                                        `maxhollin`,
                                        `mintbn`,
                                        `tbn`,
                                        `maxtbn`,
                                        `minagua`,
                                        `agua`,
                                        `maxagua`,
                                        `mincombustible`,
                                        `combustible`,
                                        `maxcombustible`,
                                        `escritica`,
                                        `observaciones`,
                                        `date_entered`,
                                        `created_by`) VALUES " .$dataInsert;
                        $lines = $this->conn->query($query);
                        if($lines == count($validate["arrayResult"])){
                            $msj = $this->validateVehicles();
                            $msjMuestras = $this->validateMuestras();
                            if(empty($msj) && empty($msjMuestras)){
                                $this->addMasterData("estado");
                                $this->addMasterData("tipomuestra");
                                $this->addMasterData("muestras");
                                echo $this->resource->getWord("fileUploaded");
                            }
                            else
                                echo $msjMuestras.$msj;
                        }
                    }
                }
                else{
                    echo $validate["msj"];
                }
                unlink($file);
            } 
            else
                echo "sss".$this->resource->getWord("fileUploadError");
        }
    }   
    
    public function add(){
        $this->addRecord($this->entity(), $_POST, array("date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
    }
    public function edit(){
        $this->updateRecord($this->entity(), $_POST, array("muestraId" => $_POST["muestraId"]), null, null);
    }
    public function del(){
        $this->eliminateRecord($this->entity(), array("muestraId" => $_POST["id"]));
    }

    public function detail($params = array()){
        $entity = $this->entity();
        $query = "  SELECT muestraId, `placa` vehiculoId,
                                        `nromuestra`,
                                        `estadoMuestra` estadoMuestraId,
                                        `tipoMuestra` tipoMuestraId,
                                        `componentenumero`,
                                        `ftoma`,
                                        `kanterior`,
                                        `klactual`,
                                        `minvis`,
                                        `vis100`,
                                        `maxvis`,
                                        `vis40`,
                                        `minfe`,
                                        `fe`,
                                        `maxfe`,
                                        `mincr`,
                                        `cr`,
                                        `maxcr`,
                                        `minpb`,
                                        `pb`,
                                        `maxpb`,
                                        `minal`,
                                        `al`,
                                        `maxal`,
                                        `mincu`,
                                        `cu`,
                                        `maxcu`,
                                        `minsi`,
                                        `si`,
                                        `maxsi`,
                                        `minhollin`,
                                        `hollin`,
                                        `maxhollin`,
                                        `mintbn`,
                                        `tbn`,
                                        `maxtbn`,
                                        `minagua`,
                                        `agua`,
                                        `maxagua`,
                                        `mincombustible`,
                                        `combustible`,
                                        `maxcombustible`,
                                        `escritica`,
                                        `observaciones`
                                FROM ".$entity["tableName"]." m
                                        JOIN ". $this->pluginPrefix ."tipoMuestras tm ON tm.tipoMuestraId = m.tipoMuestraId
                                        JOIN `". $this->pluginPrefix ."vehiculos` v ON v.vehiculoId = m.vehiculoId
                                        JOIN `". $this->pluginPrefix ."estadoMuestras` e ON e.estadoMuestraId = m.estadoMuestraId
                                WHERE m.`muestraId` = " . $params["filter"];
        $this->queryType = "row";
        return $this->getDataGrid($query);
    }
    
    public function entity($CRUD = array())
    {
        $data = array(
                    "tableName" => $this->pluginPrefix."muestras"
                    ,"entityConfig" => $CRUD
                    ,"atributes" => array(
                        "muestraId" => array("label" => "id", "type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true, "toolTip" => array("type" => "cell", "cell" => 2) )
                        ,"vehiculoId" => array("label" => "vehiculo","type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."vehiculos", "id" => "vehiculoId", "text" => "placa"))
                        ,"nromuestra" => array("type" => "varchar", "required" => true)
                        ,"estadoMuestraId" => array("label" => "estadonc","hidden" => true, "type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."estadoMuestras", "id" => "estadoMuestraId", "text" => "estadoMuestra"))
                        ,"tipoMuestraId" => array("label" => "tipoMuestra","type" => "int", "required" => true, "references" => array("table" => $this->pluginPrefix."tipoMuestras", "id" => "tipoMuestraId", "text" => "tipoMuestra"))
                        ,"componentenumero" => array("type" => "int", "required" => true , "hidden" => true, 'edithidden' => true)
                        ,"ftoma" => array("label" =>"fecha", "type" => "date", "required" => true)
                        ,"kanterior" => array("type" => "int", "required" => true , "hidden" => true, 'edithidden' => true)
                        ,"klactual" => array("type" => "int", "required" => true)
                        ,"minvis" => array("type" => "number","width" => "90", "required" => true , "hidden" => true, 'edithidden' => true)
                        ,"vis100" => array("type" => "number","width" => "90", "required" => true)
                        ,"maxvis" => array("type" => "number","width" => "90", "required" => true , "hidden" => true, 'edithidden' => true)
                        ,"vis40" => array("type" => "number","width" => "90", "required" => true)
                        ,"minfe" => array("type" => "number","width" => "90", "required" => true , "hidden" => true, 'edithidden' => true)
                        ,"fe" => array("type" => "number","width" => "90", "required" => true)
                        ,"maxfe" => array("type" => "number","width" => "90", "required" => true , "hidden" => true, 'edithidden' => true)
                        ,"mincr" => array("type" => "number","width" => "90", "required" => true , "hidden" => true, 'edithidden' => true)
                        ,"cr" => array("type" => "number","width" => "90", "required" => true)
                        ,"maxcr" => array("type" => "number","width" => "90", "required" => true , "hidden" => true, 'edithidden' => true)
                        ,"minpb" => array("type" => "number","width" => "90", "required" => true , "hidden" => true, 'edithidden' => true)
                        ,"pb" => array("type" => "number","width" => "90", "required" => true)
                        ,"maxpb" => array("type" => "number","width" => "90", "required" => true , "hidden" => true, 'edithidden' => true)
                        ,"minal" => array("type" => "number","width" => "90", "required" => true , "hidden" => true, 'edithidden' => true)
                        ,"al" => array("type" => "number","width" => "90", "required" => true)
                        ,"maxal" => array("type" => "number","width" => "90", "required" => true , "hidden" => true, 'edithidden' => true)
                        ,"mincu" => array("type" => "number","width" => "90", "required" => true , "hidden" => true, 'edithidden' => true)
                        ,"cu" => array("type" => "number","width" => "90", "required" => true)
                        ,"maxcu" => array("type" => "number","width" => "90", "required" => true , "hidden" => true, 'edithidden' => true)
                        ,"minsi" => array("type" => "number","width" => "90", "required" => true , "hidden" => true, 'edithidden' => true)
                        ,"si" => array("type" => "number","width" => "90", "required" => true)
                        ,"maxsi" => array("type" => "number","width" => "90", "required" => true , "hidden" => true, 'edithidden' => true)
                        ,"minhollin" => array("type" => "number","width" => "90", "required" => false , "hidden" => true, 'edithidden' => true)
                        ,"hollin" => array("type" => "number","width" => "90", "required" => true)
                        ,"maxhollin" => array("type" => "number","width" => "90", "required" => false , "hidden" => true, 'edithidden' => true)
                        ,"mintbn" => array("type" => "number","width" => "90", "required" => false , "hidden" => true, 'edithidden' => true)
                        ,"tbn" => array("type" => "number","width" => "90", "required" => true)
                        ,"maxtbn" => array("type" => "number","width" => "90", "required" => false , "hidden" => true, 'edithidden' => true)
                        ,"minagua" => array("type" => "number","width" => "90", "required" => false , "hidden" => true, 'edithidden' => true)
                        ,"agua" => array("type" => "number","width" => "90", "required" => false)
                        ,"maxagua" => array("type" => "number","width" => "90", "required" => false , "hidden" => true, 'edithidden' => true)
                        ,"mincombustible" => array("type" => "number","width" => "90", "required" => false , "hidden" => true, 'edithidden' => true)
                        ,"combustible" => array("type" => "number","width" => "90", "required" => true)
                        ,"maxcombustible" => array("type" => "number","width" => "90", "required" => false , "hidden" => true, 'edithidden' => true)
                        ,"escritica" => array("type" => "enum","width" => "90", "required" => false)
                        ,"observaciones" => array("type" => "text", "required" => false , "hidden" => true, 'edithidden' => true)
                    )
                );  
        return $data;
    }
}
?>