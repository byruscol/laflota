<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once('DBManagerModel.php');
class clientesUsuarios extends DBManagerModel{
   
    public function getList($params = array()){
        $entity = $this->entity();
        $start = $params["limit"] * $params["page"] - $params["limit"];
        $query = "SELECT clientesUsuariosId, ID
                    FROM ".$entity["tableName"]." i"
                . " WHERE clienteId = ".$params["filter"];
        
        if(array_key_exists('where', $params)){
            if (is_array( $params["where"]->rules )){
                $countRules = count($params["where"]->rules);
                for($i = 0; $i < $countRules; $i++){
                    switch($params["where"]->rules[$i]->field ){
                        case "Usuario": $params["where"]->rules[$i]->field = "ID"; break;
                    }
                }
            }
            
           $query .= " AND (". $this->buildWhere($params["where"]) .")";
        }
        
        return $this->getDataGrid($query, $start, $params["limit"] , $params["sidx"], $params["sord"]);
    }    
    
    public function detail(){}
    
    public function add(){
        
        $this->addRecord($this->entity(), $_POST, array("clienteId" => $_POST["parentId"],"date_entered" => date("Y-m-d H:i:s"), "created_by" => $this->currentUser->ID));
    }
    public function edit(){ 
        $this->updateRecord($this->entity(), $_POST, array("clientesUsuariosId" => $_POST["clientesUsuariosId"]), null, null);
    }
    public function del(){
        $this->eliminateRecord($this->entity(), array("clientesUsuariosId" => $_POST["id"]));
    }

    public function entity($CRUD = array())
    {
        $data = array(
                    "tableName" =>  $this->pluginPrefix."clientesUsuarios"
                    ,"entityConfig" => $CRUD
                    ,"atributes" => array(
                        "clientesUsuariosId" => array("label"=>"id","type" => "int", "PK" => 0, "required" => false, "readOnly" => true, "autoIncrement" => true, "toolTip" => array("type" => "cell", "cell" => 2) )
                        ,"ID" => array("label"=>"usuario","type" => "int", "required" => true, "references" => array("table" => $this->wpPrefix."users", "id" => "ID", "text" => "display_name"))
                        ,"parentId" => array("type" => "int","required" => false, "hidden" => true, "isTableCol" => false)
                        ,"parentRelationShip" => array("type" => "varchar","required" => false, "hidden" => true, "isTableCol" => false)
                    )
                );  
        return $data;
    }
}
?>