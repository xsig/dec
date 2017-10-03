<?php
namespace Dec\controller;
use Dec\model\SubTipoDocumentosFirmantes as SubTipoDocumentosFirmantes;
use Dec\model\TipoDocumentos as TipoDocumentos;
use Dec\model\OperaDocumentos as OperaDocumentos;
use Dec\model\Documentos as Documentos;
use Dec\model\Clientes as Clientes;
use Dec\model\Perfiles as Perfiles;
use Dec\model\Roles as Roles;
use Dec\model\Usuarios as Usuarios;
use Dec\model\Salida as _Salida;
use Dec\error\MensajeError as MensajeError;
use Dec\utils\Funciones as Funciones;
use Dec\model\Logging as Logging;

class OperaDocumentosController{
	private $valid;
	private $objSalida;
	private $salida;
	private $func;
	private $Mensaje;
	private $_clientes;
	private $_usuarios;	
	private $_roles;
	private $_perfiles;
	private $_operaDocumentos;

	public function __construct(){	
		$this->objSalida = new _Salida();
		$this->func = new Funciones();
		$this->Mensaje = new MensajeError();
		$this->_usuarios = new Usuarios();
		$this->_roles = new Roles();
		$this->_perfiles = new Perfiles();
		$this->_tiposDocumentos = new TipoDocumentos();
		$this->_clientes = new Clientes();
		$this->_operaDocumentos = new OperaDocumentos();
		$this->_Documentos = new Documentos();
	}

	// Apis OperaDocumentos 
	public function CargaDocumentos($document){
		$this->salida = $this->objSalida->seteaSalida("CargaDocumentos",$document);
		$this->validaCargaDocumentos($document);
		if ($this->valid){
			$this->ejecutaCargaDocumentos($document);
		}
		return $this->salida;
	}

	public function CargaOrdenDocumentos($document){
		$this->salida = $this->objSalida->seteaSalida("CargaOrdenDocumentos",$document);
		$this->validaCargaOrdenDocumentos($document);
		if ($this->valid){
			$this->ejecutaCargaOrdenDocumentos($document);
		}
		return $this->salida;
	}

	public function ConsultaDocumentos($document){
		$this->salida = $this->objSalida->seteaSalida("ConsultaDocumentos",$document);
		$this->validaConsultaDocumentos($document);
		if ($this->valid){
			$this->ejecutaConsultaDocumentos($document);
		}
		return $this->salida;
	}

	
	public function FirmantesDeDocumentos($document){
		$this->salida = $this->objSalida->seteaSalida("FirmantesDeDocumentos",$document);
		$this->validaFirmantesDeDocumentos($document);
		if ($this->valid){
			$this->ejecutaFirmantesDeDocumentos($document);
		}
		return $this->salida;
	}
	// Fin Apis OperaDocumentos 

	// Validaciones
	private function validaCargaDocumentos($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoCargaDocumentos($document);
	}

	private function validaFirmantesDeDocumentos($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoFirmantesDocumentos($document);
	}

	private function validaConsultaDocumentos($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoConsultaDocumentos($document);
	}

