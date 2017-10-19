<?php
namespace Dec\controller;
use Dec\model\Perfiles as Perfiles;
use Dec\model\Roles as Roles;
use Dec\model\Usuarios as Usuarios;
use Dec\model\Clientes as Clientes;
use Dec\model\Salida as _Salida;
use Dec\error\MensajeError as MensajeError;
use Dec\utils\Funciones as Funciones;

class ClientesController{
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
	
	public function ClientePorRut($document){
		$this->salida = $this->objSalida->seteaSalida("ClientePorRut",$document);
		$this->validaBuscaClientePorRut($document);
		if ($this->valid){
			$this->buscaClientePorRut($document['mensaje_dec']['mensaje']['empresaSolicitada']);
		}
		return $this->salida;
	}
	
	private function validaBuscaClientePorRut($document){
		$this->valid=true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoBuscaClientePorRut($document);
		$this->validaRutEmpresa($document);
	}
	
	private function validaConexion(){
		if(!$this->_clientes->validaConexion()){
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
	
	private function validaFormatoBuscaClientePorRut($document){
		if (!isset($document['mensaje_dec']['mensaje']['empresaSolicitada'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"empresaSolicitadaErr");
		}
	}
	
	private function validaRutEmpresa($document){
		$rut = $document['mensaje_dec']['mensaje']['empresaSolicitada'];
		if (!$this->func->valida_rut($rut)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"clienteRutInvalido","empresaSolicitada");
		}
		if (empty($rut)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"clienteRutVacio","empresaSolicitada");	
		}
	}
	
	private function buscaClientePorRut($rut){
		if (!$this->_clientes->existeClientePorRut($rut)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"ClienteNoExisteErr");			
		}
		else{
			$cliente = array();
			$cliente = $this->_clientes->buscaClientePorRut($rut);
			$this->salida['mensaje_dec']['mensaje']['rut'] = $cliente->datosDemograficos->Rut;
			$this->salida['mensaje_dec']['mensaje']['razonSocial'] = $cliente->datosDemograficos->razonSocial;
		}
	}

	private function validaPerfilesEmpresa($document){
		$this->valid=true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoPerfilesEmpresa($document);
	}
	
	private function buscaPerfilPorRut($rut){
		if (!$this->_clientes->existeClientePorRut($rut)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"ClienteNoExisteErr");			
		}
		else{
			$cliente = array();
			$cliente = $this->_clientes->buscaPerfilesPorRut($rut);
			$this->salida['mensaje_dec']['mensaje']['rut'] = $cliente->datosDemograficos->Rut;
			$this->salida['mensaje_dec']['mensaje']['razonSocial'] = $cliente->datosDemograficos->razonSocial;
		}
	}

	private function validaFormatoPerfilesEmpresa($document){
		if (isset($document['mensaje_dec']['mensaje']['empresa'])){
			$rut = $document['mensaje_dec']['mensaje']['empresa'];
			if (!$this->func->valida_rut($rut)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"clienteRutInvalido","empresa");
			}
			else{
				if(!$this->_usuarios->validaEmpresaExista($rut)){
						$this->valid=false;
						$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresaSolicitadaNoExisteErr","empresa",$rut);						
					}
			}
			if (empty($rut)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"clienteRutVacio","empresa");	
			}			
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"empresaSolicitadaErr","empresa");		
		}
	
	}
	
	private function traePerfilesEmpresa($document){
		$rut = $document['mensaje_dec']['mensaje']['empresa'];
		$this->salida['mensaje_dec']['mensaje']['empresa'] =$rut;
		$this->salida['mensaje_dec']['mensaje']['Lista Perfiles'] = $this->_perfiles->traePerfilesPorEmpresa($rut);
	}

	public function PerfilesEmpresa($document){
		$this->salida = $this->objSalida->seteaSalida("perfilesCliente",$document);
		$this->validaPerfilesEmpresa($document);
		if ($this->valid){
			$this->traePerfilesEmpresa($document);
		}
		return $this->salida;
	}
	
	public function listaClientes(){
		return $this->_clientes->listaClientes();
	}

	public function listaUsuarios(){
		return $this->_clientes->listaUsuarios();
	}

	public function listaErrores(){
		return $this->_clientes->listaErrores();
	}
	
	
}
?>