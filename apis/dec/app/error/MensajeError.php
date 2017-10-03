<?php
namespace Dec\error;
use Dec\database as Database;
use Dec\database\MongoDBConn as MongoDBConn;

class MensajeError {
    private static $ConnMDB;
    private $coll;
    private static $errCod;
    private static $errNombre;
    private static $errDescripcion;
    private static $errVars;
    private static $glosaEstado;

    public function __construct(){
        self::$ConnMDB = new MongoDBConn();
        $this->coll = "errores";
        self::$glosaEstado="Operación con errores de Negocio";
    }


    public function grabarMensajeAnterior($documento,$tipo,$modelo,$error, $campo=NULL, $variable=NULL){
        $busqueda = array();
        $busqueda['tipo'] = $tipo;
        $busqueda['errNombre'] = $error;

        if($tipo != "Sistema"){
            $busqueda['modelo'] = $modelo;
        }

        if($campo != NULL && $tipo == "Campo"){
            $busqueda['errCampo'] = $campo;
        }

        if($error != "conexionErr"){
            $cursor = self::$ConnMDB->busca($this->coll,$busqueda);
            if($cursor->count()>0){
                foreach ($cursor as $result) { 
                    $_error[] = $result;
                }
                self::$errCod = $_error[0]['errCod'];
                if ($variable == NULL){
                    $variable = "";
                }
                self::$errDescripcion = str_replace("%var",$variable,$_error[0]['errDescripcion']);
                
            }
            else{
                $tipo = "Sistema";
                self::$errCod = 5;
                self::$errDescripcion = "Error interno en el servicio, error $error no se encuentra declarado en la BD";
            }
        }
        else{
            $tipo="Sistema";
            self::$errCod = 1;
            self::$errDescripcion = "Error interno en la conexion a la BD";
        }
        $error_arr = array();
        $error_arr['errCod'] = self::$errCod;
        $error_arr['errDescripcion'] = self::$errDescripcion;


        switch ($tipo) {
            case 'Sistema':
            $documento['mensaje_dec']['header']['listaDeErroresSistema'][] = $error_arr;
            $documento['mensaje_dec']['header']['glosaEstado'] = "Errores de Sistema";
            break;
            case 'Negocio':
            $documento['mensaje_dec']['header']['listaDeErroresNegocio'][] = $error_arr;
            $documento['mensaje_dec']['header']['glosaEstado'] = "Errores de Negocio";
            break;      
            case 'Campo':
            $error_arr['errCampo'] = $campo;     
            $documento['mensaje_dec']['header']['listaDeErroresCampo'][] = $error_arr;
            $documento['mensaje_dec']['header']['glosaEstado'] = "Errores en los Campos";
            break;      
            default:
                # code...
            break;
        }
        if ($tipo != "Sistema" && $error_arr['errCod'] != 10000)
            $documento['mensaje_dec']['header']['estado'] = 1;

        return $documento;
    }

    public function grabarMensaje($documento, $error, $campo=NULL, $variable=NULL){
        $busqueda = array();
        $tipo = "";
        $busqueda['errNombre'] = $error;

        if ($error == "baseDatos"){
            $tipo ="Sistema";
        }

        if($error != "conexionErr"){
            $cursor = self::$ConnMDB->busca($this->coll,$busqueda);
            $contador = self::$ConnMDB->count($this->coll,$busqueda);
            if($contador>0){
                foreach ($cursor as $result) { 
                    $_error = $result;
                }
                self::$errCod = $_error->errCod;
                $modelo = $_error->modelo;
                $tipo = $_error->tipo;

                if ($variable == NULL){
                    $variable = "";
                }
                self::$errDescripcion = str_replace("%var",$variable,$_error->errDescripcion);
                
            }
            else{
                $tipo = "Sistema";
                $modelo = "BaseDatos";
                self::$errCod = 5;
                self::$errDescripcion = "Error interno en el servicio, error $error no se encuentra declarado en la BD";
            }
        }
        else{
            $tipo="Sistema";
            self::$errCod = 1;
            self::$errDescripcion = "Error interno en la conexion a la BD";
        }
        $error_arr = array();
        $error_arr['errCod'] = self::$errCod;
        $error_arr['errDescripcion'] = self::$errDescripcion;


        switch ($tipo) {
            case 'Sistema':
            $documento['mensaje_dec']['header']['listaDeErroresSistema'][] = $error_arr;
            $documento['mensaje_dec']['header']['glosaEstado'] = "Errores de Sistema";
            break;
            case 'Negocio':
            $documento['mensaje_dec']['header']['listaDeErroresNegocio'][] = $error_arr;
            $documento['mensaje_dec']['header']['glosaEstado'] = "Errores de Negocio";
            break;      
            case 'Campo':
            $error_arr['errCampo'] = $campo;     
            $documento['mensaje_dec']['header']['listaDeErroresCampo'][] = $error_arr;
            $documento['mensaje_dec']['header']['glosaEstado'] = "Errores en los Campos";
            break;      
            default:
                # code...
            break;
        }
        if ($tipo != "Sistema" && $error_arr['errCod'] != 10000)
            $documento['mensaje_dec']['header']['estado'] = 1;

        return $documento;
    }
    

}

?>