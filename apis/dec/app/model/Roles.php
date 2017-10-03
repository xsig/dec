<?php
namespace Dec\model;
use Dec\database\MongoDBConn as MongoDBConn;

class Roles {
	private static $ConnMDB;

	public function __construct(){
		self::$ConnMDB = new MongoDBConn();
	}

	public function listaRoles(){
		return self::$ConnMDB->lista("roles");
	}

	public function traeRolPorId($id){
		$_rol="";
		$busqueda = array('_id' => $id );
		$cursor = self::$ConnMDB->busca("roles", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $rol){
				$_rol[] = $rol;
			}
		//}
		return $_rol;
	}

	public function traeListaRolPorIds($idList){
		$_rol="";
		$busqList = array('$in' => $idList);
		$busqueda = array('_id' => $busqList );
		$cursor = self::$ConnMDB->busca("roles", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $rol){
				$_rol[] = $rol;
			}
		//}
		return $_rol;
	}

	public function traeListaIdRolPorListaNombres($nombreList){
		$_rol=array();
		$busqList = array('$in' => $nombreList);
		$busqueda = array('nombreRol' => $busqList );
		$cursor = self::$ConnMDB->busca("roles", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $rol){
				$_rol[] = $rol->_id;
			}
		//}
		return $_rol;
	}

	public function traeListaNombresRolPorListaId($idList){
		$_rol=array();
		if(count($idList)>0){
			$busqList = array('$in' => $idList);
			$busqueda = array('_id' => $busqList );
			$cursor = self::$ConnMDB->busca("roles", $busqueda);
			//if($cursor->count()>0){
				foreach($cursor as $rol){
					$_rol[] = $rol->nombreRol;
				}
			//}
		}
		return $_rol;
	}

	public function traeRolAdministradorCliente(){
		$_rol=array();
		$busqueda = array(
		   		"nombreRol" => "ADMINISTRADOR DE SEGURIDAD",
   				"estadoRol" => "ACTIVO",
   				"tipoRol" => "CLIENTES" );
		$cursor = self::$ConnMDB->busca("roles", $busqueda);
		foreach($cursor as $rol){
			$_rol[] = $rol;
		}
		return $_rol[0];
	}
    public function traeIdRolAdministradorCliente(){
		$rolId="";
		$busqueda = array(
		   		"nombreRol" => "ADMINISTRADOR DE SEGURIDAD",
   				"estadoRol" => "ACTIVO",
   				"tipoRol" => "ADMINISTRADOR_SEGURIDAD" );
		$cursor = self::$ConnMDB->busca("roles", $busqueda);

		foreach($cursor as $rol){
			$rolId = $rol->_id;
		}
		return $rolId;
	}

	public function validaRolExistaPorId($rolId){
		// Falta buscar la empresa del rut del usuario
        $busqueda = array('_id' => $rolId );
        $cursor = self::$ConnMDB->busca("roles", $busqueda);
        foreach($cursor as $rol){
            return true;
        }
        return false;
	}
	public function validaRolExistaPorNombre($rolNombre){
		// Falta buscar la empresa del rut del usuario
        $busqueda = array('nombreRol' => $rolNombre );
        $cursor = self::$ConnMDB->busca("roles", $busqueda);
        foreach($cursor as $rol){
            return true;
        }
        return false;
	}

	public function traeIdPorNombreServicio($rolNombre){
		$idRol = 0;
        $busqueda = array('nombreRol' => $rolNombre , "estadoRol" => "ACTIVO",
   				"tipoRol" => "SERVICIO");
        $cursor = self::$ConnMDB->busca("roles", $busqueda);
        foreach($cursor as $rol){
            $idRol = $rol->_id;
        }
        return $idRol;
	}

	public function traeIdPorNombreDocumentos(){
		$idRol = 0;
        $busqueda = array('nombreRol' => "ADMINISTRAR DOCUMENTOS", "estadoRol" => "ACTIVO",
   				"tipoRol" => "CLIENTES");
        $cursor = self::$ConnMDB->busca("roles", $busqueda);
        foreach($cursor as $rol){
            $idRol = $rol->_id;
        }
        return $idRol;
	}

	public function traeIdPorNombreCliente($rolNombre){
		$idRol = 0;
        $busqueda = array('nombreRol' => $rolNombre , "estadoRol" => "ACTIVO",
   				"tipoRol" => "CLIENTES");
        $cursor = self::$ConnMDB->busca("roles", $busqueda);
        foreach($cursor as $rol){
            $idRol = $rol->_id;
        }
        return $idRol;
	}
}