<?php
namespace Dec\model;
use Dec\database\MongoDBConn as MongoDBConn;

class SubTipoDocumentosFirmantes {
	private static $ConnMDB;
	private $coll;
	public function __construct(){
		self::$ConnMDB = new MongoDBConn();
	}
	

	public function traeListaRutClientePorSubTipoDocumentos($idTipoDocumento){
		$_Mfirmantes = new Firmantes();
		$_firmante="";
		$busqueda = array("idTipoDocumento" => $idTipoDocumento , "estado" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("tipoDocumentoCliente", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
					$_firmante[] =$_Mfirmantes->traeRutFirmantePorId($item->_id);
			}
		//}
	
		return $_firmante;
	}

	public function existeSubTipoDocumentoFirmante($idCliente, $idTipoDocumento){
		$busqueda = array(
				"idSubTipoDocumento" => $idSubTipoDocumento ,
				"idFirmante" => $idFirmante
			);
		$contador = self::$ConnMDB->count("subtipoDocumentoFirmante", $busqueda);
		if($contador > 0 ){
			return true;
		}
		return false;
	}

	public function existeSubTipoDocumento($busqueda){
		$contador = self::$ConnMDB->count("subtipoDocumentoFirmante", $busqueda);
		if($contador > 0 ){
			return true;
		}
		return false;
	}

	public function ingresaSubTipoDocumentoFirmante($idFirmante, $idSubTipoDocumento){
		$documento = array(
    		"idFirmante" =>  $idFirmante,
    		"idSubTipoDocumento" =>  $idSubTipoDocumento,
			"estado"  =>  "ACTIVO"
    	);

        $_id =  self::$ConnMDB->ingresa("subtipoDocumentoFirmante",$documento,"subtipoDocumentoFirmante_id");
	}

	public function traeFirmantesSubTipoDocumentos($empresa, $codDocumento, $codSubTipoDocumento){
		$arregloPerfiles = array();
		$tmpArregloPerfiles = array();
		$_SubTipoDocumentos = new SubTipoDocumentos();
		$_usuarios = new Usuarios();
		$idSubTD = $_SubTipoDocumentos->traeIdSubTipoDocumentosPorEmpresaDocSubDoc($empresa, $codDocumento, $codSubTipoDocumento);
		$docBusqueda = array(
			"idSubTipoDocumento"  => $idSubTD
			);
		$cursor = self::$ConnMDB->busca("subtipoDocumentoFirmante", $docBusqueda);
		foreach($cursor as $item ){
			$tmpArregloPerfiles['nombrePerfil'] = $item->nombrePerfil;
			$tmpArregloPerfiles['descripcionPerfil'] = $item->descripcionPerfil;
			$tmpArregloPerfiles['usuarios'] = $_usuarios->traeUsuarioNombrePorListaRuts($item->usuarios);
			$arregloPerfiles[] = $tmpArregloPerfiles;
		}
		return $arregloPerfiles;
	}
}

	?>