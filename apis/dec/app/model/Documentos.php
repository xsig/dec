<?php
namespace Dec\model;
use Dec\database\MongoDBConn as MongoDBConn;

class Documentos {
	private static $ConnMDB;
	private $coll;
	public function __construct(){
		self::$ConnMDB = new MongoDBConn();
	}
	
	private function listaDocumentos(){
		return 	self::$ConnMDB->lista("Documentos");
	}
	
	public function existeFirmanteEnDocumento($idAcepta,$empresa,$nombrePerfilFirmante){
		$firmantes = array();
		$orden = -1;
		$busqueda = array("idAcepta" => $idAcepta , "empresa" => $empresa );
		$cursor = self::$ConnMDB->busca("documentos", $busqueda);
		foreach($cursor as $item ){
			$firmantes = $item->firmantes;
		}
		foreach ($firmantes as  $firmante) {
			if ($firmante->nombrePerfil == $nombrePerfilFirmante){
				return true;
			} 
		}
		return false;
	}

	public function sepuedeFirmarDocumento($codigoDocAcepta,$empresa,$nombrePerfilFirmante){
		$firmantes = array();
		$orden = -1;
		$tmpOrden = -1;
		$ordenAFirmar = -1;
		$busqueda = array("idAcepta" => $codigoDocAcepta , "empresa" => $empresa );
		$cursor = self::$ConnMDB->busca("documentos", $busqueda);
		foreach($cursor as $item ){
			$firmantes = $item->firmantes;
		}
		foreach ($firmantes as  $firmante) {
			if($firmante->estadoFirma == "FIRMADO"){
				if ($firmante->orden > $tmpOrden){
			 		$tmpOrden = $firmante->orden;
				}
			}
			if ($firmante->nombrePerfil == $nombrePerfilFirmante){
				$ordenAFirmar = $firmante->orden;
			} 
		}
		if ($tmpOrden <= $ordenAFirmar){
			return true;
		}

		return false;		
	}
	public function traeDocumentoPorId($idDocumento){
		$_Documento = array();
		$busqueda = array("_id" => $idDocumento );
		$cursor = self::$ConnMDB->busca("documentos", $busqueda);
		foreach($cursor as $item ){
			unset($item->_id);
			$_Documento = $item;
		}
		return $_Documento;
	}

	public function traeDocumentoPorIdAcepta($idAcepta){
		$_Documento = array();
		$busqueda = array("idAcepta" => $idAcepta );
		$cursor = self::$ConnMDB->busca("documentos", $busqueda);
		foreach($cursor as $item ){
			$_Documento = $item;
		}
		return $_Documento;
	}

	public function ExisteDocumentoPorIdAcepta($idAcepta){
		$_Documento = array();
		$busqueda = array("idAcepta" => $idAcepta );
		$cursor = self::$ConnMDB->busca("documentos", $busqueda);
		foreach($cursor as $item ){
			return true;
		}
		return false;
	}

	public function ExisteDocumentoPorEstado($estadoAcepta){
		$_Documento = array();
		$busqueda = array("estado" => $estadoAcepta );
		$cursor = self::$ConnMDB->busca("documentos", $busqueda);
		foreach($cursor as $item ){
			return true;
		}
		return false;
	}

	public function buscaDocumentosFiltros($busquedaDocumentos,$rut_usuario,$rut_empresa){
		$_usuarios = new Usuarios();
		$perfilesUsuario = $_usuarios->perfilesFirmaUsuario($rut_usuario,$rut_empresa);
        $tDocsIds = array();
                $arrSalidaDocs = array();
        $cursor = self::$ConnMDB->busca("documentos", $busquedaDocumentos);
        foreach($cursor as $item){
        	unset($item->_id);
			unset($item->archivo);
			$pendiente=false;
            $tDocsIds['idAcepta'] = $item->idAcepta;
            $tDocsIds['empresa'] = $item->empresa;
            $tDocsIds['subtipoDocumento'] = $item->subtipoDocumento;
            $tDocsIds['url'] = $item->url;
            $tDocsIds['fechaCarga'] = $item->fechaCarga;
            $tDocsIds['nombre'] = $item->nombre;
			$tDocsIds['tamano'] = $item->tamano;
            $tDocsIds['usuarioCreador'] = $item->usuarioCreador;
            $tDocsIds['comentario'] = $item->comentario;
			$tDocsIds['estado'] = $item->estado;

    		$arrFirmante = array();
			$tDocsIds['firmantes'] = array();
            foreach ($item->firmantes as  $firmante) {

				$arrFirmante['rutFirmante'] = $firmante->rutFirmante;
				$arrFirmante["nombreFirmante"] = $firmante->nombreFirmante;
	            $arrFirmante['nombrePerfil'] = $firmante->nombrePerfil;
	            $arrFirmante['descripcionPerfil'] = $firmante->descripcionPerfil;
	            $arrFirmante['orden'] = $firmante->orden;
	            $arrFirmante['codigoFirma'] = $firmante->codigoFirma;
				$arrFirmante['estadoFirma'] = $firmante->estadoFirma;
				if($firmante->estadoFirma=="FIRMADO" || $pendiente)
					$arrFirmante["firmable"] = "N";
				else
				{
					$pendiente=true;
					if($firmante->descripcionPerfil=="PERSONAL")
						$arrFirmante["firmable"]="S";
					else
					{
						if(in_array($firmante->nombrePerfil,$perfilesUsuario))
							$arrFirmante["firmable"] = "S";
						else
							$arrFirmante["firmable"] = "N";
					}
				}
				$tDocsIds['firmantes'][] = $arrFirmante; 	            

            }

            $arrSalidaDocs[]= $tDocsIds;
        }   
        return $arrSalidaDocs;
    }
   
}
?>