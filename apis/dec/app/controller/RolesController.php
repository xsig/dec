<?php
namespace Dec\controller;
use Dec\model as Model;
use Dec\error\MensajeError as MensajeError;
use Dec\utils\Funciones as Funciones;

class RolesController{
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

	// Inicio Busqueda de Roles
	public function busquedaRoles($document){
		$this->salida = $this->objSalida->seteaSalida("BusquedaRoles",$document);
		$this->validaBusquedaRoles($document);
		if ($this->valid){
			$this->traeBusquedaRoles($document);
		}
		return $this->salida;
	}

	private function validaBusquedaRoles($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoBusquedaRoles($document);
	}

	private function validaFormatoBusquedaRoles($document){
	}

	private function traeBusquedaRoles($document){
		$validaBusq = 1;
		$busqueda = array();
		$listaEmpresas = "";

		$rut_usuario = $document['mensaje_dec']['header']['usuario'];
		
		$busquedaRol =  array();
		$busquedaRol['estadoRol'] = 'ACTIVO';
		$busquedaRol['tipoRol'] = 'CLIENTES';
		$listaRoles = $this->_roles->buscaRolesFiltros($busquedaRol);
		$this->salida['mensaje_dec']['mensaje']['Lista Roles'] = $listaRoles;
	}
}
?>