<?php
namespace Dec\controller;
use Dec\model as Model;
use Dec\error\MensajeError as MensajeError;
use Dec\utils\Funciones as Funciones;
use Dec\model\Logging as Logging;

class TipoDocumentosController{
	private $valid;
	private $objSalida;
	private $salida;
	private $func;
	private $Mensaje;
	private $_clientes;
	private $_usuarios;	
	private $_roles;
	private $_perfiles;

	public function __construct(){
		$this->objSalida = new Model\Salida();
		$this->func = new Funciones();
		$this->Mensaje = new MensajeError();
		$this->_usuarios = new Model\Usuarios();
		$this->_clientes = new Model\Clientes();
		$this->_roles = new Model\Roles();
		$this->_perfiles = new Model\Perfiles();
		$this->_tDocs = new Model\TipoDocumentos();
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
	// Fin Funciones Generales

	// Inicio Busqueda de Perfiles
	public function busquedaTipoDocumento($document){
		$this->salida = $this->objSalida->seteaSalida("BusquedaTipoDocumento",$document);
		$this->validaBusquedaTipoDocumento($document);
		if ($this->valid){
			$this->traeBusquedaTipoDocumento($document);
		}
		return $this->salida;
	}

	private function validaBusquedaTipoDocumento($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoBusquedaTipoDocumento($document);
	}

	private function validaFormatoBusquedaTipoDocumento($document){
		if (!isset($document['mensaje_dec']['header']['empresa'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"NoHeaderEmpresa","empresa");
		}


		if (isset($document['mensaje_dec']['mensaje']['Filtros']['tipoDocumento'])){
			$filtroNombre = strtoupper($document['mensaje_dec']['mensaje']['Filtros']['tipoDocumento']) ;
			if (empty($filtroNombre)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"filtroNombreTipoDocumentoVacio","tipoDocumento");
			}
		}
		if (isset($document['mensaje_dec']['mensaje']['Filtros']['nombreTipoDocumento'])){
			$filtroDescripcion = strtoupper($document['mensaje_dec']['mensaje']['Filtros']['nombreTipoDocumento']) ;
			if (empty($filtroDescripcion)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"filtroDescripcionPerfVacio","nombreTipoDocumento");
			}
		}
		if (isset($document['mensaje_dec']['mensaje']['Filtros']['codigo'])){
			$filtroCodigo = strtoupper($document['mensaje_dec']['mensaje']['Filtros']['codigo']) ;
			if (empty($filtroCodigo)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"filtroCodigoTipoDocumentoVacio","codigo");
			}
		}
		if (isset($document['mensaje_dec']['mensaje']['Filtros']['estado'])){
			$filtroEstado = strtoupper($document['mensaje_dec']['mensaje']['Filtros']['estado']) ;
			if (empty($filtroEstado)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"filtroEstadoTipoDocumentoVacio","estado");
			}
			if(!$this->_tDocs->estadosValidosTipoDocumentos($filtroEstado)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"filtroEstadoTipoDocumentoNoValido","estado");				
			}

		}

		if(isset($document['mensaje_dec']['mensaje']['Filtros']['empresas'])){
			$listaEmpresas = $document['mensaje_dec']['mensaje']['Filtros']['empresas'];
			if(is_array($listaEmpresas) && count($listaEmpresas) > 0){
				foreach($listaEmpresas as $emp){
					if (!$this->func->valida_rut($emp)){
						$this->valid=false;
						$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutEmpInvalidoErr", "empresas",$emp);
					}
					if(!$this->_clientes->existeClientePorRut($emp)){
						$this->valid=false;
						$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"ClienteNoExisteErr", "empresas",$emp);						
					}
				}
			}
			else{
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoEmpNoValido");				
			}
		}
	}

	private function traeBusquedaTipoDocumento($document){
		$validaBusq = 1;
		$usuariosBuscado ="";
		$busqueda = array();
		$filtroIdTipoDocumento = "";
		$filtroNombreTipoDocumento = "";
		$filtroDescripcionTipoDocumento = "";
		$filtroCodigoTipoDocumento = "";
		$filtroEstadoTipoDocumento = "";
		$listaEmpresas = "";

		$rut_usuario = $document['mensaje_dec']['header']['usuario'];
		$rut_empresa = $document['mensaje_dec']['header']['empresa'];
		
		if (isset($document['mensaje_dec']['mensaje']['Filtros']['idTipoDocumento'])){
			if ($document['mensaje_dec']['mensaje']['Filtros']['idTipoDocumento']!=NULL)
				$filtroIdTipoDocumento = $document['mensaje_dec']['mensaje']['Filtros']['idTipoDocumento'];
		}	

		if (isset($document['mensaje_dec']['mensaje']['Filtros']['tipoDocumento'])){
			if ($document['mensaje_dec']['mensaje']['Filtros']['tipoDocumento']!=NULL)
				$filtroNombreTipoDocumento = strtoupper($document['mensaje_dec']['mensaje']['Filtros']['tipoDocumento']) ;
		}	
			
		if (isset($document['mensaje_dec']['mensaje']['Filtros']['nombreTipoDocumento'])){
			if ($document['mensaje_dec']['mensaje']['Filtros']['nombreTipoDocumento']!=NULL)
				$filtroDescripcionTipoDocumento= strtoupper($document['mensaje_dec']['mensaje']['Filtros']['nombreTipoDocumento']) ;
		}

		if (isset($document['mensaje_dec']['mensaje']['Filtros']['codigo'])){
			if ($document['mensaje_dec']['mensaje']['Filtros']['codigo']!=NULL)
				$filtroCodigoTipoDocumento= strtoupper($document['mensaje_dec']['mensaje']['Filtros']['codigo']) ;
		}

		if(isset($document['mensaje_dec']['mensaje']['Filtros']['estado'])){
			if ($document['mensaje_dec']['mensaje']['Filtros']['estado']!=NULL)
				$filtroEstadoTipoDocumento = strtoupper($document['mensaje_dec']['mensaje']['Filtros']['estado']) ;
		}

		
		$rutEmpresasPermitidas = array();
		$idEmpresasPermitidas = array();
		$empresasId = array();
		$ClientesBusq = array();
		
		//$ClientesBusq = $this->_usuarios->traeListaRutClientesAutorizadasPorRut($rut_usuario);
		
		// if(isset($document['mensaje_dec']['mensaje']['empresa'])){
		// 	if ($document['mensaje_dec']['mensaje']['empresa']!=NULL)
		// 		$ClientesBusq = $document['mensaje_dec']['mensaje']['empresa'];
		// }

		
		
		//$Clientes = array_unique(array_merge($idEmpresasPermitidas,$empresasFiltro));
		
		
		// Busqueda de Tipo Documento
		$busquedaTipoDocumento =  array();
		$busquedaTipoDocumento['empresa'] = $rut_empresa;
		
		if ($filtroIdTipoDocumento != "" ){
			$busquedaTipoDocumento['_id'] = $filtroIdTipoDocumento;
		}
		if ($filtroNombreTipoDocumento != "" ){
			$busquedaTipoDocumento['nombre'] =array('$regex' => $filtroNombreTipoDocumento);
		}
		if ($filtroDescripcionTipoDocumento != "" ){
			$busquedaTipoDocumento['descripcion'] =array('$regex' => $filtroDescripcionTipoDocumento);
		}
		if ($filtroCodigoTipoDocumento != "" ){
			$busquedaTipoDocumento['codigo'] =array('$regex' => $filtroCodigoTipoDocumento);
		}
		if ($filtroEstadoTipoDocumento != "" ){
			$busquedaTipoDocumento['estado'] = $filtroEstadoTipoDocumento;
		}
		else{
			$busquedaTipoDocumento['estado'] = 'ACTIVO';
		}


		$tipoDocumentosId = $this->_tDocs->buscaTipoDocumentosFiltros($busquedaTipoDocumento);
		
		
		$listaTipoDocumentos = array();

		$listaTipoDocumentos = $this->_tDocs->traeTipoDocumentosPorListaId($tipoDocumentosId);

		$this->salida['mensaje_dec']['mensaje'] = $listaTipoDocumentos;
	}
	// Fin de Busqueda de Perfiles


	// Inicio Creacion de Tipo Documento
	public function creaTipoDocumento($document){
		$this->salida = $this->objSalida->seteaSalida("creaTipoDocumento",$document);
		$this->validaCrearTipoDocumento($document);
		if ($this->valid){
			$listaId = $this->_tDocs->ingresaTipoDocumento($document);
			if(is_array($listaId)){
				$this->salida['mensaje_dec']['mensaje']['Lista'] = $this->traeTipoDocumentoPorListaId($listaId);
				$this->salida['mensaje_dec']['header']['glosaEstado'] = "Operación Exitosa";
			}else{
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"IngresoTipoDocumentoErr");
			}
		}
		return $this->salida;
	}


	public function traeTipoDocumentoPorListaId($listaID){
		return $this->_tDocs->traeTipoDocumentosPorListaId($listaID);
	}

	public function traeTipoDocumentoPorId($id){
		return $this->_tDocs->traeTipoDocumentoPorId($id);
	}

	private function validaCrearTipoDocumento($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoCreaTipoDocumento($document);
	}

	private function validaFormatoCreaTipoDocumento($document){
		$this->validaUsuario($document);
		$this->validaNombreCreaTD($document);
		$this->validaDescripcionCreaTD($document);
		$this->validaCodigoCreaTD($document);
		$this->validaEmpresasCreaTD($document);
	}

	private function validaUsuario($document){
		$rut_usuario = $document['mensaje_dec']['header']['usuario'];
		if (!$this->func->valida_rut($rut_usuario)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutInvalidoErrNeg");
		}
		if (empty($rut_usuario)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRutVacioErrNeg");
		}
		if (!$this->_usuarios->validaRutExiste($rut_usuario)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutNoExisteErrNeg");
		}
	}

	private function validaNombreCreaTD($document){
		$rut_usuario = $document['mensaje_dec']['header']['usuario'];
		if (isset($document['mensaje_dec']['mensaje']['tipoDocumento'])){
			if(empty($document['mensaje_dec']['mensaje']['tipoDocumento'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"NombreTipoDocumentoVacio");
			}
			else{
				if ($this->_tDocs->existeTipoDocumento($rut_usuario, $document['mensaje_dec']['mensaje']['tipoDocumento'])){
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"NombreTipoDocumentoYaExiste");
				}
			}
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"NombreTipoDocumentoNoIngresado");
		}
	}

	private function validaDescripcionCreaTD($document){
		if (isset($document['mensaje_dec']['mensaje']['descripcion'])){
			if(empty($document['mensaje_dec']['mensaje']['descripcion'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"DescripcionTipoDocumentoVacio");
			}
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"DescripcionTipoDocumentoNoIngresado");
		}
	}

	private function validaCodigoCreaTD($document){
		$rut_usuario = $document['mensaje_dec']['header']['usuario'];
		if (isset($document['mensaje_dec']['mensaje']['codigo'])){
			if(empty($document['mensaje_dec']['mensaje']['codigo'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CodigoTipoDocumentoVacio");
			}
			else{
				if ($this->_tDocs->existeTipoDocumento($rut_usuario, $document['mensaje_dec']['mensaje']['codigo'])){
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CodigoTipoDocumentoYaExiste");
				}
			}
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CodigoTipoDocumentoNoIngresado");
		}
	}

	private function validaEmpresasCreaTD($document){
		if(isset($document['mensaje_dec']['mensaje']['empresasSolicitadas']) && is_array($document['mensaje_dec']['mensaje']['empresasSolicitadas'])){
			$empresasSolicitadas = $document['mensaje_dec']['mensaje']['empresasSolicitadas'];
			if(count($empresasSolicitadas)>0){
				foreach ($empresasSolicitadas as $rutEmpresa) { 
					if(!$this->_usuarios->validaEmpresaExista($rutEmpresa)){
						$this->valid=false;
						$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresaSolicitadaNoExisteErr","empresasSolicitadas",$rutEmpresa);						
					}
					if(!$this->_usuarios->validaEmpresaPermisosTipoDocumentos($document['mensaje_dec']['header']['usuario'],$rutEmpresa)){
						$this->valid=false;
						$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresaSolicitadaPermisosInvalidos","empresasSolicitadas",$rutEmpresa);						
					}
					if($this->_tDocs->existeEmpresaCodigoTipoDocumentos($document['mensaje_dec']['mensaje']['codigo'],$rutEmpresa)){
						$this->valid=false;
						$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresaSolicitadaCodigoTDocExiste","empresasSolicitadas",$rutEmpresa);						
					}
				}
			}
			else{
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresasSolicitadasErr","empresasSolicitadas");
			}
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresasSolicitadasErr","empresasSolicitadas");
		}
	}

	private function validaFirmantesCreaTD($document){
		if(isset($document['mensaje_dec']['mensaje']['firmantes'])){
			if(is_array($document['mensaje_dec']['mensaje']['firmantes'])){
				if(count($document['mensaje_dec']['mensaje']['firmantes'])>0){
					$firmantes = $document['mensaje_dec']['mensaje']['firmantes'];
					foreach ($firmantes as $firmante) {
						$nombPerfil = "";
						$orden = 0;	
						if(isset($firmante['nombrePerfil'])){
							$nombPerfil = $firmante['nombrePerfil'];						
						}				
						if(isset($firmante['orden'])){
							$orden =$firmante['orden'];						
						}	
						if(!is_int($orden) || $orden == 0){
							$this->valid=false;
							$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"FirmantesOrdenTipoDocumentoNoValido");							
						}
						if($nombPerfil == ""){
							$this->valid=false;
							$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"perfilFirmanteTipoDocumentoNoValido");							
						}
					}
				}
				else{
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"FirmantesTipoDocumentoArregloVacio");
				}
			}
			else{
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"FirmantesTipoDocumentoNoArreglo");	
			}
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"FirmantesTipoDocumentoNoIngresado");			
		}
	}

	// Fin de Creacion de Tipo Documento

	// Inicio Actualizacion Tipo Documento
	public function actualizaTipoDocumento($document){
		$this->salida = $this->objSalida->seteaSalida("ActualizaTipoDocumento",$document);
		$this->validaActualizaTipoDocumento($document);
		if ($this->valid){
			$id = $this->_tDocs->actualizaTipoDocumento($document);
			if($id){
				$this->salida['mensaje_dec']['mensaje'] = $this->traeTipoDocumentoPorId($id);
				$this->salida['mensaje_dec']['header']['glosaEstado'] = "Operación Exitosa";
			}else{
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"ActualizaTipoDocumentoErr");
			}
		}
		return $this->salida;		
	}

	private function validaActualizaTipoDocumento($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoActualizaTipoDocumento($document);
	}

	private function validaFormatoActualizaTipoDocumento($document){
		$this->validaUsuario($document);
		$this->validaDescripcionActTD($document);
		$this->validaCodigoActTD($document);
		$this->validaEmpresasActTD($document);
	}

	private function validaDescripcionActTD($document){
		if (isset($document['mensaje_dec']['mensaje']['nuevaDescripcion'])){
			if(empty($document['mensaje_dec']['mensaje']['nuevaDescripcion'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"DescripcionTipoDocumentoVacio");
			}
		}
	}

	private function validaCodigoActTD($document){
		$rut_usuario = $document['mensaje_dec']['header']['usuario'];
		if (isset($document['mensaje_dec']['mensaje']['codigoTipoDocumento'])){
			if(empty($document['mensaje_dec']['mensaje']['codigoTipoDocumento'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CodigoTipoDocumentoVacio");
			}
			else{
				if (!$this->_tDocs->existeTipoDocumentoCodigo($document['mensaje_dec']['mensaje']['codigoTipoDocumento'])){
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CodigoTipoDocumentoNoExiste");					
				}
			}
		}
	}

	private function validaEmpresasActTD($document){
		if (isset($document['mensaje_dec']['mensaje']['empresasSolicitadasAltas'])){
			if (is_array($document['mensaje_dec']['mensaje']['empresasSolicitadasAltas'])){
				$empresasSolicitadasAltas = $document['mensaje_dec']['mensaje']['empresasSolicitadasAltas'];
				if(is_array($empresasSolicitadasAltas)){
					if(count($empresasSolicitadasAltas)>0){
						foreach ($empresasSolicitadasAltas as $rutEmpresa) { 
							if(!$this->_usuarios->validaEmpresaExista($rutEmpresa)){
								$this->valid=false;
								$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresaSolicitadaNoExisteErr","empresasSolicitadasAltas",$rutEmpresa);						
							}
						}
					}
					else{
						$this->valid=false;
						$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresasSolicitadasErr","empresasSolicitadasAltas");
					}
				}
				else{
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresasSolicitadasErr","empresasSolicitadasAltas");
				}
			}
			else{
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"campoNoArreglo","empresasSolicitadasAltas");
			}
		}
		if (isset($document['mensaje_dec']['mensaje']['empresasSolicitadasBajas'])){
			if (is_array($document['mensaje_dec']['mensaje']['empresasSolicitadasBajas'])){
				$empresasSolicitadasBajas = $document['mensaje_dec']['mensaje']['empresasSolicitadasBajas'];
				if(is_array($empresasSolicitadasBajas)){
					if(count($empresasSolicitadasBajas)>0){
						foreach ($empresasSolicitadasBajas as $rutEmpresa) { 
							if(!$this->_usuarios->validaEmpresaExista($rutEmpresa)){
								$this->valid=false;
								$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresaSolicitadaNoExisteErr","empresasSolicitadasBajas",$rutEmpresa);						
							}
						}
					}
					else{
						$this->valid=false;
						$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresasSolicitadasErr","empresasSolicitadasBajas");
					}
				}
				else{
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresasSolicitadasErr","empresasSolicitadasBajas");
				}
			}
			else{
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"campoNoArreglo","empresasSolicitadasBajas");
			}
		}
	}

	// Fin Actualizacion de Tipo Documento
}
?>