<?php
namespace Dec\controller;
use Dec\model as Model;
use Dec\error\MensajeError as MensajeError;
use Dec\utils\Funciones as Funciones;

class SubTipoDocumentosController{
	private $valid;
	private $objSalida;
	private $salida;
	private $func;
	private $Mensaje;
	private $_clientes;
	private $_usuarios;	
	private $_roles;
	private $_perfiles;
	private $_subtDocs;


	public function __construct(){
		$this->objSalida = new Model\Salida();
		$this->func = new Funciones();
		$this->Mensaje = new MensajeError();
		$this->_usuarios = new Model\Usuarios();
		$this->_clientes = new Model\Clientes();
		$this->_roles = new Model\Roles();
		$this->_perfiles = new Model\Perfiles();
		$this->_tDocs = new Model\TipoDocumentos();
		$this->_subtDocs= new Model\SubTipoDocumentos();

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
	public function busquedaSubTipoDocumento($document){
		$this->salida = $this->objSalida->seteaSalida("BusquedaSubTipoDocumento",$document);
		$this->validaBusquedaSubTipoDocumento($document);
		if ($this->valid){
			$this->traeBusquedaSubTipoDocumento($document);
		}
		return $this->salida;
	}

	private function validaBusquedaSubTipoDocumento($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoBusquedaSubTipoDocumento($document);
	}

	private function validaFormatoBusquedaSubTipoDocumento($document){
		$_perfilamiento = new Model\Perfilamientos();
		if (isset($document['mensaje_dec']['mensaje']['Filtros']['subtipoDocumento'])){
			$filtroNombre = $document['mensaje_dec']['mensaje']['Filtros']['subtipoDocumento'];
			if (empty($filtroNombre)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"filtroNombreSubTipoDocumentoVacio","subtipoDocumento");
			}
		}
		if (isset($document['mensaje_dec']['mensaje']['Filtros']['nombreSubTipoDocumento'])){
			$filtroDescripcion = $document['mensaje_dec']['mensaje']['Filtros']['nombreSubTipoDocumento'];
			if (empty($filtroDescripcion)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"filtroDescripcionPerfVacio","nombreSubTipoDocumento");
			}
		}
		if (isset($document['mensaje_dec']['mensaje']['Filtros']['codigo'])){
			$filtroCodigo = $document['mensaje_dec']['mensaje']['Filtros']['codigo'];
			if (empty($filtroCodigo)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"filtroCodigoSubTipoDocumentoVacio","codigo");
			}
		}
		if (isset($document['mensaje_dec']['mensaje']['Filtros']['estado'])){
			$filtroEstado = $document['mensaje_dec']['mensaje']['Filtros']['estado'];
			if (empty($filtroEstado)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"filtroSubEstadoTipoDocumentoVacio","estado");
			}
			if(!$this->_tDocs->estadosValidosTipoDocumentos($filtroEstado)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"filtroEstadoSubTipoDocumentoNoValido","estado");				
			}

		}
		$rut_usuario = "0";
		$rut_empresa = "0";

		$rut_usuario = $document['mensaje_dec']['header']['usuario'];
		$rut_empresa = $document['mensaje_dec']['header']['empresa'];

		// Por el momento se quita la validacion de perfiles
		// se debe cambiar esto y descomentar
		// if (!$_perfilamiento->validaPerfilDocumentos($rut_usuario, $rut_empresa)){
		// 		$this->valid=false;
		// 		$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"UsuarioEmpresaSinPermiso","empresa", "buscar");	
		// }
	}

	private function traeBusquedaSubTipoDocumento($document){

		$validaBusq = 1;
		$usuariosBuscado ="";
		$busqueda = array();
		$filtroIdSubTipoDocumento = "";
		$filtroNombreSubTipoDocumento = "";
		$filtroDescripcionSubTipoDocumento = "";
		$filtroCodigoSubTipoDocumento = "";
		$filtroEstadoSubTipoDocumento = "";
		$listaEmpresas = "";

		$rut_usuario = $document['mensaje_dec']['header']['usuario'];
		$rut_empresa = $document['mensaje_dec']['header']['empresa'];
		
		if (isset($document['mensaje_dec']['mensaje']['Filtros']['idSubTipoDocumento'])){
			if ($document['mensaje_dec']['mensaje']['Filtros']['idSubTipoDocumento']!=NULL)
				$filtroIdTipoDocumento = $document['mensaje_dec']['mensaje']['Filtros']['idSubTipoDocumento'];
		}	

		if (isset($document['mensaje_dec']['mensaje']['Filtros']['subtipoDocumento'])){
			if ($document['mensaje_dec']['mensaje']['Filtros']['subtipoDocumento']!=NULL)
				$filtroNombreSubTipoDocumento = $document['mensaje_dec']['mensaje']['Filtros']['subtipoDocumento'];
		}	
			
		if (isset($document['mensaje_dec']['mensaje']['Filtros']['nombreSubTipoDocumento'])){
			if ($document['mensaje_dec']['mensaje']['Filtros']['nombreSubTipoDocumento']!=NULL)
				$filtroDescripcionSubTipoDocumento= $document['mensaje_dec']['mensaje']['Filtros']['nombreSubTipoDocumento'];
		}

		if (isset($document['mensaje_dec']['mensaje']['Filtros']['codigo'])){
			if ($document['mensaje_dec']['mensaje']['Filtros']['codigo']!=NULL)
				$filtroCodigoSubTipoDocumento= $document['mensaje_dec']['mensaje']['Filtros']['codigo'];
		}

		if(isset($document['mensaje_dec']['mensaje']['Filtros']['estado'])){
			if ($document['mensaje_dec']['mensaje']['Filtros']['estado']!=NULL)
				$filtroEstadoSubTipoDocumento = $document['mensaje_dec']['mensaje']['Filtros']['estado'];
		}

		
		$rutEmpresasPermitidas = array();
		$idEmpresasPermitidas = array();
		$empresasId = array();
		$ClientesBusq = array();
		
		//$ClientesBusq = $_perfilamiento->validaPerfilDocumentos($rut_usuario, $rut_empresa);
		
		// if(isset($document['mensaje_dec']['mensaje']['empresa'])){
		// 	if ($document['mensaje_dec']['mensaje']['empresa']!=NULL)
		// 		$ClientesBusq = $document['mensaje_dec']['mensaje']['empresa'];
		// }

		// if(count($ClientesBusq)>0 && is_array($ClientesBusq)){
		// 	$empresasId = $this->_clientes->traeIdClientePorListaRut($ClientesBusq);
		// }
		
		//$Clientes = array_unique(array_merge($idEmpresasPermitidas,$empresasFiltro));
		
		
		// Busqueda de Tipo Documento
		$busquedaSubTipoDocumento =  array();
		if ($filtroIdSubTipoDocumento != "" ){
			$busquedaSubTipoDocumento['_id'] = $filtroIdSubTipoDocumento;
		}
		if ($filtroNombreSubTipoDocumento != "" ){
			$busquedaSubTipoDocumento['nombre'] =array('$regex' => $filtroNombreSubTipoDocumento);
		}
		if ($filtroDescripcionSubTipoDocumento != "" ){
			$busquedaSubTipoDocumento['descripcion'] =array('$regex' => $filtroDescripcionSubTipoDocumento);
		}
		if ($filtroCodigoSubTipoDocumento != "" ){
			$busquedaSubTipoDocumento['codigo'] =array('$regex' => $filtroCodigoSubTipoDocumento);
		}
		if ($filtroEstadoSubTipoDocumento != "" ){
			$busquedaSubTipoDocumento['estado'] = $filtroEstadoSubTipoDocumento;
		}
		else{
			$busquedaSubTipoDocumento['estado'] = 'ACTIVO';
		}


		$tipoSubDocumentosId = $this->_subtDocs->buscaSubTipoDocumentosFiltros($busquedaSubTipoDocumento);
		
		
		$listaSubTipoDocumentos = array();

		$listaSubTipoDocumentos = $this->_subtDocs->traeSubTipoDocumentosYRutsPorListaId($tipoSubDocumentosId);

		$this->salida['mensaje_dec']['mensaje'] = $listaSubTipoDocumentos;
	}
	// Fin de Busqueda de Perfiles


	// Inicio Creacion de Tipo Documento
	public function creaSubTipoDocumento($document){
		$this->salida = $this->objSalida->seteaSalida("creaSubTipoDocumento",$document);
		$this->validaCrearSubTipoDocumento($document);
		if ($this->valid){
			$id = $this->_subtDocs->ingresaSubTipoDocumento($document);
			if($id){
				$this->salida['mensaje_dec']['mensaje'] = $this->traeSubTipoDocumentoPorId($id);
				$this->salida['mensaje_dec']['header']['glosaEstado'] = "Operación Exitosa";
			}else{
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"IngresoSubTipoDocumentoErr");
			}
		}
		return $this->salida;
	}

	public function traeSubTipoDocumentoPorId($id){
		return $this->_subtDocs->traeSubTipoDocumentoPorId($id);
	}

	private function validaCrearSubTipoDocumento($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoCreaSubTipoDocumento($document);
	}

	private function validaFormatoCreaSubTipoDocumento($document){
		$this->validaUsuario($document);
		$this->validaEmpresaSTD($document);
		$this->validaNombreCreaSubTD($document);
		$this->validaDescripcionCreaSubTD($document);
		$this->validaCodigoCreaSubTD($document);
		$this->validaTipoDocumentoCreaSubTD($document);
		$this->validaFirmantesCreaSubTD($document);
	}

	private function validaEmpresaSTD($document){
		$rut_empresa = $document['mensaje_dec']['header']['empresa'];
		if (!$this->func->valida_rut($rut_empresa)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutInvalidoErrNeg");
		}
		if (empty($rut_empresa)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRutVacioErrNeg");
		}
		if (!$this->_clientes->existeClientePorRut($rut_empresa)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"ClienteNoExisteErr", "empresa", $document['mensaje_dec']['header']['empresa']);	
		}
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

	private function validaNombreCreaSubTD($document){
		$rut_usuario = $document['mensaje_dec']['header']['usuario'];
		if (isset($document['mensaje_dec']['mensaje']['subtipoDocumento'])){
			if(empty($document['mensaje_dec']['mensaje']['subtipoDocumento'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"NombreSubTipoDocumentoVacio");
			}
			else{
				if ($this->_subtDocs->existeSubTipoDocumento($rut_usuario, $document['mensaje_dec']['mensaje']['subtipoDocumento'])){
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"NombreSubTipoDocumentoYaExiste");
				}
			}
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"NombreSubTipoDocumentoNoIngresado");
		}
	}

	private function validaDescripcionCreaSubTD($document){
		if (isset($document['mensaje_dec']['mensaje']['descripcion'])){
			if(empty($document['mensaje_dec']['mensaje']['descripcion'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"DescripcionSubTipoDocumentoVacio");
			}
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"DescripcionTipoDocumentoNoIngresado");
		}
	}

	private function validaCodigoCreaSubTD($document){
		$rut_empresa = $document['mensaje_dec']['header']['empresa'];
		if (isset($document['mensaje_dec']['mensaje']['codigo'])){
			if(empty($document['mensaje_dec']['mensaje']['codigo'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CodigoSubTipoDocumentoVacio");
			}
			else{
				if ($this->_subtDocs->existeSubTipoDocumentoEmpresa($rut_empresa, $document['mensaje_dec']['mensaje']['codigo'])){
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CodigoSubTipoDocumentoYaExiste");
				}
			}
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CodigoSubTipoDocumentoNoIngresado");
		}
	}

	private function validaTipoDocumentoCreaSubTD($document){
		if(isset($document['mensaje_dec']['mensaje']['tipoDocumento'])){
			if(!$this->_tDocs->existeTipoDocumentoCodigo($document['mensaje_dec']['mensaje']['tipoDocumento'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CodigoTipoDocumentoNoExisteEnSTD","tipoDocumento");						
			}
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CodigoTipoDocumentoNoIngresadoEnSTD","tipoDocumento");
		}
	}

	private function validaFirmantesCreaSubTD($document){
		if(isset($document['mensaje_dec']['mensaje']['firmantes'])){
			if(is_array($document['mensaje_dec']['mensaje']['firmantes'])){
				if(count($document['mensaje_dec']['mensaje']['firmantes'])>0){
					$firmantes = $document['mensaje_dec']['mensaje']['firmantes'];
					foreach ($firmantes as $firmante) {
						$nombPerfil = "";
						$descPerfil = "";
						$orden = 0;	
						if(isset($firmante['nombrePerfil'])){
							$nombPerfil = $firmante['nombrePerfil'];						
						}				
						if(isset($firmante['orden'])){
							$orden =$firmante['orden'];						
						}	
						if(isset($firmante['descripcionPerfil'])){
							$descPerfil = $firmante['descripcionPerfil'];						
						}
						if(!is_int($orden) || $orden == 0){
							$this->valid=false;
							$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"FirmantesOrdenTipoDocumentoNoValido");							
						}
						if($nombPerfil == ""){
							$this->valid=false;
							$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"perfilFirmanteTipoDocumentoNoValido");							
						}
						if($descPerfil == ""){
							$this->valid=false;
							$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"descFirmanteTipoDocumentoNoValido");							
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
	public function actualizaSubTipoDocumento($document){
		$this->salida = $this->objSalida->seteaSalida("ActualizaSubTipoDocumento",$document);
		$this->validaActualizaSubTipoDocumento($document);
		if ($this->valid){
			$id = $this->_subtDocs->actualizaSubTipoDocumento($document);
			if($id){
				$this->salida['mensaje_dec']['mensaje'] = $this->traeSubTipoDocumentoPorId($id);
				$this->salida['mensaje_dec']['header']['glosaEstado'] = "Operación Exitosa";
			}else{
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"ActualizaSubTipoDocumentoErr");
			}
		}
		return $this->salida;		
	}

	private function validaActualizaSubTipoDocumento($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoActualizaSubTipoDocumento($document);
	}

	private function validaFormatoActualizaSubTipoDocumento($document){
		$this->validaUsuario($document);
		$this->validaDescripcionActSubTD($document);
		$this->validaCodigoActSubTD($document);
	}

	private function validaDescripcionActSubTD($document){
		if (isset($document['mensaje_dec']['mensaje']['nuevaDescripcion'])){
			if(empty($document['mensaje_dec']['mensaje']['nuevaDescripcion'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"DescripcionSubTipoDocumentoVacio");
			}
		}
	}

	private function validaCodigoActSubTD($document){
		$rut_usuario = $document['mensaje_dec']['header']['usuario'];
		if (isset($document['mensaje_dec']['mensaje']['codigoSubTipoDocumento'])){
			if(empty($document['mensaje_dec']['mensaje']['codigoSubTipoDocumento'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CodigoSubTipoDocumentoVacio");
			}
			else{
				if (!$this->_subtDocs->existeSubTipoDocumentoCodigo($document['mensaje_dec']['mensaje']['codigoSubTipoDocumento'])){
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CodigoSubTipoDocumentoNoExiste");					
				}
			}
		}
	}

	// Fin Actualizacion de Tipo Documento
}
?>