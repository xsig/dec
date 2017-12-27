<?php 
namespace Dec\model;
use Dec\database\MongoDBConn as MongoDBConn;
use Dec\utils\Funciones as Funciones;
use Dec\error\MensajeError as MensajeError;

class Identidad {
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
    
    public function validaRutExiste($rut){
        $busqueda = array('rut' => $rut );
        $cursor = self::$ConnMDB->busca("identidad", $busqueda);
        foreach($cursor as $item )
        {
            return true;
        }
        return false;
    }
    
    public function enrolar($document){
        $doc_usuario = array(
            "rut" =>strtoupper($document['mensaje_dec']['mensaje']['rut']) , 
            "nombre" => strtoupper($document['mensaje_dec']['mensaje']['Nombre']),
            "apellidoPaterno" => strtoupper($document['mensaje_dec']['mensaje']['apellidoPaterno']),
            "apellidoMaterno" => strtoupper($document['mensaje_dec']['mensaje']['apellidoMaterno']),
            "huella" => $document['mensaje_dec']['mensaje']['huella']
        );

        $idIdentidad =  self::$ConnMDB->ingresa("identidad",$doc_usuario,"identidad_id");

        return $idIdentidad;
    }

    public function identificar($rut)
    {
        $_usuario = 0;
        $busqueda = array('rut' => $rut );
        $cursor = self::$ConnMDB->busca("identidad", $busqueda);
        foreach($cursor as $users){
            $_usuario = $users;
        }
        return $_usuario;
    }

    public function traeUsuarioPorId($id){
        $_usuario = 0;
        $busqueda = array('_id' => $id );
        $cursor = self::$ConnMDB->busca("identidad", $busqueda);
        foreach($cursor as $users){
            $_usuario = $users;
        }
        return $_usuario;
    }

    private function formatoRut($rut){
        return strtoupper(str_replace(".","",$rut));
    }
}