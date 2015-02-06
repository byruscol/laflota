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
        
        public function checkFormaDate($date, $format){
            $formatOk = false;
            $date = trim($date);
            $format = stripslashes($format);
            $separator = (strpos($format,"/") !== false)? "/":"-";
            $dateParts = explode($separator,$date);
            $formatParts = explode($separator,$format);
            
            if(count($dateParts) == 3)
            {
                $dayPosition =  array_search('dd', $formatParts);
                $monthPosition = array_search('mm', $formatParts);
                $yearPosition = array_search('yyyy', $formatParts);
                if(empty($dayPosition))
                    $dayPosition = array_search('d', $formatParts);
                
                if(empty($monthPosition))
                    $monthPosition = array_search('m', $formatParts);
                
                if(empty($yearPosition))
                    $yearPosition = array_search('yy', $formatParts);
                 
                $formatOk = checkdate($dateParts[$monthPosition], $dateParts[$dayPosition], $dateParts[$yearPosition]);
            }
            
            return $formatOk;
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
                        if(strtolower($arrayHeader[$i]["column"]) != strtolower(trim((empty($cols[$i]))? "NULL" :$cols[$i]))){
                            $msj .= "Error en la linea " . $num_linea.": ".$arrayHeader[$i]["column"] ." diferente a ". $cols[$i]."\n";
                        }
                    }
                }
                else{
                    foreach ($cols as $num_col => $col){
                        $col = trim($col);
                        if($arrayHeader[$num_col]["required"] && (is_null($col) || $col == ""))
                            $msj .= "Error en la linea " . $num_linea.": ".$arrayHeader[$num_col]["column"] ." es requerid@ \n";
                        else{
                            switch ($arrayHeader[$num_col]["type"]){
                                case "string":  
                                        if($arrayHeader[$num_col]["required"] && (is_null($col) || strlen($col) < 1))
                                            $msj .= "Error en la linea " . $num_linea.": ".$arrayHeader[$num_col]["column"] ." (".$col.") no es un texto válido \n";
                                        else
                                            $col = (is_null($col) || $col == "")? "NULL": $col;
                                    break;
                                case "number":
                                        $col = str_replace(",", ".", $col);
                                        if($arrayHeader[$num_col]["required"] && (is_null($col) || !is_numeric($col)))
                                            $msj .= "Error en la linea " . $num_linea.": ".$arrayHeader[$num_col]["column"] ." (".$col.") no es un número válido \n";
                                        else
                                            $col = (is_null($col) || $col == "")? "NULL": (is_numeric($col))? $col: $msj .= "Error en la linea " . $num_linea.": ".$arrayHeader[$num_col]["column"] ." (".$col.") no - es un número válido \n";;
                                    break;
                                case "option":
                                        if($arrayHeader[$num_col]["required"] && is_null($col) || (!in_array($col, $arrayHeader[$num_col]["options"])))
                                            $msj .= "Error en la linea " . $num_linea.": ".$arrayHeader[$num_col]["column"] ." (".$col.") no es una opcion válida (".(implode(",",$arrayHeader[$num_col]["options"])).") \n";
                                        else
                                            $col = (is_null($col) || $col == "")? "NULL": (in_array($col, $arrayHeader[$num_col]["options"]))? $col:$msj .= "Error en la linea " . $num_linea.": ".$arrayHeader[$num_col]["column"] ." (".$col.") no es una opcion válida (".(implode(",",$arrayHeader[$num_col]["options"])).") \n";;
                                    break;                            
                                case "date":
                                        if($arrayHeader[$num_col]["required"] && (is_null($col) || !$this->checkFormaDate($col, $arrayHeader[$num_col]["format"])))
                                            $msj .= "Error en la linea " . $num_linea.": ".$arrayHeader[$num_col]["column"] ." (".$col.") no es una fecha válida \n";
                                        else
                                            $col = (is_null($col) || $col == "")? "NULL": ($this->checkFormaDate($col, $arrayHeader[$num_col]["format"]))? $col:$msj .= "Error en la linea " . $num_linea.": ".$arrayHeader[$num_col]["column"] ." (".$col.") no es una fecha válida \n";;
                                    break;
                                case "email":
                                        if($arrayHeader[$num_col]["required"] && !filter_var($col, FILTER_VALIDATE_EMAIL))
                                            $msj .= "Error en la linea " . $num_linea.": ".$arrayHeader[$num_col]["column"] ." (".$col.") no es e-mail válido \n";
                                        else
                                            $col = (is_null($col) || $col == "")? "NULL": (filter_var($col, FILTER_VALIDATE_EMAIL))? $col:$msj .= "Error en la linea " . $num_linea.": ".$arrayHeader[$num_col]["column"] ." (".$col.") no es e-mail válido \n";
                                    break;
                            }

                            $cols[$num_col] = addslashes(trim($col));
                        }
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
