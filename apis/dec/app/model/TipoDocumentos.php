<?php
namespace Dec\model;
use Dec\database\MongoDBConn as MongoDBConn;

class TipoDocumentos {
	private static $ConnMDB;
	private $coll;
	public function __construct(){
		self::$ConnMDB = new MongoDBConn();
	}
	
	private function listaPerfiles(){
		return 	self::$ConnMDB->lista("tipoDocumento");
	}
	
	public function traeListaTiposDocumentos($listaIdCliente){
		$_tDocs= array();
		$_tDocumentos= array();
		if (count($listaIdCliente)>0){
			$listaId = array('$in' => $listaIdCliente);
			$busqueda = array('idCliente' => $listaId ,'estado' => 'ACTIVO' );
			$cursor = self::$ConnMDB->busca("tipoDocumentoCliente", $busqueda);
			//if($cursor->count()>0){
				foreach($cursor as $item ){
						$_tDocs[] =$item->idTipoDocumento;
				}
			//}
			$documentos = array_unique($_tDocs);
			foreach($documentos as $idDocs){
					$_tDocumentos[] =$this->traeTipoDocumentoPorId($idDocs);
			}
		}
		return $_tDocumentos;
	}

	public function traeIdTipoDocumentoPorCodigo($codigo){
		$_tipoDocumento = "";
		$busqueda = array("codigo" => $codigo );
		$cursor = self::$ConnMDB->busca("tipoDocumento", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
				$_tipoDocumento = $item->_id;
			}
		//}
		return $_tipoDocumento;
	}

	public function traeEmpresasPorIdDocumento($idDocumento){
		$_tipoDocumento = array();
		$busqueda = array("idTipoDocumento" => $idDocumento );
		$cursor = self::$ConnMDB->busca("tipoDocumentoCliente", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
				$_tipoDocumento[] = $item->idCliente;
			}
		//}
		return $_tipoDocumento;
	}

