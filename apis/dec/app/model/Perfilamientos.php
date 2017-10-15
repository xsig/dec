<?php
namespace Dec\model;
use Dec\database\MongoDBConn as MongoDBConn;
use Dec\model\Logging as Logging;
use Dec\model\Usuarios as Usuarios;
class Perfilamientos {
	private static $ConnMDB;
	private $coll;
	public function __construct(){
		self::$ConnMDB = new MongoDBConn();
	}
	
	public function validaConexion(){
		if(self::$ConnMDB)
			return true;
		else
			return false;
	}

	private function listaPerfiles(){
		return 	self::$ConnMDB->lista("perfilamiento");
	}

	public function traePerfilesUsuarioEmpresa($rutUsuario, $rutEmpresa)
	{
		$_perfiles = new Perfiles();
		$_usuarios = new Usuarios();
		
		$idUsuario=$_usuarios->traeIdUsuarioPorRut($rutUsuario);
		$perfiles=$_perfiles->traePerfilesPorEmpresa($rutEmpresa);
		$accesos=array();
		foreach($perfiles as $perfil)
		{
			$filtro = array("idUsuario" => $idUsuario, "idPerfil" => $perfil["id"] );
			$cursor = self::$ConnMDB->busca("perfilamiento", $filtro);
			foreach($cursor as $item)
			{
				$accesos[] = $perfil["nombrePerfil"];
			}
		}

		return $accesos;
	}	

	public function usuarioTienePerfil($rutUsuario, $perfil, $rutEmpresa)
	{
		$_perfiles = new Perfiles();
		$_usuarios = new Usuarios();
		$filtro = array( "nombrePerfil" => $perfil, "empresa" => $rutEmpresa );
		$listaPerfiles = $_perfiles->buscaPerfilesFiltros($filtro);
		if(count($listaPerfiles)==0)
			return false;
		else
		{
			$idPerfil=$listaPerfiles[0];
			$idUsuario=$_usuarios->traeIdUsuarioPorRut($rutUsuario);
			$filtro = array( "idPerfil" => $idPerfil, "idUsuario" => $idUsuario );
			$cursor = self::$ConnMDB->busca("perfilamiento", $filtro);
			$autorizado=false;
			foreach($cursor as $item)
			{
				$autorizado=true;
			}
			return $autorizado;
		}	
	}	

	public function validaPerfilDocumentos($rutUsuario, $rutEmpresa){
		$_rol = new Roles();
		$_perfiles = new Perfiles();
		$_perfilCliente = new PerfilesClientes(); 
		$_usuario = new Usuarios();
		$_cliente = new Clientes();
		$idUsuario = $_usuario->traeIdUsuarioPorRut($rutUsuario);
		$idRol = $_rol->traeIdPorNombreDocumentos();
		$listaPerfiles = $_perfiles->traeListaIdPerfiles($idRol);
		if (!isset($rutEmpresa)){
			if($this->validaPerfilamientoIdUsuarioListaPerfil($idUsuario,$listaPerfiles)){
				return true;
			}
		}
		else{
			$idCliente = $_cliente->traeIdClientePorRut($rutEmpresa);
			$listaPerfilClientes = $_perfilCliente->traeListaIdPerfilesClientes($listaPerfiles,$idCliente);
			if($this->validaPerfilamientoIdUsuarioListaPerfilCLiente($idUsuario,$listaPerfiles)){
				return true;
			}
		}
		return false;
	}

	public function validaPerfilServicio($servicio, $rutUsuario, $rutEmpresa){
        
		$_rol = new Roles();
		$_perfiles = new Perfiles();
		$_perfilCliente = new PerfilesClientes(); 
		$_usuario = new Usuarios();
		$_cliente = new Clientes();
		$idUsuario = $_usuario->traeIdUsuarioPorRut($rutUsuario);
		$idRol = $_rol->traeIdPorNombreServicio($servicio);
		$listaPerfiles = $_perfiles->traeListaIdPerfiles($idRol);
		$documento = array();
		$documento['servicio'] = $servicio;
		$documento['rutUsuario'] = $rutUsuario;
		$documento['rutEmpresa'] = $rutEmpresa;

		if (!isset($rutEmpresa)){
			$documento['empresanull'] = "SI";
			$documento['idUsuario'] = $idUsuario;
			$documento['listaPerfiles'] = $listaPerfiles;
			$documento['idRol'] = $idRol;

			if($this->validaPerfilamientoIdUsuarioListaPerfil($idUsuario,$listaPerfiles)){
				$documento['validaPerfilamientoIdUsuarioListaPerfil'] = "SI";

				return true;
			}
		}
		else{
			$documento['empresanull'] = "NO";
			$idCliente = $_cliente->traeIdClientePorRut($rutEmpresa);
			$listaPerfilClientes = $_perfilCliente->traeListaIdPerfilesClientes($listaPerfiles,$idCliente);
			if($this->validaPerfilamientoIdUsuarioListaPerfilCLiente($idUsuario,$listaPerfiles)){
				$documento['validaPerfilamientoIdUsuarioListaPerfilCLiente'] = "SI";
				return true;
			}
		}
		return false;
	}

