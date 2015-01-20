<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class extensiones extends DBManagerModel{
   
    public function getList($params = array()){
        $entity = $this->entity();
        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT `extensionId`,
                        `kilometraje`
                    FROM ".$entity["tableName"]." i"
                . " WHERE vehiculoId = ".$params["filter"];
        
        if(array_key_exists('where', $params)){
            if (is_array( $params["where"]->rules )){
                $countRules = count($params["where"]->rules);
                for($i = 0; $i < $countRules; $i++){
                    switch($params["where"]->rules[$i]->field ){
                        case "vehiculo": $params["where"]->rules[$i]->field = "vehiculoId"; break;
                    }
                }
            }
            
           $query .= " AND (". $this->buildWhere($params["where"]) .")";
        }
        
        return $this->getDataGrid($query, $start, $params["limit"] , $params["sidx"], $params["sord"]);
    }    
    
    public function detail(){}
    
    public function add(){
        $this->addRecord($this->entity(), $_POST, array("vehiculoId" => $_POST["parentId"],"date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
    }
    public function edit(){ 
        $this->updateRecord($this->entity(), $_POST, array("extensionId" => $_POST["extensionId"]), null, null);
    }
    public function del(){
        $this->eliminateRecord($this->entity(), array("extensionId" => $_POST["id"]));
    }

    public function entity($CRUD = array())
    {
        $data = array(
                    "tableName" => $this->pluginPrefix."extensiones"
                    ,"entityConfig" => $CRUD
                    ,"atributes" => array(
                        "extensionId" => array("type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true, "toolTip" => array("type" => "cell", "cell" => 2) )
                        ,"kilometraje" => array("type" => "int", "required" => true)
                        ,"parentId" => array("type" => "int","required" => false, "hidden" => true, "isTableCol" => false)
                        ,"parentRelationShip" => array("type" => "varchar","required" => false, "hidden" => true, "isTableCol" => false)
                    )
                );  
        return $data;
    }
}
?>