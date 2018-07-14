<?php

	include('connection_strings.php');
	
	class connection{
	
		private $link;
		
		const SELECT = 1;
		const INSERT = 2;
		const UPDATE = 3;
		const DELETE = 4;
		
		public function __construct(){
			$this->link = new mysqli(host,user,pass,db) or die("Error " . $link->error);
		}
		
		private function db_fetch_array($result){
			return $result->fetch_array(MYSQLI_ASSOC);
		}
		
		public function real_escape_string($string){
			return mysqli_real_escape_string($this->link, $string);
		}

		public function db_query($query, $params = [], $type = $this::SELECT){
				if($type == $this::SELECT){
					$q_res = $this->link->query($query) or die('Error: ' . $this->link->error);
					$result = array();
					while($x = $this->db_fetch_array($q_res)){
						$result[] = $x;
					}
					return $result;
				} else{
					return $this->execStmt($query, $params);
				}
		}
		
		public function verifyIfExists($table, $column, $value){
			return (count($this->db_query("select * from $table where $column = '$value'")) > 0);
		}
		
		private function verifyIfExists1($table, $column1, $column2, $value1, $value2){
			return (count($this->db_query("select * from $table where $column1 = '$value1' AND $column2 = '$value2'")) > 0);
		}
		
		public function count_table_values($table, $column = "", $value = ""){
			if(strcmp($column, "") == 0)
				return count($this->db_query("select * from $table"));
			return count($this->db_query("select * from $table where $column = $value"));
		}
		
		public function execStmt($query, $params){
			$_params = array();
			$_params_type = "";
			$stmt = $this->link->prepare($query);
			if($stmt === false) {
				trigger_error('Wrong SQL: ' . $query . ' Error: ' . $this->link->errno . ' ' . $this->link->error, E_USER_ERROR);
				return -1;
			}
			foreach($params as $param){
				switch(gettype($param)){
					case "integer" :
						$_params_type .= "i";
						break;
					case "double" :
						$_params_type .= "d";
						break;
					case "string";
						$_params_type .= "s";
						break;
				}
			}
			$_params[] = &$_params_type;
			$n = count($params);
			for($i = 0; $i < $n; $i++){
				$_params[] = &$params[$i];
			}
			call_user_func_array(array($stmt, 'bind_param'), $_params);
			$stmt->execute();
			$result = $stmt->get_result();
			$stmt->close();
			return $result;
		}
		
		public function __destruct(){
			$this->link->close();
		}
		
	}
?>