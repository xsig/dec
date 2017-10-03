<?php
namespace Dec\controller;
use Dec\model as Model;
use Dec\error\MensajeError as MensajeError;
use Dec\utils\Funciones as Funciones;

class SubTipoDocumentosFirmantesController{
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
		$this->_subtDocs = new Model\TipoDocumentos();
		$this->_subtDocsFirmantes = new Model\SubTipoDocumentosFirmantes();
	}
	// Funciones Generales
	private function validaConexion(){
		if(!$this->_subtDocsFirmantes->validaConexion()){
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

	// public function existeTipoDocumentoCliente($idCliente, $idTipoDocumento){
	// 	$busqueda = array(
	// 			"idTipoDocumento" => $idTipoDocumento ,
	// 			"idCliente" => $idCliente
	// 		);

	// 	if ($_tDocsCliente->existeTipoDocumentoCliente($busqueda)){
	// 		return true;
	// 	}
	// 	return false;
	// }

	// Fin Actualizacion de Tipo Documento Cliente
}
?>