	public function validaPerfilCliente($servicio, $rutUsuario, $rutEmpresa){
		$_rol = new Roles();
		$_perfiles = new Perfiles();
		$_perfilCliente = new PerfilCliente(); 
		$_usuario = new Usuarios();
		$_cliente = new Clientes();
		$idUsuario = $_usuario->traeIdUsuarioPorRut($rutUsuario);
		$idRol = $_rol->traeIdPorNombreCliente($servicio);
		$listaPerfiles = $_perfiles->traeListaIdPerfiles($idRol);
		if (!isset($rutEmpresa)){
			if($this->validaPerfilamientoIdUsuarioListaPerfil($idUsuario,$listaPerfiles)){
				return true;
			}
		}
		else{
			$idCliente = $_cliente->traeIdClientePorRut($rutEmpresa);
			$listaPerfilClientes = $_perfilCliente->traeListaIdPerfilesClientes($listaPerfiles,$idCliente);
			if($this->validaPerfilamientoIdUsuarioListaPerfilCLiente($idUsuario,$listaPerfiles)){
				return true;
			}
		}
		return false;
	}

	public function validaPerfilamientoIdUsuarioListaPerfil($idUsuario,$listaPerfiles){
		$busqList = array('$in' => $listaPerfiles);
		$busqueda = array("idPerfil" => $busqList , "idUsuario" => $idUsuario ,"estado" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("perfilamiento", $busqueda);
		foreach($cursor as $item ){
			return true;
		}
		return false;
	}

	public function validaPerfilamientoIdUsuarioListaPerfilCLiente($idUsuario,$listaPerfilCLiente){
		$busqList = array('$in' => $listaPerfilCLiente);
		$busqueda = array("idPerfilCliente" => $busqList , "idUsuario" => $idUsuario ,"estado" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("perfilamiento", $busqueda);
		foreach($cursor as $item ){
			return true;
		}
		return false;
	}

	public function agregaPerfilamiento($idUsuario, $idPerfil){
    	$doc_perfilamiento = array(
    		"estado" =>  "ACTIVO",
			"idUsuario"  =>  $idUsuario,
			"idPerfil"  =>  $idPerfil
    	);
        $_id =  self::$ConnMDB->ingresa("perfilamiento",$doc_perfilamiento,"perfilamiento_id");

	}
	public function traeListaIdPerfilamientoPorPerfilesCliente($idPerfilCliente){
		$_perfilamiento="";
		$busqueda = array("idPerfilCliente" => $idPerfilCliente , "estado" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("perfilamiento", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
					$_perfilamiento[] =$item->_id;
			}
		//}
		return $_perfilamiento;
	}
	public function traeListaIdPerfilamientoPorCliente($idCliente){
		$_perfilamiento="";
		$busqueda = array("idCliente" => $idCliente , "estado" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("perfilamiento", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
					$_perfilamiento[] =$item->_id;
			}
		//}
		return $_perfilamiento;
	}
	
	public function traeListaIdPerfilamientoPorListaPerfilesCliente($idPerfilClienteList){
		$_perfilamiento="";
		$busqList = array('$in' => $idPerfilClienteList);
		$busqueda = array("idPerfilCliente" => $busqList , "estado" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("perfilamiento", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
					$_perfilamiento[] =$item->_id;
			}
		//}
		return $_perfilamiento;
	}
	public function traeListaIdPerfilamientoPorListaCliente($idClienteList){
		$_perfilamiento="";
		$busqList = array('$in' => $idClienteList);
		$busqueda = array("idCliente" => $busqList , "estado" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("perfilamiento", $busqueda);
		foreach($cursor as $item ){
				$_perfilamiento[] =$item->_id;
		}
		return $_perfilamiento;
	}

	public function esAdministradorSeguridadCliente($rutUsuario, $rutEmpresa){
		$_perfilCliente = new PerfilCliente();
		$_usuario =  new Usuarios();
		$idUsuario = $_usuario->traeIdUsuarioPorRut($rutUsuario);
		$idsPerfiles = $_perfilCliente->traeListaPerfilesAdministradorSeguridadPorCliente($rutEmpresa);
		$idsPerfilCliente = $_perfilCliente->traeListaPerfilesClientesAdministradorSeguridadPorCliente($rutEmpresa);

		$_perfilamiento="";
		$busqList = array('$in' => $idsPerfilCliente);
		$busqueda = array("idPerfilCliente" => $busqList ,"idUsuario" => $idUsuario, "estado" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("perfilamiento", $busqueda);
		foreach($cursor as $item ){
			return true;
		}
		return false;
	}

	public function validaPerfilamientoPerfilClienteIdUsuario($idPerfilClienteList, $idUsuario){
		$_perfilamiento="";
		$busqList = array('$in' => $idPerfilClienteList);
		$busqueda = array("idPerfilCliente" => $busqList , "idUsuario" => $idUsuario ,"estado" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("perfilamiento", $busqueda);
		foreach($cursor as $item ){
			return true;
		}
		return false;
	}

	public function validaPerfilamientoIdUsuarioPerfilCliente($idPerfilCliente, $idUsuario){
		$_perfilamiento="";
		$busqueda = array("idPerfilCliente" => $idPerfilCliente , "idUsuario" => $idUsuario ,"estado" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("perfilamiento", $busqueda);
		foreach($cursor as $item ){
			return true;
		}
		return false;
	}

	public function validaPerfilamientoIdUsuarioPerfil($idPerfil, $idUsuario){
		$_perfilamiento="";
		$busqueda = array("idPerfil" => $idPerfil , "idUsuario" => $idUsuario );
		$cursor = self::$ConnMDB->busca("perfilamiento", $busqueda);
		foreach($cursor as $item ){
			return true;
		}
		return false;
	}
}