	public function traeTipoDocumentoPorListaId($listaIdTipoDocumento){
		$_tipoDocumento = array();
		$listaTD = array();
		$busqueda = array("_id" => $idTipoDocumento ,"estado" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("tipoDocumento", $busqueda);
		foreach($cursor as $item ){
			$_tipoDocumento['tipoDocumento'] = $item->nombre;
			$_tipoDocumento['codigo'] = $item->codigo;
			$_tipoDocumento['descripcion'] = $item->descripcion;
			$_tipoDocumento['empresas'] = $item->empresa;
			$listaTD['ListaTD'][] = $_tipoDocumento;

		}
		return $listaTD;
	}

	public function traeTipoDocumentoPorId($idTipoDocumento){
		$_tipoDocumento = array();
		$busqueda = array("_id" => $idTipoDocumento ,"estado" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("tipoDocumento", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
				$_tipoDocumento['tipoDocumento'] = $item->nombre;
				$_tipoDocumento['codigo'] = $item->codigo;
				$_tipoDocumento['descripcion'] = $item->descripcion;
				$_tipoDocumento['empresasSolicitadas'] = $item->empresasSolicitadas;
			}
		//}
		return $_tipoDocumento;
	}
	public function estadosValidosTipoDocumentos($estadoValidar){
		$estadoVal = strtoupper($estadoValidar);
		if($estadoVal == "ACTIVO" ||
		   $estadoVal == "INACTIVO" )
			return true;
		return false;
	}

	public function buscaTipoDocumentosFiltros($busquedaTipoDocumentos){
        $tDocsIds = array();
        $cursor = self::$ConnMDB->busca("tipoDocumento", $busquedaTipoDocumentos);
        //if($cursor->count()>0){
            foreach($cursor as $item){
                $tDocsIds[] = $item->_id;
            }   
        //}
        return $tDocsIds;
    }

    public function traeTipoDocumentosPorListaId($listaIdTDocs){
		$_tDocsClientes = new TipoDocumentosCliente();
		$_subtDocsClientes = new SubTipoDocumentos();
		$listaClientes = array();
		$tipoDocs=array();
		$_tdocs = array();
		$listaID = array('$in' => $listaIdTDocs);
		$busqueda = array( "_id" => $listaID  );
		$cursor = self::$ConnMDB->busca("tipoDocumento", $busqueda);
		foreach($cursor as $item ){
				$_tdocs['id'] =$item->_id;
				$_tdocs['tipoDocumento'] =$item->nombre;
				$_tdocs['nombreTipoDocumento'] =$item->descripcion;
				$_tdocs['estado'] =$item->estado;
				$_tdocs['codigo'] =$item->codigo;
				$_tdocs['empresa'] = $item->empresa;
				$_tdocs['subtipoDocumentos'] = $_subtDocsClientes->traeArrSubTipoDocumentoPorIdTipoDocumento($item->_id, $item->empresa);
				$tipoDocs[] =$_tdocs;
		}
		return $tipoDocs;
	}

	public function existeTipoDocumento($rut, $nombreTipoDocumento){
    	// Falta buscar la empresa del rut del usuario
        $busqueda = array('nombre' => $nombreTipoDocumento );
        $cursor = self::$ConnMDB->busca("tipoDocumento", $busqueda);
        foreach($cursor as $item ){
            return true;
        }
        return false;
    }
    public function existeTipoDocumentoCodigo($codigoTipoDocumento){
    	// Falta buscar la empresa del rut del usuario
        $busqueda = array('codigo' => $codigoTipoDocumento );
        $cursor = self::$ConnMDB->busca("tipoDocumento", $busqueda);
        foreach($cursor as $item ){
            return true;
        }
        return false;
    }

    public function ingresaTipoDocumento($document){
    	$usuario = new Usuarios();
    	$idCliente = 0;
    	$_idPerfilCliente = 0;
    	$listaIdTDocs = array();

    	$rut_usuario = $document['mensaje_dec']['header']['usuario'];

    	$idUsuario = $usuario->traeIdUsuarioPorRut($rut_usuario);

    	foreach ($document['mensaje_dec']['mensaje']['empresasSolicitadas'] as $empresaRut) {
	    	$doc_tDocs = array(
	    		"nombre" => strtoupper($document['mensaje_dec']['mensaje']['tipoDocumento']) ,
	    		"descripcion" => strtoupper($document['mensaje_dec']['mensaje']['descripcion']) ,
	    		"codigo" => strtoupper($document['mensaje_dec']['mensaje']['codigo'])  ,
	    		"empresa"  => strtoupper($empresaRut) ,
				"estado"  =>  "ACTIVO"
	    	);
			$_id =  self::$ConnMDB->ingresa("tipoDocumento",$doc_tDocs,"tipoDocumento_id");
			$listaIdTDocs[] = $_id ;
			$this->ingresaClienteTipoDocumentoCliente($empresaRut,$_id);
    	}

        return $listaIdTDocs;
    }

    private function ingresaClienteTipoDocumentoCliente($empresaRut,$idTipoDocumento){
    	$tipoDoc = new TipoDocumentosCliente();
    	$cliente = new Clientes();
    	$idCliente = $cliente->traeIdClientePorRut($empresaRut);
    	if (!$tipoDoc->existeTipoDocumentoCliente($idCliente, $idTipoDocumento)){
    		$tipoDoc->ingresaTipoDocumentoCliente($idCliente, $idTipoDocumento);
    	}
    }

    public function actualizaTipoDocumento($document){
    	$cliente = new Clientes();
    	$usuario = new Usuarios();
    	$tDocCliente = new TipoDocumentosCliente();
    	$idCliente = 0;
    	$_idPerfilCliente = 0;
    	//////
    	$codigoTD = $document['mensaje_dec']['mensaje']['codigoTipoDocumento'];
    	$_idTD = $this->traeIdTipoDocumentoPorCodigo($codigoTD);
    	$arrIdEmpresas = $this->traeEmpresasPorIdDocumento($_idTD);
    	$docModTipoDocumento = array();
    	//////

    	$rut_usuario = $document['mensaje_dec']['header']['usuario']; 
     	$idUsuario = $usuario->traeIdUsuarioPorRut($rut_usuario); 

     	if (isset($document['mensaje_dec']['mensaje']['nuevoTipoDocumento'])){
    		$docModTipoDocumento['nombre'] = $document['mensaje_dec']['mensaje']['nuevoTipoDocumento'];
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

    public function existeEmpresaCodigoTipoDocumentos($codTD, $rutEmpresa){
    	// $_clientes = new Clientes();
    	// $idCliente = $_clientes->traeIdClientePorRut();
    	$codigoTD = strtoupper($codTD);
        $busqueda = array('codigo' => $codigoTD , "empresa" => $rutEmpresa );
        $cursor = self::$ConnMDB->busca("tipoDocumento", $busqueda);
        foreach($cursor as $item ){
            return true;
        }
        return false;   	
    }
   
}
?>