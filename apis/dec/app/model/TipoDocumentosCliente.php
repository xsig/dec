<?php
namespace Dec\model;
use Dec\database\MongoDBConn as MongoDBConn;

class TipoDocumentosCliente {
	private static $ConnMDB;
	private $coll;
	public function __construct(){
		self::$ConnMDB = new MongoDBConn();
	}
	

	public function traeListaRutClientePorTipoDocumentos($idTipoDocumento){
		$_Mclientes = new Clientes();
		$_cliente="";
		$busqueda = array("_id" => $idTipoDocumento , "estado" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("tipoDocumentoCliente", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
					$_cliente =$item->empresasSolicitadas;
			}
		//}
	
		return $_cliente;
	}

	public function existeTipoDocumentoCliente($idCliente, $idTipoDocumento){
		$busqueda = array(
				"idTipoDocumento" => $idTipoDocumento ,
				"idCliente" => $idCliente
			);
		$contador = self::$ConnMDB->count("tipoDocumentoCliente", $busqueda);
		if($contador > 0 ){
			return true;
		}
		return false;
	}

	public function existeTipoDocumento($busqueda){
		$contador = self::$ConnMDB->count("tipoDocumentoCliente", $busqueda);
		if($contador > 0 ){
			return true;
		}
		return false;
	}

	public function ingresaTipoDocumentoCliente($idCliente, $idTipoDocumento){
		$documento = array(
    		"idCliente" =>  $idCliente,
    		"idTipoDocumento" =>  $idTipoDocumento,
			"estado"  =>  "ACTIVO"
    	);

        $_id =  self::$ConnMDB->ingresa("tipoDocumentoCliente",$documento,"tipoDocumentoCliente_id");
	}
}

	?>