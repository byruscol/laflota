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
        $entity = $this->entity();
        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT `vehiculoId`,
                        `placa`,
                        `tipoMotorId`,
                        `marcaMotorId`,
                        `marcaVehiculoId`,
                        `des_modelo`
                    FROM ".$entity["tableName"]." i"
                    . " JOIN ".$this->pluginPrefix."clientesUsuarios c ON c.clienteId = i.clienteId"
                . " WHERE c.ID = ".$this->currentUser->ID;
        
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
                        FROM ".$this->pluginPrefix."muestras m
                            JOIN ".$this->pluginPrefix."tipoMuestras t ON t.tipoMuestraId = m.tipoMuestraId
                        WHERE m.vehiculoId = ".$_GET["id"]." AND t.`tipoMuestra` LIKE '%".$_GET["type"]."%'";
        $r = $this->getDataGrid($query, 0, 6 , "ftoma", "DESC");
        return $r;
    }
    
    public function setTableData($data){
        $table = '<table cellspacing="0" align="center">';
        $header = "<tr><th class='borderLeft'></th>";
        $min = "<tr><td class='borderLeft smallFont'>".$this->resource->getWord('minValue')."</td>";
        $val = "<tr><td class='borderLeft smallFont'>".$this->resource->getWord('Value')."</td>";
        $max = "<tr><td class='borderLeft smallFont'>".$this->resource->getWord('maxValue')."</td>";
        $i = 0;
        foreach($data["values"] as $key => $value){
            $i++;
            if(!$data["fixed"] || ($data["fixed"] && $i > 1 && count($data["values"]) > $i)){
                $header .= "<th class='smallFont'>"
                            . $data["dates"][$key]
                            . "</th>";
                $min .=  "<td class='smallFont'>"
                        . $data["min"][$key]
                        . "</td>";
                $val .=  "<td class='smallFont'>"
                        . (($value > $data["max"][$key] || $value < $data["min"][$key])? "<span class='red'>".$value."</span>" : $value)
                        . "</td>";
                $max .=  "<td class='smallFont'>"
                        . $data["max"][$key]
                        . "</td>";
            }
        }
        return  $table .= $header."</tr>".$min."</tr>".$val."</tr>".$max."</tr></table>";
    }
    
    public function getChartLine($data){

        define('PREFIX_DIR', 'tmp.muestras');   // images will be created here
        define('PREFIX', 'muestras');   // prefix for the images, can be anything
        $tmpfname = tempnam(PREFIX_DIR, PREFIX);
        
        require_once ($this->pluginPath.'/helpers/jpgraph/jpgraph.php');
        require_once ($this->pluginPath.'/helpers/jpgraph/jpgraph_line.php');

        $min = array_reverse($data["min"]);
        $values = array_reverse($data["values"]);
        $max =  array_reverse($data["max"]);
        $dates =  array_reverse($data["dates"]);
        
        $fixed = false;
        
        if(count($values) < 2){
            array_unshift($min, $min[0]);
            $min[] = $min[0];
            
            array_unshift($values, null);
            $values[] = null;
            
            array_unshift($max, $max[0]);
            $max[] = $max[0];
            
            
            array_unshift($dates, '');
            $dates[] = '';
            
            $fixed = true;
        }
        
        $tableData = $this->setTableData(array("min" => $min,"values" => $values, "max" => $max, "dates" => $dates, "fixed" => $fixed));
        
        // Setup the graph
        $graph = new Graph(310);
        $graph->SetScale("textlin");

        $theme_class=new UniversalTheme;

        $graph->SetTheme($theme_class);
        $graph->img->SetAntiAliasing(false);
        $graph->title->Set($data["title"]);
        $graph->SetBox(false);
        
        $graph->legend->SetPos(0.53,0,'center','top');

        $graph->img->SetAntiAliasing();

        $graph->yaxis->HideZeroLabel();
        $graph->yaxis->HideLine(false);
        $graph->yaxis->HideTicks(false,false);

        $graph->xgrid->Show();
        $graph->xgrid->SetLineStyle("solid");
        $graph->xaxis->SetTickLabels($dates);
        $graph->xgrid->SetColor('#E3E3E3');
        $graph->xaxis->SetLabelAngle(55);
        $graph->xaxis->SetLabelMargin(1); 

        // Create the first line
        $p1 = new LinePlot($min);
        $graph->Add($p1);
        $p1->SetColor("#FE642E");
        $p1->SetLegend($this->resource->getWord('minValue'));

        // Create the second line
        $p2 = new LinePlot($values);
        $graph->Add($p2);
        $p2->SetColor("blue");
        $p2->SetLegend($this->resource->getWord('Value'));
        $p2->mark->SetType(MARK_FILLEDCIRCLE,'',1);
        $p2->mark->SetFillColor('blue');
        $p2->mark->SetSize(2);

        // Create the third line
        $p3 = new LinePlot($max);
        $graph->Add($p3);
        $p3->SetColor("red");
        $p3->SetLegend($this->resource->getWord('maxValue'));

        $graph->legend->SetFrameWeight(1);

        // Output line
        $graph->Stroke($tmpfname);
        return array("src" => $tmpfname,"table" => $tableData);
    }
    
    public function getCantidadMuestrasCriticas($id){
        $query = "SELECT m.escritica
                    FROM ".$this->pluginPrefix."vehiculos v 
                        JOIN ".$this->pluginPrefix."muestras m ON m.vehiculoId = v.vehiculoId 
                    WHERE v.vehiculoId = ".$id;
        $result = $this->getDataGrid($query, 0, 6 , "m.muestraId", "DESC");

        $i = 0;
        foreach ($result["data"] as $key => $value){
            if($value->escritica == 'si'){
                $i++;
            }
        }
        return $i;
    }
    
    
    public function  getExtendedCriticalVehicles(){
        global $isCurrentUserLoggedIn;
        $this->isCurrentUserLoggedIn = (function_exists ("is_user_logged_in"))?is_user_logged_in(): $isCurrentUserLoggedIn;
        if($this->isCurrentUserLoggedIn == 0)
            exit($this->resource->getWord("noPermisos"));
        
        $condition = ($_POST["type"] == "user")? "cu.ID=".$this->currentUser->ID : "cu.clienteID = ".$_POST["filter"];
        
        $query = "SELECT v.vehiculoId, v.placa
                    FROM ".$this->pluginPrefix."clientesUsuarios cu 
                    JOIN ".$this->pluginPrefix."vehiculos v ON v.clienteId = cu.clienteId 
                    WHERE ".$condition;
 
        $vehicles = $this->getDataGrid($query, null, null , "placa", "ASC");
        
        $criticas = array();
        
        foreach($vehicles["data"] as $key => $value){
            $CantidadMuestrasCriticas = $this->getCantidadMuestrasCriticas($value->vehiculoId);
            if($CantidadMuestrasCriticas > 0)
                $criticas["data"][]= array("placa" => $value->placa, "Q" => $CantidadMuestrasCriticas);
        }
        
        $query = "SELECT ve.placa, d.kilometraje
                    FROM (
                                    SELECT e.vehiculoId, max(kilometraje) kilometraje
                                    FROM ".$this->pluginPrefix."clientesUsuarios cu 
                                    JOIN ".$this->pluginPrefix."vehiculos v ON v.clienteId = cu.clienteId 
                                    JOIN ".$this->pluginPrefix."extensiones e ON e.vehiculoId = v.vehiculoId
                                    WHERE ".$condition." 
                                    GROUP BY e.vehiculoId
                            )d
                            JOIN ".$this->pluginPrefix."vehiculos ve ON ve.vehiculoId = d.vehiculoId";
        $extendidas = $this->getDataGrid($query, null, null , "placa", "ASC");
        return array("customResponce"=> true, "data" => array("criticas" => $criticas["data"], "extendidas" => $extendidas["data"]));
    }


    public function checkVehicleUser(){
        $query = "SELECT COUNT(1) Q 
                    FROM ".$this->pluginPrefix."clientesUsuarios cu
                             JOIN ".$this->pluginPrefix."vehiculos v ON v.clienteId = cu.clienteId
                    WHERE cu.ID = ".$this->currentUser->ID." AND v.vehiculoId = ".$_GET["id"];
        $val = $this->get($query, "var");
        
        return ($val["data"] > 0)? true : false;
    }
    
    public function report(){
        global $isCurrentUserLoggedIn;
        $this->isCurrentUserLoggedIn = (function_exists ("is_user_logged_in"))?is_user_logged_in(): $isCurrentUserLoggedIn;
        $vehicleInCliente = $this->checkVehicleUser();
        if($this->isCurrentUserLoggedIn == 0 || (!$vehicleInCliente && $this->currentUser->caps["administrator"] != 1))
            exit($this->resource->getWord("noPermisos"));
        
        require_once($this->pluginPath.'/helpers/html2pdf/html2pdf.class.php');
        
        $vehicleData = $this->getVehicleData();
        $muestrasData = $this->getmuestrasData();
        
        $template =  "<style>".file_get_contents(__DIR__."/miFlotaReporTemplates/css/style.css")."</style>";
        //$template .= file_get_contents(__DIR__."/miFlotaReporTemplates/".$_GET["type"].'template.inc');
        $template .= file_get_contents(__DIR__."/miFlotaReporTemplates/motortemplate.inc");
        
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
        $template = str_replace("{tablaComponentes}", $this->pluginPath."/models/miFlotaReporTemplates/css/img/tabla.jpg", $template);
        
        $i = 1;
        $feData = array("title" => $this->resource->getWord("fe"), "min" => array(), "values" => array(), "max" => array(), "dates" => array());
        if(count($muestrasData["data"]) > 0)
        {
            foreach($muestrasData["data"] as $row){
                
                $date = date_create($row->ftoma);
                $row->ftoma = date_format($date, 'd/m/y');
                
                $feData["min"][] = (empty($row->minfe))? 0:$row->minfe;
                $feData["values"][] = (empty($row->fe))? 0:$row->fe;
                $feData["max"][] = (empty($row->maxfe))? 0:$row->maxfe;
                $feData["dates"][] = (empty($row->ftoma))? '':$row->ftoma;
                
                $pbData["min"][] = (empty($row->minpb))? 0:$row->minpb;
                $pbData["values"][] = (empty($row->pb))? 0:$row->pb;
                $pbData["max"][] = (empty($row->maxpb))? 0:$row->maxpb;
                $pbData["dates"][] = (empty($row->ftoma))? '':$row->ftoma;
                
                $cuData["min"][] = (empty($row->mincu))? 0:$row->mincu;
                $cuData["values"][] = (empty($row->cu))? 0:$row->cu;
                $cuData["max"][] = (empty($row->maxcu))? 0:$row->maxcu;
                $cuData["dates"][] = (empty($row->ftoma))? '':$row->ftoma;
                
                $crData["min"][] = (empty($row->mincr))? 0:$row->mincr;
                $crData["values"][] = (empty($row->cr))? 0:$row->cr;
                $crData["max"][] = (empty($row->maxcr))? 0:$row->maxcr;
                $crData["dates"][] = (empty($row->ftoma))? '':$row->ftoma;
                
                $alData["min"][] = (empty($row->minal))? 0:$row->minal;
                $alData["values"][] = (empty($row->al))? 0:$row->al;
                $alData["max"][] = (empty($row->maxal))? 0:$row->maxal;
                $alData["dates"][] = (empty($row->ftoma))? '':$row->ftoma;
                
                $siData["min"][] = (empty($row->minsi))? 0:$row->minsi;
                $siData["values"][] = (empty($row->si))? 0:$row->si;
                $siData["max"][] = (empty($row->maxsi))? 0:$row->maxsi;
                $siData["dates"][] = (empty($row->ftoma))? '':$row->ftoma;
                
                $vis100Data["min"][] = (empty($row->minvis))? 0:$row->minvis;
                $vis100Data["values"][] = (empty($row->vis100))? 0:$row->vis100;
                $vis100Data["max"][] = (empty($row->maxvis))? 0:$row->maxvis;
                $vis100Data["dates"][] = (empty($row->ftoma))? '':$row->ftoma;
                
                $hollinData["min"][] = (empty($row->minhollin))? 0:$row->minhollin;
                $hollinData["values"][] = (empty($row->hollin))? 0:$row->hollin;
                $hollinData["max"][] = (empty($row->maxhollin))? 0:$row->maxhollin;
                $hollinData["dates"][] = (empty($row->ftoma))? '':$row->ftoma;
                
                $otrasVariables .= "<tr>"
                                    . "<td class='borderLeft'>".$row->ftoma."</td>"
                                    . "<td>".$row->combustible."</td>"
                                    . "<td>".$row->agua."</td>"
                                    . "<td>".$row->tbn."</td>"
                                . "</tr>";
                $tipoAceite .= "<tr>"
                                    . "<td class='borderLeft width30'>".$i."</td>"
                                    . "<td class='width523'><p>".$row->observaciones."</p></td>"
                                    . "<td class='width70'>".$row->ftoma."</td>"
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
        
        $fe = (count($feData["values"]) == 0)? array("src" => $this->resource->getWord("noDataLegend"),"table" => $this->resource->getWord("noDataLegend")):$this->getChartLine($feData);
        $template = str_replace("{feLegend}", $this->resource->getWord("feLegend"), $template);
        $template = str_replace("{feChart}", ((count($feData["values"]) == 0)?$fe["src"]:"<img src=".$fe["src"].">"), $template);
        $template = str_replace("{feTable}", $fe["table"], $template);
        
        $pb = (count($pbData["values"]) == 0)? array("src" => $this->resource->getWord("noDataLegend"),"table" => $this->resource->getWord("noDataLegend")):$this->getChartLine($pbData);
        $template = str_replace("{pbLegend}", $this->resource->getWord("pbLegend"), $template);
        $template = str_replace("{pbChart}", ((count($pbData["values"]) == 0)?$pb["src"]:"<img src=".$pb["src"].">"), $template);
        $template = str_replace("{pbTable}", $pb["table"], $template);
        
        $cu = (count($cuData["values"]) == 0)? array("src" => $this->resource->getWord("noDataLegend"),"table" => $this->resource->getWord("noDataLegend")):$this->getChartLine($cuData);
        $template = str_replace("{cuLegend}", $this->resource->getWord("cuLegend"), $template);
        $template = str_replace("{cuChart}", ((count($cuData["values"]) == 0)?$cu["src"]:"<img src=".$cu["src"].">"), $template);
        $template = str_replace("{cuTable}", $cu["table"], $template);
        
        $cr = (count($crData["values"]) == 0)? array("src" => $this->resource->getWord("noDataLegend"),"table" => $this->resource->getWord("noDataLegend")):$this->getChartLine($crData);
        $template = str_replace("{crLegend}", $this->resource->getWord("crLegend"), $template);
        $template = str_replace("{crChart}", ((count($crData["values"]) == 0)?$cr["src"]:"<img src=".$cr["src"].">"), $template);
        $template = str_replace("{crTable}", $cr["table"], $template);
        
        $al = (count($alData["values"]) == 0)? array("src" => $this->resource->getWord("noDataLegend"),"table" => $this->resource->getWord("noDataLegend")):$this->getChartLine($alData);
        $template = str_replace("{alLegend}", $this->resource->getWord("alLegend"), $template);
        $template = str_replace("{alChart}", ((count($alData["values"]) == 0)?$al["src"]:"<img src=".$al["src"].">"), $template);
        $template = str_replace("{alTable}", $al["table"], $template);
        
        $si = (count($siData["values"]) == 0)? array("src" => $this->resource->getWord("noDataLegend"),"table" => $this->resource->getWord("noDataLegend")):$this->getChartLine($siData);
        $template = str_replace("{siLegend}", $this->resource->getWord("siLegend"), $template);
        $template = str_replace("{siChart}", ((count($siData["values"]) == 0)?$si["src"]:"<img src=".$si["src"].">"), $template);
        $template = str_replace("{siTable}", $si["table"], $template);
        
        $vis100 = (count($vis100Data["values"]) == 0)? array("src" => $this->resource->getWord("noDataLegend"),"table" => $this->resource->getWord("noDataLegend")):$this->getChartLine($vis100Data);
        $template = str_replace("{vis100Legend}", $this->resource->getWord("vis100Legend"), $template);
        $template = str_replace("{vis100Chart}", ((count($vis100Data["values"]) == 0)?$vis100["src"]:"<img src=".$vis100["src"].">"), $template);
        $template = str_replace("{vis100Table}", $vis100["table"], $template);
        
        $hollin = (count($hollinData["values"]) == 0)? array("src" => $this->resource->getWord("noDataLegend"),"table" => $this->resource->getWord("noDataLegend")):$this->getChartLine($hollinData);
        $template = str_replace("{hollinLegend}", $this->resource->getWord("hollinLegend"), $template);
        $template = str_replace("{hollinChart}", ((count($hollinData["values"]) == 0)?$hollin["src"]:"<img src=".$hollin["src"].">"), $template);
        $template = str_replace("{hollinTable}", $hollin["table"], $template);
        
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
        unlink($fe);
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