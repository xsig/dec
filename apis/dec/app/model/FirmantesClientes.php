<?php
namespace Dec\model;
use Dec\database\MongoDBConn as MongoDBConn;

class FirmantesClientes {
	private static $ConnMDB;
	private $coll;

	public function __construct(){
		self::$ConnMDB = new MongoDBConn();
	}
	
	public function ingresaFirmantesClientes($firmante, $cliente){
		return $this->agregaFirmanteCliente($idFirmante,$idCliente);
	}

	public function existeFirmanteNombreCliente($nombreFirmante, $idCliente){
		$firmante = new Firmantes();
		$busqueda = array(
				"idCliente" => $idCliente 
			);
		$cursor = self::$ConnMDB->busca("FirmanteCliente", $busqueda);
		foreach ($cursor as $value) {
			$idFirmante = $value['idFirmante'];
			if (strtolower($firmante->traeNombrePorIdFirmante($idFirmante)) == strtolower($nombreFirmante)){
				return true;
			}
		}
		return false;
	}

	public function existeFirmanteCliente($idFirmante, $idCliente){
		$busqueda = array(
				"idFirmante" => $idFirmante ,
				"idCliente" => $idCliente
			);
		$contador = self::$ConnMDB->count("FirmanteCliente", $busqueda);
		if($contador > 0 ){
			return true;
		}
		return false;
	}

	public function agregaFirmanteCliente($idFirmante,$idCliente,$orden){
		$doc_FirmanteCliente = array(
    		"idFirmante" =>  $idFirmante,
    		"idCliente" =>  $idCliente,
    		"orden" => $orden
    	);
		$_id =  self::$ConnMDB->ingresa("FirmanteCliente",$doc_FirmanteCliente,"FirmanteCliente_id");
		return $_id;
	}

	public function traeIdFirmantePorNombre($nombreFirmante, $idCliente){
		$firmante = new Firmantes();
		$busqueda = array(
				"idCliente" => $idCliente 
			);
		$cursor = self::$ConnMDB->busca("FirmanteCliente", $busqueda);
		foreach ($cursor as $value) {
			$idFirmante = $value['idFirmante'];
			if (strtolower($firmante->traeNombrePorIdFirmante($idFirmante)) == strtolower($nombreFirmante)){
				return $value['_id'];
			}
		}
		return 0;
	}

}

?>