<?php
namespace Dec\model;
use Dec\database\MongoDBConn as MongoDBConn;

class Firmantes {
	private static $ConnMDB;
	private $coll;

	public function __construct(){
		self::$ConnMDB = new MongoDBConn();
	}


	public function traeNombrePorIdFirmante($idFirmante){
		$nombre = "";
		$busqueda = array(
				"_id" => $idFirmante 
			);
		$cursor = self::$ConnMDB->busca("Firmantes", $busqueda);
		foreach ($cursor as $value) {
			$nombre = $value['nombrePerfil'];
		}
		return $nombre;
	}

	public function existeFirmanteEnEmpresa($nombrePerfil, $idCliente){
		$busqueda = array(
				"idCliente" => $idCliente 
			);
		$cursor = self::$ConnMDB->busca("FirmanteCliente", $busqueda);
		foreach ($cursor as $value) {
			$nombreFirmante = $this->traeNombrePorIdFirmante($value->idFirmante);
			if(strtolower($nombreFirmante) == strtolower($nombrePerfil)){
				return true;
			}
		}
		return false;
	}

	public function agregaFirmante($nombrePerfil,$descripcionPerfil ,$idSubTipoDoc, $idCliente){
		$doc_Firmante = array(
    		"nombrePerfil" =>  $nombrePerfil,
    		"descripcionPerfil" =>  $descripcionPerfil,
    	    "idSubTipoDoc"	=> $idSubTipoDoc, 
    	    "idCliente" => $idCliente,
    		"estado" =>  "ACTIVO"
    	);
		$_id =  self::$ConnMDB->ingresa("firmantes",$doc_Firmante,"firmantes_id");	
		return $_id;	
	}

	public function traeFirmanteEnEmpresa($nombrePerfil, $idCliente){
		$busqueda = array(
				"idCliente" => $idCliente 
			);
		$cursor = self::$ConnMDB->busca("FirmanteCliente", $busqueda);
		foreach ($cursor as $value) {
			$nombreFirmante = $this->traeNombrePorIdFirmante($value->idFirmante);
			if(strtolower($nombreFirmante) == strtolower($nombrePerfil)){
				return $value['idFirmante'];
			}
		}
		return 0;
	}

	public function existenFirmantesMismoOrden($arregloFirmantes, $orden){
		foreach ($arregloFirmantes as $firmante) {
			if ($firmante['orden'] == $orden){
				if ($firmante['estadoFirma'] == "DISPONIBLE FIRMA"){
					return true;
				}
			}
		}
		return false;
	}


	public function actualizaEstadosFirmantes($arregloFirmantes, $orden){
		$new_orden = $orden + 1;
		$resArregloFirmantes = array();
		foreach ($arregloFirmantes as $firmante) {
			if ($firmante['orden'] == $new_orden){
				if ($firmante['estadoFirma'] == "PENDIENTE FIRMA"){
					$firmante['estadoFirma'] = "DISPONIBLE FIRMA";
				}
			}
			$resArregloFirmantes[] = $firmante;
		}
		return $resArregloFirmantes;
	}



	public function estaFirmadoFirmantes($arregloFirmantes){
		foreach ($arregloFirmantes as  $firmante) {
			if ($firmante['estadoFirma'] != "FIRMADO"){
				return false;
			}
		}
		return true;
	}

	public function firmarDocumento($idAcepta,$codigoFirma,$rutFirmante,$nombreFirmante,$nombrePerfilFirmante,$descripcionFirmante){
		$_documentos = new Documentos();
		$new_firmante = array();
		$tmp_firmante = array();
		$datosAct =array();
		$documento = $_documentos->traeDocumentoPorIdAcepta($idAcepta);
		$idDoc = $documento->_id;
		$lastFirmante = 0 ;
		foreach ($documento->firmantes as  $firmante) {
			$tmp_firmante['rutFirmante'] = $firmante->rutFirmante;
			$tmp_firmante['nombreFirmante'] = $firmante->nombreFirmante;
			$tmp_firmante['nombrePerfil'] = $firmante->nombrePerfil;
			$tmp_firmante['descripcionPerfil'] = $firmante->descripcionPerfil;
			$tmp_firmante['fechaFirma'] = $firmante->fechaFirma;
			$tmp_firmante['estadoFirma'] = $firmante->estadoFirma;
			$tmp_firmante['codigoFirma'] = $firmante->codigoFirma;
			$tmp_firmante['orden'] = $firmante->orden;	

			if ($nombrePerfilFirmante == $firmante->nombrePerfil && $descripcionFirmante == $firmante->descripcionPerfil && $firmante->estadoFirma == "DISPONIBLE FIRMA"){
				$tmp_firmante['rutFirmante'] = $rutFirmante;
				$tmp_firmante['nombreFirmante'] = $nombreFirmante;
				$tmp_firmante['nombrePerfil'] = $nombrePerfilFirmante;
				$tmp_firmante['descripcionPerfil'] = $descripcionFirmante;
				$tmp_firmante['fechaFirma'] = date("Y-m-d H:i:s");
				$tmp_firmante['estadoFirma'] = "FIRMADO";
				$tmp_firmante['codigoFirma'] = $codigoFirma;
				$lastFirmante = $firmante->orden;			
			}

			$new_firmante[] = $tmp_firmante;
		}

		if ($lastFirmante > 0){

			$datosAct['firmantes'] =  $new_firmante;
			$cursor = self::$ConnMDB->actualizaPorId("documentos", $idDoc, $datosAct);	

			if(!$this->existenFirmantesMismoOrden($new_firmante, $lastFirmante) && $lastFirmante > 0 ){
				$new_firmante = $this->actualizaEstadosFirmantes($new_firmante, $lastFirmante);
			}

			if ($this->estaFirmadoFirmantes($new_firmante)){
				$datosAct['estado'] =  "FIRMADO";
			}

			$datosAct['firmantes'] =  $new_firmante;
			$cursor = self::$ConnMDB->actualizaPorId("documentos", $idDoc, $datosAct);			
		}
		else{
			$idDoc = 0 ;
		}


		return $idDoc;
	}

}

?>