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
    
    public function getVehicleData(){
        $query = "SELECT c.propietario, v.placa, m.marcaMotor, t.tipoMotor
                    FROM ".$this->pluginPrefix."vehiculos v
                            JOIN ".$this->pluginPrefix."clientes c ON c.clienteId = v.clienteId
                            JOIN ".$this->pluginPrefix."marcaMotores m ON m.marcaMotorId = v.marcaMotorId
                            JOIN ".$this->pluginPrefix."tipoMotor t ON t.tipoMotorId = v.tipoMotorId
                    WHERE v.vehiculoId = ".$_GET["id"];
        $r = $this->get($query, "row");
        return $r["data"];
    }
    
    public function getmuestrasData(){
        $query = "SELECT `muestraId`,
                            `nromuestra`,
                            `componentenumero`,
                            `ftoma`,
                            `klactual`,
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
                        FROM ".$this->pluginPrefix."muestras m
                            JOIN ".$this->pluginPrefix."tipoMuestras t ON t.tipoMuestraId = m.tipoMuestraId
                        WHERE m.vehiculoId = ".$_GET["id"]." AND t.`tipoMuestra` LIKE '%".$_GET["type"]."%'";
        $r = $this->getDataGrid($query, 0, 6 , "ftoma", "DESC");
        return $r;
    }
    
    public function report(){
        require_once($this->pluginPath.'/helpers/html2pdf/html2pdf.class.php');
        
        $vehicleData = $this->getVehicleData();
        $muestrasData = $this->getmuestrasData();
        
        $template =  "<style>".file_get_contents(__DIR__."/miFlotaReporTemplates/css/style.css")."</style>";
        $template .= file_get_contents(__DIR__."/miFlotaReporTemplates/".$_GET["type"].'template.inc');
        
        $template = str_replace("{slogan}", $this->resource->getWord("slogan"), $template);
        
        $template = str_replace("{socioLabel}", $this->resource->getWord("socioLabel"), $template);
        $template = str_replace("{socio}", $vehicleData->propietario, $template);
        $template = str_replace("{placaLabel}", $this->resource->getWord("placa"), $template);
        $template = str_replace("{placa}", $vehicleData->placa, $template);
        $template = str_replace("{marcaMotorLabel}", $this->resource->getWord("marcaMotor"), $template);
        $template = str_replace("{marcaMotor}", $vehicleData->marcaMotor, $template);
        $template = str_replace("{tipoMotorLabel}", $this->resource->getWord("tipoMotorId"), $template);
        $template = str_replace("{tipoMotor}", $vehicleData->tipoMotor, $template);
        
        $template = str_replace("{fechaLabel}", $this->resource->getWord("fecha"), $template);
        $template = str_replace("{combustibleLabel}", $this->resource->getWord("combustible"), $template);
        $template = str_replace("{aguaLabel}", $this->resource->getWord("agua"), $template);
        $template = str_replace("{tbnLabel}", $this->resource->getWord("tbn"), $template);
        $template = str_replace("{tipo}", $this->resource->getWord($_GET["type"]), $template);
        $template = str_replace("{itemLabel}", $this->resource->getWord("item"), $template);
        $template = str_replace("{observacionesLabel}", $this->resource->getWord("observaciones"), $template);
        $template = str_replace("{muestraLabel}", $this->resource->getWord("nromuestra"), $template);
        $template = str_replace("{kilometrosLabel}", $this->resource->getWord("Kilometraje"), $template);
        
        $i = 1;
        if(count($muestrasData["data"]) > 0)
        {
            foreach($muestrasData["data"] as $row){
                $otrasVariables .= "<tr>"
                                    . "<td class='borderLeft'>".$row->ftoma."</td>"
                                    . "<td>".$row->combustible."</td>"
                                    . "<td>".$row->agua."</td>"
                                    . "<td>".$row->tbn."</td>"
                                . "</tr>";
                $tipoAceite .= "<tr>"
                                    . "<td class='borderLeft'>".$i."</td>"
                                    . "<td>".$row->observaciones."</td>"
                                    . "<td>".$row->ftoma."</td>"
                                . "</tr>";
                $infoMuestras .= "<tr>"
                                    . "<td class='borderLeft'>".$i."</td>"
                                    . "<td>".$row->nromuestra."</td>"
                                    . "<td>".$row->klactual."</td>"
                                . "</tr>";
            
                $i++;
            }
        }
        else{
            $otrasVariables = "<tr>"
                                . "<td></td>"
                                . "<td></td>"
                                . "<td></td>"
                                . "<td></td>"
                            . "</tr>";
            $tipoAceite = "<tr>"
                                . "<td></td>"
                                . "<td></td>"
                                . "<td></td>"
                            . "</tr>";
            $infoMuestras = "<tr>"
                                . "<td></td>"
                                . "<td></td>"
                                . "<td></td>"
                            . "</tr>";
        }
        
        $template = str_replace("{otrasVariablesRows}", $otrasVariables, $template);
        $template = str_replace("{tipoAceiteRows}", $tipoAceite, $template);
        $template = str_replace("{infoMuestrasRows}", $infoMuestras, $template);
        
        
        try
        {
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
    //      $html2pdf->setModeDebug();
            $html2pdf->setDefaultFont('Arial');
            $html2pdf->writeHTML($template);
            $html2pdf->Output($_GET["type"].'.pdf');
        }
        catch(HTML2PDF_exception $e) {
            echo $e;
            exit;
        }
        echo $template;
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
                                            . "return '<a title=\"Motor\" href=\"".$this->pluginURL."downloadReport.php?controller=miFlota&type=motor&id='+rowObject[0]+'\" target=\"_blank\"><img src=\"".$this->pluginURL."images/informes_motor_btn.png\"/> </a>';}@")
                        ,"caja" => array("type" => "int","required" => false, "readOnly" => true, "isTableCol" => false,"formatter" => "@function(cellvalue, options, rowObject){" 
                                            . "return '<a title=\"Caja\" href=\"".$this->pluginURL."downloadReport.php?controller=miFlota&type=caja&id='+rowObject[0]+'\" target=\"_blank\"><img src=\"".$this->pluginURL."images/informes_caja_btn.png\"/> </a>';}@")
                        ,"diferencial" => array("type" => "int","required" => false, "readOnly" => true, "isTableCol" => false,"formatter" => "@function(cellvalue, options, rowObject){" 
                                            . "return '<a title=\"Diferencial\" href=\"".$this->pluginURL."downloadReport.php?controller=miFlota&type=diferencial&id='+rowObject[0]+'\" target=\"_blank\"><img src=\"".$this->pluginURL."images/informes_diferencial_btn.png\"/> </a>';}@")
                    )
                );  
        return $data;
    }
}
?>