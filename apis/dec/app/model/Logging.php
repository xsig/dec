<?php 
namespace Dec\model;
use Dec\database\MongoDBConn as MongoDBConn;
use Dec\utils\Funciones as Funciones;
use Dec\error\MensajeError as MensajeError;
//require_once '/usr/share/php/Mail.php';

class Logging {
    private static $ConnMDB;
    private $coll;
    private $salida;
    private $valid;
    private $func;
    private $Mensaje;

    public function __construct(){
        try{
            self::$ConnMDB = new MongoDBConn();
        }catch(MongoConnectionException $e){
            self::$ConnMDB = false;
        }catch(MongoException $e){
            self::$ConnMDB = false;
        }  
        $this->func = new Funciones();
        $this->Mensaje = new MensajeError();
    }
    
    public function validaConexion(){
        if(self::$ConnMDB)
            return true;
        else
            return false;
    }
    
    public function guardaDocumentoEntrada($document){
        //$id = self::$ConnMDB->ingresa("log",$document,"log_id");
    }
}