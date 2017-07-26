<?php

class db {

	private $mysqli;
	var $server = '';
	var $user = '';
	var $passwd = '';
	var $dbase ='';
	var $log;
	//var $conn = new stdClass();

	function __construct($server,$user,$passwd,$db)
	{

	    if (isset($GLOBALS["log"])){
	        $this->log = &$GLOBALS["log"];
	    }else{
	        $this->log = new log();
	    }

	    try
	    {
	        $this->mysqli = new mysqli ($server,$user,$passwd,$db);


	    }
	    catch (Exception $ex){

	        echo "Error initalising database...".$ex->getMessage();
	        exit;
	    }
	}

	private function modifStr($sql)
	{
		$what = array("[","]");
		return str_replace($what,"`",$sql);
	}


	function buildSql($string,$data=array())
	{

	    $pattern = "/\[([a-zA-Z_0-9-]+)\.([a-zA-Z_0-9]+)\]/";
	    $replacement = "`$1`.`$2`";
	    $string = preg_replace($pattern, $replacement, $string);

	    $pattern = "/\[([a-zA-Z_0-9-]+)\]/";

	    $replacement = "`$1`";
	    $string = preg_replace($pattern, $replacement, $string);


	    foreach ($data as $key=>$value){
	        $pattern1 = "/({".$key."\|s)}/";

	        if (preg_match($pattern1,$string)){

	            $replacement = "'{$this->mysqli->escape_string($value)}'";
	            //$this->mysqli->
	            $string = preg_replace($pattern1, $replacement,$string);
	        }

	        $pattern2 = "/({".$key."\|i)}/";

	        if (preg_match($pattern2,$string)){
	            $replacement = intval($value);
	            $string = preg_replace($pattern2, $replacement,$string);
	        }


	    }

	    return $string;
	}





	public function execute($sql)
	{

		$tmp = $this->mysqli->real_query($sql);

		if (!$tmp)
		{
			//trigger_error('Chyba SQL: ' . $sql . ' Error: ' . $this->mysqli->error, E_USER_ERROR);
			$this->log->logData('Chyba SQL: ' . $sql .'  Error: ' . $this->mysqli->error,false);

			return array("status"=>FALSE,"result"=>"Chyba SQL: ".$sql."  Error: ".$this->mysqli->error);
		}

		return array("status"=>TRUE,"result"=>"");

	}

	public function table($sql)
	{

	    $result = array("status"=>true,"result"=>array());
	    $data = array();



		if ($tmp = $this->mysqli->query($sql))
		{
			$this->log->logData($sql,false);
			$num_rows =$tmp->num_rows;

			for ($i=0; $i<$tmp->num_rows; $i++)
			{
				$tmp->data_seek($i);
				$row = $tmp->fetch_array(MYSQL_ASSOC);
				array_push($data,$row);
			}

			$result["status"] = true;
			$result["result"] = $data;
			$tmp->free_result();
		}
		else
		{

			//trigger_error('Chyba SQL: <p>' . $sql . '</p> Error: ' . $this->mysqli->error);
			$result['status'] = false;
			$result['result'] = "SQL:<p>{$sql}</p>, error:<p>{$this->mysqli->error}</p>";

			$this->log->logData('Chyba SQL: ' . $sql . ' Error: ' . $this->mysqli->error,000,true);

			$tmp->free_result();
		}

		//$tmp->close();
		return $result;

	}

	public function sql_count_rows($sql)
	{
		$result = array();
		$sql = $this->modifStr($sql);
		if ($tmp = $this->mysqli->query($sql))
		{
			$this->logData($sql);
			$result['rows'] = $tmp->num_rows;

		}
		else
		{
			trigger_error('Chyba SQL: ' . $sql . ' Error: ' . $this->mysqli->error, E_USER_ERROR);
			$result['error'] = "Error SQL: {$sql}, ".$this->mysqli->error;
			$this->logData('Chyba SQL: ' . $sql . ' Error: ' . $this->mysqli->error,000,true);
		}

		//print_r($result);
		return $result;
	}

