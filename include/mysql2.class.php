<?php

class mysql2 implements iDatabase{

    var $dbLink;

    var $log;

    function __construct($data){

        if (isset($GLOBALS["log"])){

            $this->log = &$GLOBALS["log"];

        }else{

            $this->log = new log();

        }

        $this->open($data);



    }


    public function open($data){

        $this->dbLink = new mysqli ($data["server"],$data["user"], $data["passwd"],$data["db"]);


    }

    public function buildSql($string,$data=array())
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

                $replacement = "'{$this->dbLink->escape_string($value)}'";
                //$this->dbLink->
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

        $tmp = $this->dbLink->real_query($sql);

        if (!$tmp)
        {
            //trigger_error('Chyba SQL: ' . $sql . ' Error: ' . $this->dbLink->error, E_USER_ERROR);
            $this->log->logData('Chyba SQL: ' . $sql .'  Error: ' . $this->dbLink->error,false);

            return array("status"=>FALSE,"result"=>"Chyba SQL: ".$sql."  Error: ".$this->dbLink->error);
        }

        return array("status"=>TRUE,"result"=>"");

    }

    public function table($sql)
    {

        $result = array("status"=>true,"result"=>array());
        $data = array();



        if ($tmp = $this->dbLink->query($sql))
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

            //trigger_error('Chyba SQL: <p>' . $sql . '</p> Error: ' . $this->dbLink->error);
            $result['status'] = false;
            $result['result'] = "SQL:<p>{$sql}</p>, error:<p>{$this->dbLink->error}</p>";

            $this->log->logData('Chyba SQL: ' . $sql . ' Error: ' . $this->dbLink->error,000,true);

            $tmp->free_result();
        }

        //$tmp->close();
        return $result;

    }

    public function row($sql)
    {
        $result = array();

        if (strpos($sql,"LIMIT") == FALSE){
            $sql .=" LIMIT 1";
        }

        $sql = $this->buildSql($sql);

        $tmp = $this->dbLink->query($sql);
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
            //trigger_error("Error SQL: {$sql} <br> ".$this->dbLink->error);
            $result["result"] = "Error SQL: {$sql}<br> ".$this->dbLink->error;
            $result["status"] = FALSE;

            //$this->logData('Chyba SQL: ' . $sql . ' Error: ' . $this->dbLink->error,false);
        }
        //$tmp->free_result();
        //print_r($result);
        return $result;

    }


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
                $col_val .= "'{$this->dbLink->real_escape_string($value)}',";
            }
            else
            {
                $col_str .="`{$key}`";
                $col_val .= "'{$this->dbLink->real_escape_string($value)}'";
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

        $tmp = $this->dbLink->query($sql);

        if (!$tmp)
        {
            $this->log->logData('Chyba SQL: ' . $sql . ' Error: ' . $this->dbLink->error,false);
            $result = array("status"=>FALSE,"result"=>'Chyba SQL: ' . $sql . ' Error: ' . $this->dbLink->error);
        }
        else
        {
            $this->log->logData($sql);
            $lastId = $this->dbLink->insert_id;
            $result = array("status"=>TRUE,"result"=>$lastId);
        }

        return $result;

    }

    function startTransaction()
    {
        return mysqli_begin_transaction($this->dbLink, MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
    }

    function rollBackTransaction()
    {
        mysqli_rollback($this->dbLink);
    }

    function commitTransaction()
    {
        mysqli_commit($this->dbLink);
    }


    function insert_row($table,$data,$param)
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
                $col_val .= "'{$this->dbLink->real_escape_string($value)}',";
                $col_update .= sprintf(" `%s` = VALUES(`%s`), ",$key,$key);
            }
            else
            {
                $col_str .="`{$key}`";
                $col_val .= "'{$this->dbLink->real_escape_string($value)}'";
                $col_update .= sprintf(" `%s` = VALUES(`%s`)",$key,$key);
            }
            $i++;
        }

        $sql = sprintf("INSERT INTO `%s` (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s",$table,$col_str,$col_val,$col_update);

        //$sql = $this->dbLink->real_escape_string($sql);

        //echo $sql;
        //return;

        $tmp = $this->dbLink->query($sql);

        if ($tmp!=FALSE){

            $result['status'] = TRUE;
            $result["result"] = $this->dbLink->insert_id;
            $result['last_id'] = $this->dbLink->insert_id;

        }else
        {
            $result['result'] = 'Chyba SQL: ' . $sql . ' Error: ' . $this->dbLink->error;
            $this->log->logData('Chyba SQL: ' . $sql . ' Error: ' . $this->dbLink->error,false);
            $result['status'] = FALSE;
        }



        return $result;
    }

    public function insert_rows($table, $data,$param){

    }



}

?>