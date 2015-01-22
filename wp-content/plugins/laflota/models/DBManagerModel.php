<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');
*/
require_once $pluginPath . "/helpers/DBManager.php";
require_once $pluginPath . "/helpers/mailSender.php";
abstract class DBManagerModel extends DBManager{
        public $mail;
        public $resource;
        function __construct() {
            parent::__construct();
            global $resource;
            $this->mail = new mailSender();
            $this->resource = $resource;
        }
        
        public function sendAssignedMail($user, $id, $type){
            $assignedUserData = $this->getUserdata($user);
            $this->mail->PQRAssigned($assignedUserData->data->display_name
                                         , $type
                                         , $id
                                         , $assignedUserData->data->user_email
                                    );
        }
        
        public function formatDate($date){
            $dateParts = array();
            $dateFormated = $date;
            if(substr_count($date, '/') > 0){
                $dateParts = explode("/",$date);
                $dateFormated = $dateParts[2]."-".$dateParts[0]."-".$dateParts[1];
            }
            return $dateFormated;
        }
        
        public function getRelationshipDescriptionData($references, $filter){
            $query = "SELECT "  . $references["text"] . " Name "
                    . " FROM ". $references["table"]
                    . " WHERE " . $references["id"] ." = " . $filter;
            return $this->get($query, "var");
        }
        
        public function validatorFile($arrayFile, $arrayHeader){
            $msj = "";
            $countheader = count($arrayHeader);
            $arrayResult = array();
            foreach ($arrayFile as $num_linea => $linea) {
                $cols = explode(";", $linea);
                $numCols = count($cols);

                if($numCols != $countheader )
                {
                    $msj .= "Error en la línea " . $num_linea."(".$cols[0]."): ".$numCols." columnas, deben ser ". $countheader ." columnas\n";
                }

                if($num_linea == 0){
                    for($i = 0; $i < $countheader; $i++){
                        if(strtolower($arrayHeader[$i]) != strtolower(trim((empty($cols[$i]))? "NULL" :$cols[$i]))){
                            $msj .= "Error en la linea " . $num_linea.": ".$arrayHeader[$i] ." diferente a ". $cols[$i]."\n";
                        }
                    }
                }
                else{
                    foreach ($cols as $num_col => $col){
                        $cols[$num_col] = addslashes(trim($col));
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
        
        function __destruct() {}
        
	abstract protected function getList($params = array());
	abstract protected function add();
	abstract protected function edit();
	abstract protected function del();
        abstract protected function detail();
}
?>