	private function validaConexion(){
		if(!$this->_operaDocumentos->validaConexion()){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"conexionErr");
		}		
	}

	private function validaDocumento($document){
		if (is_array($document))	
			return true;
		return false;
	}

	private function validaFormatoCargaDocumentos($document){
		$this->validaEmpresa($document);
		$this->validaTipoDocumentos($document);
		$this->validaSubTipoDocumentos($document);
	}

	private function validaFormatoFirmantesDocumentos($document){
		$this->validaEmpresa($document);
		$this->validaTipoDocumentos($document);
		$this->validaSubTipoDocumentos($document);
	}
	private function validaDocumentos($document){
		if (!isset($document['mensaje_dec']['mensaje']['documentos'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"DocumentosEnOperaDocumentosVacio", "documentos");	
		}
		else{
			if (!is_array($document['mensaje_dec']['mensaje']['documentos'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"DocumentosEnOperaDocumentosNoArreglo", "documentos");					
			}
			foreach ($document['mensaje_dec']['mensaje']['documentos'] as  $docs) {
				$this->validaDocArchivo($docs);
				$this->validaDocNombre($docs);
				$this->validaDocTamano($docs);
				$this->validaDocUsuarioCreador($docs);	
				$this->validaDocComentario($docs);	
			}
		}
	}

	private function validaFirmantes($document){
		if (!isset($document['mensaje_dec']['mensaje']['documentos'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"DocumentosEnOperaDocumentosVacio", "documentos");	
		}
		else{
			if (!is_array($document['mensaje_dec']['mensaje']['documentos'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"DocumentosEnOperaDocumentosNoArreglo", "documentos");					
			}
			foreach ($document['mensaje_dec']['mensaje']['documentos'] as  $docs) {
				$this->validaDocArchivo($docs);
				$this->validaDocNombre($docs);
				$this->validaDocTamano($docs);
				$this->validaDocUsuarioCreador($docs);	
				$this->validaDocComentario($docs);	
			}
		}
	}

	private function validaFormatoConsultaDocumentos($document){
		$this->validaConsultaEmpresa($document);
		$this->validaConsultaTipoDocumentos($document);
		$this->validaConsultaSubTipoDocumentos($document);
		$this->validaConsultaDocumentosFiltros($document);
	}

	// Fin Validaciones

	private function validaEmpresa($document){
		if (!isset($document['mensaje_dec']['header']['empresa']) ){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresaOperaDocumentoVacio", "empresa");			
		}
		else{
			if (!$this->func->valida_rut($document['mensaje_dec']['header']['empresa'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"clienteRutInvalido", "empresa", $document['mensaje_dec']['header']['empresa']);	
			}
			else{
				if(!$this->_clientes->existeClientePorRut($document['mensaje_dec']['header']['empresa'])){
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"ClienteNoExisteErr", "empresa", $document['mensaje_dec']['header']['empresa']);	
				}
			}

		}
	}

	private function validaConsultaEmpresa($document){
		if (!isset($document['mensaje_dec']['header']['empresa']) ){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresaOperaDocumentoVacio", "empresa");			
		}
		else{
			if (!$this->func->valida_rut($document['mensaje_dec']['header']['empresa'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"clienteRutInvalido", "empresa", $document['mensaje_dec']['header']['empresa']);	
			}
			else{
				if(!$this->_clientes->existeClientePorRut($document['mensaje_dec']['header']['empresa'])){
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"ClienteNoExisteErr", "empresa", $document['mensaje_dec']['header']['empresa']);	
				}
			}

		}
	}

	private function validaTipoDocumentos($document){
		if (!isset($document['mensaje_dec']['mensaje']['TipoDocumentos'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"TipoDocumentosEnOperaDocumentos", "TipoDocumentos");	
		}
		else{

		}


	}

	private function validaConsultaTipoDocumentos($document){
		if (!isset($document['mensaje_dec']['mensaje']['TipoDocumentos'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"TipoDocumentosEnOperaDocumentos", "TipoDocumentos");	
		}
		else{

		}
	}

	private function validaSubTipoDocumentos($document){
		if (!isset($document['mensaje_dec']['mensaje']['subTipoDocumentos'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"SubTipoDocumentosEnOperaDocumentos", "subTipoDocumentos");	
		}
		else{
			
		}
	}

	private function validaConsultaSubTipoDocumentos($document){
		if (!isset($document['mensaje_dec']['mensaje']['subTipoDocumentos'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"SubTipoDocumentosEnOperaDocumentos", "subTipoDocumentos");	
		}
		else{
			
		}
	}

	private function validaDocArchivo($docs){
		if (!isset($docs['archivo'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"ArchivoDocumentosEnOperaDocumentosNoExiste", "documentos");	
		}
	}

	private function validaDocNombre($docs){
		if (!isset($docs['nombre'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"NombreDocumentosEnOperaDocumentosNoExiste", "documentos");	
		}
	}

	private function validaDocTamano($docs){
		if (!isset($docs['tamano'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"TamañoDocumentosEnOperaDocumentosNoExiste", "documentos");	
		}
	}

	private function validaDocUsuarioCreador($docs){
		if (!isset($docs['usuarioCreador'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"UsuarioCreadorDocumentosEnOperaDocumentosNoExiste", "documentos");	
		}
	}

	private function validaDocComentario($docs){
		if (!isset($docs['comentario'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"ComentarioDocumentosEnOperaDocumentosNoExiste", "documentos");	
		}
	}

	private function ejecutaCargaDocumentos($document){
		$this->salida['mensaje_dec']['mensaje'] = $this->_operaDocumentos->ejecutaCargaDocumentos($document);
	}

	private function validaConsultaDocumentosFiltros($document){
		$this->validaIdDocumento($document);
		$this->validaEstadoDocumento($document);
	}

	private function validaIdDocumento($document){
		$_Documentos = new Documentos();
		if (isset($document['mensaje_dec']['mensaje']['Filtro']['idDocumento'])){
			if (!$_Documentos->ExisteDocumentoPorIdAcepta($document['mensaje_dec']['mensaje']['Filtro']['idDocumento'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"IdDocumentosEnOperaDocumentosNoExiste", "documentos");		
			}	
		}
	}

	private function validaEstadoDocumento($document){
		$_Documentos = new Documentos();
		if (isset($document['mensaje_dec']['mensaje']['Filtro']['estado'])){
			if (!$_Documentos->ExisteDocumentoPorEstado($document['mensaje_dec']['mensaje']['Filtro']['estado'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EstadoEnOperaDocumentosNoExiste", "documentos");		
			}	
		}
	}

	private function ejecutaConsultaDocumentos($document){
		$_Documentos = new Documentos();
		$doc_busqueda = array();
		if (isset($document['mensaje_dec']['mensaje']['Filtro']['idDocumento'])){
			$doc_busqueda['idAcepta'] = $document['mensaje_dec']['mensaje']['Filtro']['idDocumento'];
		}
		if (isset($document['mensaje_dec']['mensaje']['Filtro']['estado'])){
			$doc_busqueda['estado'] = $document['mensaje_dec']['mensaje']['Filtro']['estado'];
		}
		$doc_busqueda['empresa'] = $document['mensaje_dec']['header']['empresa'];

		$this->salida['mensaje_dec']['mensaje'] = $_Documentos->buscaDocumentosFiltros($doc_busqueda);

	}

	private function ejecutaFirmantesDeDocumentos($document){
		$_SubTipoDocumentosFirmantes = new SubTipoDocumentosFirmantes();

		$empresa = $document['mensaje_dec']['header']['empresa'];
		$codDoc = $document['mensaje_dec']['mensaje']['TipoDocumentos'];
		$codSubTDoc = $document['mensaje_dec']['mensaje']['subTipoDocumentos'];

		$this->salida['mensaje_dec']['mensaje'] = $_SubTipoDocumentosFirmantes->traeFirmantesSubTipoDocumentos($empresa, $codDoc, $codSubTDoc);
	}

}
?>