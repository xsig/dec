<?php
namespace Dec\model;
use Dec\database\MongoDBConn as MongoDBConn;
use Dec\utils\Funciones as Funciones;
use Dec\error\MensajeError as MensajeError;

class Clientes {
	private static $dataDB;
	private static $ConnMDB;
	private $coll;
	private $salida;
	private $valid;
	private $func;
	private $Mensaje;
	public function __construct(){
		try{
			self::$ConnMDB = new MongoDBConn();
		}catch(MongoConnectionException $e){
			self::$ConnMDB = false;
		}catch(MongoException $e){
			self::$ConnMDB = false;
		}

		$this->documentoSalida();
		$this->func = new Funciones();
		$this->Mensaje = new MensajeError();
	}
	
	
	public function validaConexion(){
		if(self::$ConnMDB)
			return true;
		else
			return false;
	}

	
	private function documentoSalida(){
		$this->salida= array(
			"mensaje_dec" => array(
				"header" => array(
					"usuario"=>"",
					"fecha"=>date("Y-m-d H:i:s"),
					"accion" =>"",
					"descripcion" => "",
					"estado" => 0,
					"glosaEstado" => "Operacion Exitosa",
					"listaErrores" => array()
				),
				"mensaje" => array(),
				"mensajeOriginal" => array(),
				"datosAuditoria" => array()
			)
		);
	}	
	
	private function seteaSalida($accion,$document){
		if (isset($document['mensaje_dec']['header']['usuario']) && empty($document['mensaje_dec']['header']['usuario']))
			$usuario = $document['mensaje_dec']['header']['usuario'];
		else
			$usuario = "Anonimo";
			
		$this->salida['mensaje_dec']['datosAuditoria']['fechaOperacion'] = date("Y-m-d H:i:s");
		$this->salida['mensaje_dec']['datosAuditoria']['usuarioOperacion'] = $usuario;	
		$this->salida['mensaje_dec']['header']['usuario'] = $usuario;
		
		switch ($accion){
			case 2:
				$this->salida['mensaje_dec']['header']['accion'] = 2;
				$this->salida['mensaje_dec']['header']['descripcion'] = "Busca Empresa - Selección de Empresas";	
			break;
			default:		
		}
		$this->salida['mensaje_dec']['mensajeOriginal'] = $document['mensaje_dec']['mensaje'];
	}
	


	public function buscaClientePorRut($rut){
		$cliente=array();
		$busqueda = array('datosDemograficos.Rut' => $rut );
		$cursor = self::$ConnMDB->busca("clientes", $busqueda);
		foreach($cursor as $result)
			$cliente = $result;
		return $cliente;
	}	
	
	public function traePerfilesPorRut($rut){
		$cliente=array();
		$listaId = array();
		$listaIdPerfiles = array();
		$_perfiles = array();
		$perfiles= new Perfiles();
		$busqueda = array('datosDemograficos.Rut' => $rut );
		$cursor = self::$ConnMDB->busca("clientes", $busqueda);

		foreach($cursor as $result){
			$listaIdPerfiles = $perfiles->traeIdPerfilesPorIdCliente($result->_id);
		}
		//$listaId = array_unique($listaIdPerfiles);
		if(count($listaIdPerfiles)){
			$_perfiles = $perfiles->traePerfilPorListaId($listaIdPerfiles);
		}
		//return $listaIdPerfiles;
		return $_perfiles;
	}
	public function traeNombresDeEmpresasPorListaDeRuts($listaRuts){
		$arrRutsEmpresas = array();
		foreach ($listaRuts as $rut ) {
			$arrEmpresas["rutEmpresa"] = $rut;
			$arrEmpresas["razonSocial"] = $this->traeRazonSocialPorRut($rut);
			$arrRutsEmpresas[] = $arrEmpresas;
		}
		return $arrRutsEmpresas;
	}

	public function traeRazonSocialPorRut($rut){
		$razonSocial = "";
		$busqueda = array('datosDemograficos.Rut' => $rut );
		$cursor = self::$ConnMDB->busca("clientes", $busqueda);
		foreach($cursor as $result){
			$razonSocial = $result->datosDemograficos->razonSocial;
		}
		return $razonSocial;
	}

