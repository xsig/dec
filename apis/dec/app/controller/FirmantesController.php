<?php
namespace Dec\controller;
use Dec\model\Perfiles as Perfiles;
use Dec\model\Roles as Roles;
use Dec\model\Usuarios as Usuarios;
use Dec\model\Clientes as Clientes;
use Dec\model\Documentos as Documentos;
use Dec\model\Firmantes as Firmantes;
use Dec\model\Salida as _Salida;
use Dec\error\MensajeError as MensajeError;
use Dec\utils\Funciones as Funciones;
use Dec\model\Logging as Logging;
use Dec\model\Perfilamientos as Perfilamientos;

class FirmantesController{
	private $valid;
	private $objSalida;
	private $salida;
	private $func;
	private $_usuarios;
	private $_roles;
	private $_perfiles;
	
	public function __construct(){
		$this->objSalida = new _Salida();
		$this->func = new Funciones();
		$this->Mensaje = new MensajeError();
		$this->_clientes = new Clientes();
		$this->_roles = new Roles();
		$this->_perfiles = new Perfiles();
		$this->_usuarios = new Usuarios();
	}
	
	public function busquedaFirmantes($document){
		$this->salida = $this->objSalida->seteaSalida("busquedaFirmantes",$document);
		$this->validaBusquedaFirmantes($document);
		if ($this->valid){
			$this->traeBusquedaFirmantes($document);
		}
		return $this->salida;
	}

	public function firmarDocumento($document){
		$_documentos = new Documentos();
		$this->salida = $this->objSalida->seteaSalida("firmarDocumento",$document);
		$this->validaFirmarDocumento($document);
		if ($this->valid){
			$id = $this->ingresaFirmarDocumento($document);
			if($id){
				$this->salida['mensaje_dec']['mensaje'] = $_documentos->traeDocumentoPorId($id);
				$this->salida['mensaje_dec']['header']['glosaEstado'] = "Operación Exitosa";
			}else{
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"firmarDocumentoErr");
			}
		}
		return $this->salida;
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

	private function validaFirmarDocumento($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoFirmarDocumento($document);
	}

	public function validaFormatoFirmarDocumento($document){
		$this->validaHeaderFormatoFirmarDocumento($document);
		$this->validaMensajeFirmarDocumento($document);
	}

	private function validaHeaderFormatoFirmarDocumento($document){
		if (isset($document['mensaje_dec']['header']['empresa'])){
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
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"NoHeaderEmpresa","empresa");
		}

		if(isset($document['mensaje_dec']['header']['usuario'])){
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
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"UsuarioCampoRutVacioErr");	
		}
	}

	public function validaMensajeFirmarDocumento($document){
		$_documentos = new Documentos();
		$codigoDocAcepta = "";	
		$codigoFirma="";
		$rutFirmante="";
		$nombrePerfilFirmante="";
		$descripcionFirmante = "";
		$empresa = $document['mensaje_dec']['header']['empresa'];
		if(!isset($document['mensaje_dec']['mensaje']['codigoDocAcepta'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"codigoDocAceptaNoIngresado");	
		}
		else{
			$codigoDocAcepta = strtoupper($document['mensaje_dec']['mensaje']['codigoDocAcepta']) ;
		}
		if(!isset($document['mensaje_dec']['mensaje']['codigoFirma'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"codigoFirmaNoIngresado");
		}
		else{
			$codigoFirma = strtoupper($document['mensaje_dec']['mensaje']['codigoFirma']) ;
		}
		if(!isset($document['mensaje_dec']['mensaje']['rutFirmante'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"rutFirmanteNoIngresado");
		}
		else{
			$rutFirmante = strtoupper($document['mensaje_dec']['mensaje']['rutFirmante']) ;
		}
		if(!isset($document['mensaje_dec']['mensaje']['nombrePerfilFirmante'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"nombrePerfilFirmanteNoIngresado");
		}
		else{
			$nombrePerfilFirmante = strtoupper($document['mensaje_dec']['mensaje']['nombrePerfilFirmante']) ;
		}
		if(!isset($document['mensaje_dec']['mensaje']['descripcionFirmante'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"descripcionFirmanteNoIngresado");
		}
		else{
			$descripcionFirmante = strtoupper($document['mensaje_dec']['mensaje']['descripcionFirmante']) ;
		}

		if(!$_documentos->ExisteDocumentoPorIdAcepta($codigoDocAcepta)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"documentofirmarNoexiste");
		}
		else{
			if(!$_documentos->existeFirmanteEnDocumento($codigoDocAcepta,$empresa,$nombrePerfilFirmante )){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"firmanteNoexiste");
			}
			else{
				if(!$_documentos->sepuedeFirmarDocumento($codigoDocAcepta,$empresa,$nombrePerfilFirmante)){
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"firmanteOrdenPrioridadInvalido");
				}
				if($descripcionFirmante!="PERSONAL")
				{
					if(!$this->_usuarios->verificaAutorizacionFirma($rutFirmante,$empresa,$nombrePerfilFirmante)){
						$this->valid=false;
						$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"accesoNegado");
					}
				}
			}
		}
	}

	public function ingresaFirmarDocumento($document){
		$_firmante = new Firmantes();
		$codigoDocAcepta = strtoupper($document['mensaje_dec']['mensaje']['codigoDocAcepta']) ;
		$codigoFirma = strtoupper($document['mensaje_dec']['mensaje']['codigoFirma']) ;
		$rutFirmante = strtoupper($document['mensaje_dec']['mensaje']['rutFirmante']) ;
		$nombreFirmante = strtoupper($document['mensaje_dec']['mensaje']['nombreFirmante']) ;
		$nombrePerfilFirmante = strtoupper($document['mensaje_dec']['mensaje']['nombrePerfilFirmante']) ;
		$descripcionFirmante = strtoupper($document['mensaje_dec']['mensaje']['descripcionFirmante']) ;
		$rut_usuario=$document["mensaje_dec"]["header"]["usuario"];
		$rut_empresa=$document["mensaje_dec"]["header"]["empresa"];

		$idDoc = $_firmante->firmarDocumento(
					$rut_usuario,
					$rut_empresa,
					$codigoDocAcepta,
					$codigoFirma,
					$rutFirmante,
					$nombreFirmante,
					$nombrePerfilFirmante,
					$descripcionFirmante
			);
		return $idDoc;

	}

}

?>