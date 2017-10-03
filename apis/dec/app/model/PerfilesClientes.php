<?php
namespace Dec\model;
use Dec\database\MongoDBConn as MongoDBConn;

class PerfilesClientes {
	private static $ConnMDB;
	private $coll;
	public function __construct(){
		self::$ConnMDB = new MongoDBConn();
	}
	
	private function listaPerfiles(){
		return 	self::$ConnMDB->lista("PerfilesClientes");
	}
	
	public function traeListaIdPerfilesPorCliente($idCliente){
		$_perfil="";
		$busqueda = array("idCliente" => $idCliente , "estado" => "ACTIVO"  );
		$cursor = self::$ConnMDB->busca("PerfilCliente", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
					$_perfil[] =$item->_id;
			}
		//}
	
		return $_perfil;
	}
	public function traeIdPerfilPorIdPerfilCliente($idPerfilCliente){
		$_perfil=0;
		$busqueda = array("_id" => $idPerfilCliente , "estado" => "ACTIVO"  );
		$cursor = self::$ConnMDB->busca("PerfilCliente", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
				$_perfil =$item->idPerfil;
			}
		//}
	
		return $_perfil;
	}

	public function traeListaIdClientePorPerfiles($idPerfil){
		$_cliente="";
		$busqueda = array("idPerfil" => $idPerfil , "estado" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("PerfilCliente", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
					$_cliente[] =$item->_id;
			}
		//}
	
		return $_cliente;
	}
	
	public function traeListaRutClientePorPerfiles($idPerfil){
		$_Mclientes = new Clientes();
		$_cliente=array();
		$busqueda = array("idPerfil" => $idPerfil );
		$cursor = self::$ConnMDB->busca("PerfilCliente", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
					$_cliente[] =$_Mclientes->traeRutClientePorId($item->idCliente);
			}
		//}
	
		return $_cliente;
	}

	public function traeIdPerfilCliente($idPerfil,$idCliente){
		$busqueda = array("idPerfil" => $idPerfil , "idCliente" => $idCliente );
		$cursor = self::$ConnMDB->busca("PerfilCliente", $busqueda);
		//if($cursor->count()>0){
			foreach ($cursor as $perfCli) {
				return	$perfCli->_id;
			}
		//}
		return 0;
	}

	public function existeIdsPerfilCliente($idPerfil,$idCliente){
		$busqueda = array("idPerfil" => $idPerfil , "idCliente" => $idCliente );
		$cursor = self::$ConnMDB->busca("PerfilCliente", $busqueda);
		foreach($cursor as $item ){
			return true;
		}
		return false;
	}

	public function traeListaIdPerfilesClientes($idPerfilList, $idCliente){
		$_cliente = array();
		$busqList = array('$in' => $idPerfilList);
		$busqueda = array("idPerfil" => $busqList , "idCliente" => $idCliente, "estado" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("PerfilCliente", $busqueda);
		foreach($cursor as $item ){
				$_cliente[] =$item->_id;
		}
		return $_cliente;
	}

	public function existePerfilCliente($busqueda){
		$cursor = self::$ConnMDB->busca("PerfilCliente", $busqueda);
		foreach($cursor as $item ){
			return true;
		}
		return false;
	}

	public function traeListaPerfilesAdministradorSeguridadPorCliente($rutEmpresa){
		$_roles = new Roles();
		$_pefiles = new Perfiles();
		$_cliente = new Clientes();
		$IdsPerfiles = array();
		$idCliente = $_cliente->traeIdClientePorRut($rutEmpresa);
		$idPerfiles = $_pefiles->traeListaIdPerfiles($_roles->traeIdRolAdministradorCliente());
		foreach ($idPerfiles as $idPerfil) {
			if ($this->existeIdsPerfilCliente($idPerfil,$idCliente)){
				$IdsPerfiles[] = $idPerfil;
			}
		}
		return $IdsPerfiles;
	}

	public function traeListaPerfilesClientesAdministradorSeguridadPorCliente($rutEmpresa){
		$_roles = new Roles();
		$_pefiles = new Perfiles();
		$_cliente = new Clientes();
		$IdsPerfilesClientes = array();
		$idCliente = $_cliente->traeIdClientePorRut($rutEmpresa);
		$idPerfiles = $_pefiles->traeListaIdPerfiles($_roles->traeIdRolAdministradorCliente());
		foreach ($idPerfiles as $idPerfil) {
			if ($this->existeIdsPerfilCliente($idPerfil,$idCliente)){
				$IdsPerfilesClientes[] = $this->traeIdPerfilCliente($idPerfil,$idCliente);
			}
		}
		return $IdsPerfilesClientes;
	}

	public function ingresaListaPerfilesClientes(){
		
	}
}