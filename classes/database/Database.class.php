<?php
class Database
{
    private $HOST_NAME = 'localhost';
    private $DB_NAME = 'coupe_trosieme_infos';
    private $USER_NAME = 'root';
    private $PASSWORD = '';
    private $options = [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8"];
    private $connection = NULL;
    private $dsn = NULL;

    protected function getConnection()
    {
        $this->dsn = 'mysql:host=' . $this->HOST_NAME . ';dbname=' . $this->DB_NAME;

        try {
            $this->connection = new PDO($this->dsn, $this->USER_NAME, $this->PASSWORD, $this->options);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            return $this->connection;
        } catch (PDOException $ex) {
            die('Error : ' . $ex);
        }
    }
}
