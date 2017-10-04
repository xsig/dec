<?php
namespace Dec\model;
use Dec\database\MongoDBConn as MongoDBConn;

class SubTipoDocumentos {
	private static $ConnMDB;
	private $coll;
	public function __construct(){
		self::$ConnMDB = new MongoDBConn();
	}
	
	private function listaSubTipoDocumentos(){
		return 	self::$ConnMDB->lista("subtipoDocumento");
	}
	
		// Funciones Generales
	private function validaConexion(){
		if(!$this->_perfiles->validaConexion()){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"conexionErr");
		}		
	}

	private function validaDocumento($document){
		if (!$this->func->validaDocumento($document)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"DocumentoErr");
		}
	}
	
	public function traeListaSubTiposDocumentos($listaIdFirmante){
		$_subtDocs= array();
		$_subtDocumentos= array();
		if (count($listaIdFirmantes)>0){
			$listaId = array('$in' => $listaIdFirmante);
			$busqueda = array('idFirmante' => $listaId ,'estado' => 'ACTIVO' );
			$cursor = self::$ConnMDB->busca("subtipoDocumentoFirmante", $busqueda);
			//if($cursor->count()>0){
				foreach($cursor as $item ){
						$_subtDocs[] =$item->idSubTipoDocumento;
				}
			//}
			$subdocumentos = array_unique($_subtDocs);
			foreach($subdocumentos as $idSubDocs){
					$_subtDocumentos[] =$this->traeSubTipoDocumentoPorId($idSubDocs);
			}
		}
		return $_subtDocumentos;
	}

	public function traeIdSubTipoDocumentoPorCodigo($codigo){
		$_subtipoDocumento = "";
		$busqueda = array("codigo" => $codigo );
		$cursor = self::$ConnMDB->busca("subtipoDocumento", $busqueda);
		foreach($cursor as $item ){
			$_subtipoDocumento = $item->_id;
		}
		return $_subtipoDocumento;
	}

	public function traeFirmantesPorCodigoSubTipoDocumento($empresa, $codigoDoc, $subTipoDocumento){
		$_subtipoDocumento = array();
		$busqueda = array(
			"empresa" => $empresa,
			"tipoDocumento" => $codigoDoc,
			"codigo" => $subTipoDocumento 
		);
		$cursor = self::$ConnMDB->busca("subtipoDocumento", $busqueda);
		foreach($cursor as $item ){
			$_subtipoDocumento = $item->firmantes;
		}
		return $_subtipoDocumento;
	}

	public function traeIdSubTipoDocumentosPorEmpresaDocSubDoc($empresa, $codDocumneto, $codSubTipoDocumento){
		$_idSubTD = 0;
		$busqueda = array(
			"codigo" => $codSubTipoDocumento ,
			"tipoDocumento" => $codDocumneto,
			"empresa" => $empresa
		);
		$cursor = self::$ConnMDB->busca("subtipoDocumento", $busqueda);
		foreach($cursor as $item ){
			$_idSubTD = $item->_id;
		}
		return $_idSubTD;
	}


	public function traeFirmantesPorIdSubTipoDocumento($idSubTipoDocumento){
		$_subtipoDocumento = array();
		$busqueda = array("idSubTipoDocumento" => $idSubTipoDocumento );
		$cursor = self::$ConnMDB->busca("subtipoDocumentoCliente", $busqueda);
		foreach($cursor as $item ){
			$_subtipoDocumento[] = $item->idFirmante;
		}
		return $_subtipoDocumento;
	}

	public function traeSubTipoDocumentoPorId($idSubTipoDocumento){
		$_subtipoDocumento = array();
		$busqueda = array("_id" => $idSubTipoDocumento ,"estado" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("subtipoDocumento", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
				$_subtipoDocumento['subtipoDocumento'] = $item->nombre;
				$_subtipoDocumento['codigo'] = $item->codigo;
				$_subtipoDocumento['descripcion'] = $item->descripcion;
				$_subtipoDocumento['firmantes'] = $item->firmantes;
			}
		//}
		return $_subtipoDocumento;
	}
	

	public function traeFirmantesSubTipoDocumentoPorCodigo($CodigoSubTipoDocumento){
		$_subtipoDocumento = array();
		$busqueda = array("codigo" => $CodigoSubTipoDocumento ,"estado" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("subtipoDocumento", $busqueda);
		foreach($cursor as $item ){
			$_subtipoDocumento[] = $item->firmantes;
		}
		return $_subtipoDocumento;
	}

	public function traeArrSubTipoDocumentoPorIdTipoDocumento($idTipoDocumento){
		$_subtipoDocumento = array();
		$_subtipoDocumentoArr = array();
		$busqueda = array("_id" => $idTipoDocumento ,"estado" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("tipoDocumento", $busqueda);
		foreach($cursor as $item ){
			$_busqueda = array("tipoDocumento" => $item->codigo ,"estado" => "ACTIVO" );
			$_cursor = self::$ConnMDB->busca("subtipoDocumento", $_busqueda);
			foreach ($_cursor as $subitem) {
				$_subtipoDocumentoArr['id'] = $subitem->_id;
				$_subtipoDocumentoArr['nombre'] = $subitem->nombre;
				$_subtipoDocumentoArr['descripcion'] = $subitem->descripcion;
				$_subtipoDocumentoArr['codigo'] = $subitem->codigo;
				$_subtipoDocumentoArr['estado'] = $subitem->estado;
				foreach ($subitem->firmantes as $firmante)
				{
					$_busqueda2 = array("idSubTipoDocumento" => $subitem->_id ,"nombrePerfil" => $firmante->nombrePerfil );
					$_cursor2 = self::$ConnMDB->busca("subtipoDocumentoFirmante", $_busqueda2);
					foreach ($_cursor2 as $usuario)
					{
						$firmante->usuarios=$usuario->usuarios;
					}
				}
				$_subtipoDocumentoArr['firmantes'] = $subitem->firmantes;
				$_subtipoDocumento[] = $_subtipoDocumentoArr;
			}
		}
		return $_subtipoDocumento;
	}

	public function traeSubTipoDocumentoPorIdTipoDocumento($idTipoDocumento){
		$_subtipoDocumento = array();
		$busqueda = array("_id" => $idTipoDocumento ,"estado" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("tipoDocumento", $busqueda);
		foreach($cursor as $item ){
			$_busqueda = array("tipoDocumento" => $item->codigo ,"estado" => "ACTIVO" );
			$_cursor = self::$ConnMDB->busca("subtipoDocumento", $_busqueda);
			foreach ($_cursor as $subitem) {
				$_subtipoDocumento[] = $subitem->nombre;
			}
		}
		return $_subtipoDocumento;
	}


	public function estadosValidosSubTipoDocumentos($estadoValidar){
		$estadoVal = strtoupper($estadoValidar);
		if($estadoVal == "ACTIVO" ||
		   $estadoVal == "INACTIVO" )
			return true;
		return false;
	}

	public function buscaSubTipoDocumentosFiltros($busquedaSubTipoDocumentos){
        $subtDocsIds = array();
        $cursor = self::$ConnMDB->busca("subtipoDocumento", $busquedaSubTipoDocumentos);
        //if($cursor->count()>0){
            foreach($cursor as $item){
                $subtDocsIds[] = $item->_id;
            }   
        //}
        return $subtDocsIds;
    }
   	public function traeUsuariosPerfilSubTipoDocumentos($idSubTipoDocumento, $nombrePerfil){
   		$arrUsuarios = array();
   		$busqueda = array( "idSubTipoDocumento" => $idSubTipoDocumento , "nombrePerfil" => $nombrePerfil);
   		$cursor = self::$ConnMDB->busca("subtipoDocumentoFirmante", $busqueda);
   		foreach ($cursor as  $item) {
   			if ($item->nombrePerfil != "PERSONAL"){
				$arrUsuarios = $item->usuarios;
   			}
   		}
   		return $arrUsuarios;
   	}

   

   	public function traeIdSubTipoDocumentosPorEmpresaSubDoc($empresa, $codSubTipoDocumento){
		$_idSubTD = 0;
		$busqueda = array(
			"codigo" => $codSubTipoDocumento ,
			"empresa" => $empresa
		);
		$cursor = self::$ConnMDB->busca("subtipoDocumento", $busqueda);
		foreach($cursor as $item ){
			$_idSubTD = $item->_id;
		}
		return $_idSubTD;
	}

    public function traeSubTipoDocumentosYRutsPorListaId($listaIdSubTDocs){
		$_subtipoDocumentosFirmantes = new SubTipoDocumentosFirmantes();
		$listaFirmantes = array();

		$subtipoDocs=array();
		$_subtdocs = array();
		$listaID = array('$in' => $listaIdSubTDocs);
		$busqueda = array( "_id" => $listaID  );
		$cursor = self::$ConnMDB->busca("subtipoDocumento", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
					$_subtdocs['subtipoDocumento'] =$item->nombre;
					$_subtdocs['nombreSubTipoDocumento'] =$item->descripcion;
					$_subtdocs['estado'] =$item->estado;
					$_subtdocs['codigo'] =$item->codigo;
					$arrFirmante = array();
					foreach ($item->firmantes as $firmante ) {
						$arrFirmante['orden'] = $firmante->orden;
						$arrFirmante['descripcionPerfil'] = $firmante->descripcionPerfil;
						$arrFirmante['nombrePerfil'] = $firmante->nombrePerfil;
						$arrFirmante['usuarios'] = $this->traeUsuariosPerfilSubTipoDocumentos($item->_id, $firmante->nombrePerfil);
						$_subtdocs['firmantes'][] = $arrFirmante;
					}
					$subtipoDocs[] =$_subtdocs;
			}
		//}
		return $subtipoDocs;
	}


    public function traeSubTipoDocumentosPorListaId($listaIdSubTDocs){
		$_subtipoDocumentosFirmantes = new SubTipoDocumentosFirmantes();
		$listaFirmantes = array();
		$subtipoDocs=array();
		$_subtdocs = array();
		$listaID = array('$in' => $listaIdSubTDocs);
		$busqueda = array( "_id" => $listaID  );
		$cursor = self::$ConnMDB->busca("subtipoDocumento", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
					$_subtdocs['subtipoDocumento'] =$item->nombre;
					$_subtdocs['nombreSubTipoDocumento'] =$item->descripcion;
					$_subtdocs['estado'] =$item->estado;
					$_subtdocs['codigo'] =$item->codigo;
					$_subtdocs['firmantes'] = $item->firmantes;
					$subtipoDocs[] =$_subtdocs;
			}
		//}
		return $subtipoDocs;
	}

	public function existeSubTipoDocumentoEmpresa($rutEmpresa, $codigo){
    	// Falta buscar la empresa del rut del usuario
        $busqueda = array('empresa' => strtoupper($rutEmpresa) , "codigo" => strtoupper($codigo) );
        $cursor = self::$ConnMDB->busca("subtipoDocumento", $busqueda);
        foreach($cursor as $item ){
            return true;
        }
        return false;
    }

	public function existeSubTipoDocumento($rut, $nombreSubTipoDocumento){
    	// Falta buscar la empresa del rut del usuario
        $busqueda = array('nombre' => $nombreSubTipoDocumento );
        $cursor = self::$ConnMDB->busca("subtipoDocumento", $busqueda);
        foreach($cursor as $item ){
            return true;
        }
        return false;
    }
    public function existeSubTipoDocumentoCodigo($codigoSubTipoDocumento){
    	// Falta buscar la empresa del rut del usuario
        $busqueda = array('codigo' => $codigoSubTipoDocumento );
        $cursor = self::$ConnMDB->busca("subtipoDocumento", $busqueda);
        foreach($cursor as $item ){
            return true;
        }
        return false;
    }

    public function ingresaSubTipoDocumento($document){
    	$usuario = new Usuarios();
    	$idCliente = 0;
    	$_idPerfilCliente = 0;

    	$rut_usuario = $document['mensaje_dec']['header']['usuario'];

    	$idUsuario = $usuario->traeIdUsuarioPorRut($rut_usuario);

    	$doc_subtDocs = array(
    		"nombre" => strtoupper($document['mensaje_dec']['mensaje']['subtipoDocumento']) ,
    		"descripcion" => strtoupper($document['mensaje_dec']['mensaje']['descripcion']) ,
    		"codigo" => strtoupper($document['mensaje_dec']['mensaje']['codigo']) ,
    		"tipoDocumento" => strtoupper($document['mensaje_dec']['mensaje']['tipoDocumento']) ,
    		"empresa" => strtoupper($document['mensaje_dec']['header']['empresa']),
    		"firmantes"  =>  $this->upperFirmantes($document['mensaje_dec']['mensaje']['firmantes']) ,
			"estado"  =>  "ACTIVO"
    	);

        $_id =  self::$ConnMDB->ingresa("subtipoDocumento",$doc_subtDocs,"subtipoDocumento_id");

        foreach ($this->upperFirmantes($document['mensaje_dec']['mensaje']['firmantes'])  as $firmante) {
        	$this->ingresaFirmanteSubTipoDocumentoFirmante($document['mensaje_dec']['mensaje']['tipoDocumento'],$firmante,$_id, strtoupper($document['mensaje_dec']['header']['empresa']));
        }

        return $_id;
    }

    private function upperFirmantes($firmantes){
    	$new_firmante = array();
    	$temp_firmante = array();
    	foreach ($firmantes as $firmante) {
            $temp_firmante['orden'] = $firmante['orden']; 
            $temp_firmante['descripcionPerfil'] = strtoupper($firmante['descripcionPerfil']) ;
            $temp_firmante['nombrePerfil'] = strtoupper($firmante['nombrePerfil']);
            $new_firmante[] = $temp_firmante;
    	}
    	return $new_firmante;
    }

    private function ingresaFirmanteSubTipoDocumentoFirmante($codTD,$firmante,$idSubTipoDocumento, $empresa){
    	$subtipoDoc = new SubTipoDocumentosFirmantes();
    	$firmantes = new Firmantes();
    	$_clientes = new Clientes();
    	$firmanteCliente = new FirmantesClientes();
    	$t_Docs = new TipoDocumentos();
    	$idTD = $t_Docs->traeIdTipoDocumentoPorCodigo(strtoupper($codTD));
    	$listaEmpresasId = $t_Docs->traeEmpresasPorIdDocumento($idTD);
    	$valCliente = $_clientes->traeIdClientePorRut($empresa);
    	//foreach ($listaEmpresasId as $valCliente) {
    	    $descriocionPerfil = $firmante['descripcionPerfil'];
    		$nomPerfil = $firmante['nombrePerfil'];
    		$orden  = $firmante['orden'];
    		if (!$firmantes->existeFirmanteEnEmpresa($nomPerfil,$valCliente)){
    			$idFirmante = $firmantes->agregaFirmante($nomPerfil, $idSubTipoDocumento, $valCliente);
    			$idFirmanteCliente = $firmanteCliente->agregaFirmanteCliente($idFirmante,$valCliente,$orden);
    		}
    		else{
    			$idFirmante = $firmantes->traeFirmanteEnEmpresa($nomPerfil,$valCliente);
    			if (!$firmanteCliente->existeFirmanteCliente($idFirmante, $valCliente)){
    				$idFirmanteCliente = $firmanteCliente->agregaFirmanteCliente($idFirmante,$valCliente,$orden);
    			}
    		}
    	//}


    	// $idFirmante = $firmantes->traeIdFirmantePorRut($firmante);
    	// if (!$subtipoDoc->existeSubTipoDocumentoFirmante($idFirmante, $idSubTipoDocumento)){
    	// 	$subtipoDoc->ingresaSubTipoDocumentoFirmante($idFirmante, $idSubTipoDocumento);
    	// }
    }

    public function actualizaSubTipoDocumento($document){
    	$firmante = new Firmantes();
    	$usuario = new Usuarios();
    	$subtDocFirmante = new SubTipoDocumentosFirmantes();
    	$idFirmante = 0;
    	$_idPerfilCliente = 0;
    	//////
    	$codigoSubTD = $document['mensaje_dec']['mensaje']['codigoSubTipoDocumento'];
    	$_idSubTD = $this->traeIdSubTipoDocumentoPorCodigo($codigoSubTD);
    	$arrIdFirmantes = $this->traeFirmantesPorIdSubTDocumento($_idSubTD);
    	$docModSubTipoDocumento = array();
    	//////

    	$rut_usuario = $document['mensaje_dec']['header']['usuario']; 
     	$idUsuario = $usuario->traeIdUsuarioPorRut($rut_usuario); 

     	if (isset($document['mensaje_dec']['mensaje']['nuevoSubTipoDocumento'])){
    		$docModSubTipoDocumento['nombre'] = $document['mensaje_dec']['mensaje']['nuevoSubTipoDocumento'];
    	}

    	if (isset($document['mensaje_dec']['mensaje']['nuevaDescripcion'])){
    		$docModTipoDocumento['descripcion'] = $document['mensaje_dec']['mensaje']['nuevaDescripcion'];
    	}

    	// if (isset($document['mensaje_dec']['mensaje']['firmantes'])){
    	// 	$docModTipoDocumento['firmantes'] = $document['mensaje_dec']['mensaje']['firmantes'];
    	// }

    	if (isset($document['mensaje_dec']['mensaje']['empresasSolicitadasAltas'])){
    		$arrIdEmpresas = $arrIdEmpresas + $cliente->traeIdClientePorListaRut($document['mensaje_dec']['mensaje']['empresasSolicitadasAltas']);
    	}

    	if (isset($document['mensaje_dec']['mensaje']['empresasSolicitadasBajas'])){
    		$arrIdEmpresas = array_diff($arrIdEmpresas, $cliente->traeIdClientePorListaRut($document['mensaje_dec']['mensaje']['empresasSolicitadasBajas']));
    	}

    	$arrRutsEmpresas = $cliente->traeRutsClientePorListaId($arrIdEmpresas);
		$docModTipoDocumento['empresasSolicitadas'] = $arrRutsEmpresas;


        if(self::$ConnMDB->actualizaPorId("tipoDocumento",$_idTD,$docModTipoDocumento)){
        	$hayCambiosEnEmpresa =  0;
        	if(isset($document['mensaje_dec']['mensaje']['empresasSolicitadasAltas'])){
        		$arrClientes  = $document['mensaje_dec']['mensaje']['empresasSolicitadasAltas'];
		        foreach($arrClientes as $rutEmpresa) { 
					$idCliente = $cliente->traeIdClientePorRut($rutEmpresa);
			        $doc_TDCliente= array(
			        	"idCliente" => $idCliente,
			        	"idTipoDocumento" => $_idTD,
			   			"estado"  => "ACTIVO" 
			        );
			        if (!$tDocCliente->existeTipoDocumentoCliente($idCliente,$_idTD)){
			        	$_idTDCliente=  self::$ConnMDB->ingresa("tipoDocumentoCliente",$doc_TDCliente,"tipoDocumentoCliente_id");
			        }
		    	}

        	}
        	if(isset($document['mensaje_dec']['mensaje']['empresasSolicitadasBajas'])){
         		$arrClientes  = $document['mensaje_dec']['mensaje']['empresasSolicitadasBajas'];
		        foreach($arrClientes as $rutEmpresa) { 
					$idCliente = $cliente->traeIdClientePorRut($rutEmpresa);

			        $doc_TDCliente= array(
			        	"idCliente" => $idCliente,
			        	"idTipoDocumento" => $_idTD
			        );
			        $_idTDCliente =  self::$ConnMDB->buscaId("tipoDocumentoCliente",$doc_TDCliente);

			        if ($tDocCliente->existeTipoDocumentoCliente($idCliente,$_idTD)){
			        	self::$ConnMDB->eliminaPorId("tipoDocumentoCliente",$_idTDCliente);
			        }

		    	}  		
        	}


        }
        
        return $_idTD;
    }
   
}
?>