	public function row($sql)
	{
		$result = array();

		if (strpos($sql,"LIMIT") == FALSE){
		    $sql .=" LIMIT 1";
		}

		$sql = $this->modifStr($sql);

		$tmp = $this->mysqli->query($sql);
		if ($tmp)
		{
			$row = $tmp->fetch_assoc();
			if (is_array($row))
			{
				foreach ($row as $key=>$value)
				{
					$result[$key] = $value;
				}
			}
		}
		else
		{
			//trigger_error("Error SQL: {$sql} <br> ".$this->mysqli->error);
			$result["result"] = "Error SQL: {$sql}<br> ".$this->mysqli->error;
			$result["status"] = FALSE;

			//$this->logData('Chyba SQL: ' . $sql . ' Error: ' . $this->mysqli->error,false);
		}
		//$tmp->free_result();
		//print_r($result);
		return $result;

	}
	/** vlozi novy riadok bez kontroly ci existuje **/
	public function insert_row_old($table,$data,$param)
	{
		//$this->openDb();
		$result = array();
		$colLen = count($data);
		$col_str = "";
		$col_val = "";
		$i=0;
		foreach ($data as $key=>$value)
		{
			if (($i+1) < $colLen)
			{
				$col_str .="`{$key}`,";
				$col_val .= "'{$this->mysqli->real_escape_string($value)}',";
			}
			else
			{
				$col_str .="`{$key}`";
				$col_val .= "'{$this->mysqli->real_escape_string($value)}'";
			}

			$i++;
		}

		if ($param == "REPLACE")
		{
		    $sql = sprintf("REPLACE INTO `%s` (%s) VALUES (%s)",$table,$col_str,$col_val);
		}
		else{
		    $sql = sprintf("INSERT %s INTO `%s` (%s) VALUES (%s)",$param,$table,$col_str,$col_val);
		}

		$tmp = $this->mysqli->query($sql);

		if (!$tmp)
		{
			$this->log->logData('Chyba SQL: ' . $sql . ' Error: ' . $this->mysqli->error,false);
			$result = array("status"=>FALSE,"result"=>'Chyba SQL: ' . $sql . ' Error: ' . $this->mysqli->error);
		}
		else
		{
			$this->log->logData($sql);
			$lastId = $this->mysqli->insert_id;
			$result = array("status"=>TRUE,"result"=>$lastId);
		}

		return $result;

	}

	function startTransaction()
	{
	    return mysqli_begin_transaction($this->mysqli, MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
	}

	function rollBackTransaction($trans)
	{
	    mysqli_rollback($this->mysqli);
	}

	function commitTransaction()
	{
	    mysqli_commit($this->mysqli);
	}


	function insert_row($table,$data)
	{

		$result = array();
		$colLen = count($data);
		$col_str = "";
		$col_val = "";
		$col_update = "";
		$i=0;

		foreach ($data as $key=>$value)
		{
			if (($i+1) < $colLen)
			{
				$col_str .="`{$key}`,";
				$col_val .= "'{$this->mysqli->real_escape_string($value)}',";
				$col_update .= sprintf(" `%s` = VALUES(`%s`), ",$key,$key);
			}
			else
			{
				$col_str .="`{$key}`";
				$col_val .= "'{$this->mysqli->real_escape_string($value)}'";
				$col_update .= sprintf(" `%s` = VALUES(`%s`)",$key,$key);
			}
			$i++;
		}

		$sql = sprintf("INSERT INTO `%s` (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s",$table,$col_str,$col_val,$col_update);

		//$sql = $this->mysqli->real_escape_string($sql);

		//echo $sql;
		//return;

		$tmp = $this->mysqli->query($sql);

		if ($tmp!=FALSE){

		    $result['status'] = TRUE;
		    $result["result"] = $this->mysqli->insert_id;
		    $result['last_id'] = $this->mysqli->insert_id;

		}else
		{
		    $result['result'] = 'Chyba SQL: ' . $sql . ' Error: ' . $this->mysqli->error;
		    $this->log->logData('Chyba SQL: ' . $sql . ' Error: ' . $this->mysqli->error,false);
		    $result['status'] = FALSE;
		}



		return $result;
	}
}

?>