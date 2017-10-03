<?php
namespace Dec\acciones;
use Dec\database as Database;
use Dec\database\MongoDBConn as MongoDBConn;
use Dec\config\ConfigData as ConfigData; 

class AdminstradorAcciones {
    private static $ConnMDB;
    private $coll;
    private static $host;
    private static $port;
    private static $dbname;
    private static $user;
    private static $pass;

	public function __construct(){
        $this->getVarsDB();
        self::$ConnMDB = new MongoDBConn($this->host, $this->port, $this->dbname, $this->user, $this->pass);
        $this->coll = "acciones";
    }

    private function getVarsDB(){
        $this->dataDB = new ConfigData();
        $this->host = $this->dataDB->dbData['host'];
        $this->port = $this->dataDB->dbData['port'];
        $this->dbname = $this->dataDB->dbData['dbname'];
        $this->user = $this->dataDB->dbData['user'];
        $this->pass = $this->dataDB->dbData['pass'];
    }
    
}