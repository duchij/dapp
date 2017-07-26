<?php
/**
 * Interface pre pracu s db na rozdelenie
 * SQLite, MySQL
 *
 * @author bduchaj
 *
 */
interface iDatabase{



    /**
     * Creates the link to db in the constructor
     *
     * @param array $data
     */
    public function open($data);

    public function row ($data);

    public function table ($data);

    public function insert_row($table, $data, $param);

    public function buildSql($string,$data=array());

    public function execute($data);

    public function startTransaction();

    public function rollBackTransaction();

    public function commitTransaction();

    public function insert_row_old($table, $data, $param);

    public function insert_rows($table,$data,$parameter);


}



?>