	public function traeNombrePerfilesPorRutEmpresa($rut){
		$cliente=array();
		$listaId = array();
		$listaIdPerfiles = array();
		$_perfiles = array();
		$perfiles= new Perfiles();
		$busqueda = array('datosDemograficos.Rut' => $rut );
		$cursor = self::$ConnMDB->busca("clientes", $busqueda);

		foreach($cursor as $result){
			$listaIdPerfiles = $perfiles->traeIdPerfilesPorIdCliente($result->_id);
		}
		//$listaId = array_unique($listaIdPerfiles);
		if(count($listaIdPerfiles)){
			$_perfiles = $perfiles->traeNombrePerfilPorListaId($listaIdPerfiles);
		}

		//return $listaIdPerfiles;
		return $_perfiles;
	}

	public function existeClientePorRut($rut){
		$busqueda = array('datosDemograficos.Rut' => $rut );
		$cursor = self::$ConnMDB->busca("clientes", $busqueda);
		foreach ($cursor as $value) {
			return true;
		}
		return false;
	}	
	public function listaUsuarios(){
		$empresas = array();
		$cursor =self::$ConnMDB->lista("usuarios");
		foreach ($cursor as $empresa){
			unset($empresa->foto);
			unset($empresa->password);
			$empresas[] = $empresa;
		}
		return $empresas;
	}
	
	public function listaClientes(){
		$empresas = array();
		$cursor =self::$ConnMDB->lista("clientes");
		foreach ($cursor as $empresa){
			$empresas[] = $empresa;
		}
		return $empresas;
	}

	public function listaErrores(){
		$empresas = "";
		$cursor =self::$ConnMDB->lista("error");
		foreach ($cursor as $empresa){
			$empresas[] = $empresa;
		}
		return $empresas;
	}

	public function creaNuevoUsuario($document){
		$this->seteaSalida(2,$document);
		$this->valida($document);
		if ($this->valid){
			if($this->ingresaCliente($document)){
				if(!$this->enviaCorreo($document)){
					$this->salida['mensaje_dec']['header']['estado'] = 1;
					$this->salida['mensaje_dec']['header']['glosaEstado'] = "Operación con errores de Servicio";
					$error_arr=array(
						"codError" => 1130,
						"descripcionError"=>"No se pudo enviar el correo electronico"						
					);
					$this->salida['mensaje_dec']['header']['listaErrores'][] = $error_arr;
				}
			}else{
				$this->salida['mensaje_dec']['header']['estado'] = 1;
			}
		}
		else{
			$this->salida['mensaje_dec']['header']['estado'] = 1;
		}
		return $this->salida;
	}
	
	public function traeIdClientePorRut($rutCliente){
		$_cliente=0;
		$busqueda = array( "datosDemograficos.Rut" => $rutCliente , "estado" => "Activo" );
		$cursor = self::$ConnMDB->busca("clientes", $busqueda);
		foreach($cursor as $item ){
			$_cliente =$item->_id;
		}
		return $_cliente;
	}
	
	public function traeRutClientePorId($Id){
		$_cliente=0;
		$busqueda = array( "_id" => $Id , "estado" => "Activo" );
		$cursor = self::$ConnMDB->busca("clientes", $busqueda);

		foreach($cursor as $item ){
				$_cliente =$item->datosDemograficos->Rut;
		}

		return $_cliente;
	}

	public function traeIdClientePorListaRut($ListaRutCliente){
		$_cliente=array();
		$listaIRuts = array('$in' => $ListaRutCliente);
		$busqueda = array( "datosDemograficos.Rut" => $listaIRuts , "estado" => "Activo" );
		$cursor = self::$ConnMDB->busca("clientes", $busqueda);
		foreach($cursor as $item ){
				$_cliente[] =$item->_id;
		}

		return $_cliente;
	}

	public function traeRutsClientePorListaId($ListaIds){
		$_cliente=array();
		$lista = array('$in' => $ListaIds);
		$busqueda = array( "_id" => $lista , "estado" => "Activo" );
		$cursor = self::$ConnMDB->busca("clientes", $busqueda);

		foreach($cursor as $item ){
				$_cliente[] =$item->datosDemograficos->Rut;
		}

		return $_cliente;
	}
}