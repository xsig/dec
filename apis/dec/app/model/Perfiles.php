<?php
namespace Dec\model;
use Dec\database\MongoDBConn as MongoDBConn;
use Dec\utils\Funciones as Funciones; 

class Perfiles {
	private static $ConnMDB;
	private $coll;
	private $func;
	public function __construct(){
		try{
			self::$ConnMDB = new MongoDBConn();
		}catch(MongoConnectionException $e){
			self::$ConnMDB = false;
		}catch(MongoException $e){
			self::$ConnMDB = false;
		}
		$this->func = new Funciones();
	}
	
	// Funciones Generales
    
    public function validaConexion(){
        if(self::$ConnMDB)
            return true;
        else
            return false;
    }
	
	//Fin Funciones Generales

	private function listaPerfiles(){
		return 	self::$ConnMDB->lista("perfiles");
	}
	
	public function traeListaPerfilesAdministradorSeguridad(){
		$_roles = new Roles();
		return $this->traeListaIdPerfiles($_roles->traeIdRolAdministradorCliente());
	}

	public function esPerfilAdministradorSeguridad($idPerfil){
		$listaIdPerfiles = $this->traeListaPerfilesAdministradorSeguridad();
		if (in_array($idPerfil,$listaIdPerfiles)){
			return true;
		}
		return false;
	}


	public function traeListaIdPerfilesPorListaRol($listaRol){
		$_perfil=array();
		$busqRoles = array('$in' => $listaRol);
		$busqueda = array("roles" => $busqRoles ,"estadoPerfil" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("perfiles", $busqueda);
		foreach($cursor as $item ){
			$_perfil[] =$item->_id;
		}
		return $_perfil;
	}


	public function traeListaIdPerfiles($idRol){
		$_perfil=array();
		$arregloRol = array();
		$arregloRol[] = $idRol;
		$busqRoles = array('$in' => $arregloRol);
		$busqueda = array("roles" => $busqRoles ,"estadoPerfil" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("perfiles", $busqueda);
		foreach($cursor as $item ){
			$_perfil[] =$item->_id;
		}
		return $_perfil;
	}
	public function traeListaIdPerfilesClientes($idRol){
		$_perfil="";
		$arregloRol = array();
		$arregloRol[] = $idRol;
		$busqRoles = array('$in' => $arregloRol);
		$busqueda = array("roles" => $busqRoles ,"estadoPerfil" => "ACTIVO" ,"tipoPerfil" => "CLIENTES");
		$cursor = self::$ConnMDB->busca("perfiles", $busqueda);
		foreach($cursor as $item ){
				$_perfil[] =$item->_id;
		}
		return $_perfil;
	}
	
	public function traeIdPerfilPorNombreYEmpresa($nombrePerfil,$idEmpresa){
		$IdPerfiles = array();
		$_perfilClientes = new PerfilesClientes();
		$busqueda = array("nombrePerfil" => $nombrePerfil, "estadoPerfil" => "ACTIVO", "tipoPerfil" => "CLIENTES" );
		$cursor = self::$ConnMDB->busca("perfiles", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item){
				$IdPerfiles[] = $item->_id;
			}	
		//}
		foreach ($IdPerfiles as $idPerfil) {
			if($_perfilClientes->existeIdsPerfilCliente($idPerfil,$idEmpresa)){
				return $idPerfil;
			}
		}
		return 0;
	}

	public function traeIdPerfilPorNombre($nombrePerfil){
		$perfilId = 0;
		$busqueda = array("nombrePerfil" => $nombrePerfil, "estadoPerfil" => "ACTIVO", "tipoPerfil" => "CLIENTES" );
		$cursor = self::$ConnMDB->busca("perfiles", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item){
				$perfilId = $item->_id;
			}	
		//}
		return $perfilId;
	}

	public function traeIdPerfilesPorNombre($nombrePerfil){
		$perfilesIds = array();
		$busqueda = array("nombrePerfil" => $nombrePerfil, "estadoPerfil" => "ACTIVO" );
		$cursor = self::$ConnMDB->busca("perfiles", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item){
				$perfilesIds[] = $item->_id;
			}	
		//}
		return $perfilesIds;
	}
	
	public function traeIdPerfilesPorIdCliente($IdCliente){
		$perfilesIds = array();
		$busqueda = array("idCliente" => $IdCliente );
		$cursor = self::$ConnMDB->busca("PerfilCliente", $busqueda);
		foreach($cursor as $item){
			$perfilesIds[] = $item->idPerfil;
		}	
		return $perfilesIds;
	}

	public function traePerfilesAdministradorSeguridad(){
		$_roles = new Roles();
		$idRol = $_roles->traeIdRolAdministradorCliente();

	}

	public function traeRolesPorIdPerfil($IdPerfil){
		$rolesIds = array();
		$busqueda = array("_id" => $IdPerfil );
		$cursor = self::$ConnMDB->busca("perfiles", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item){
				$rolesIds = $item->roles;
			}	
		//}
		return $rolesIds;
	}

	public function traePerfilesPorEmpresa($rut){
		$_roles = new Roles();
		$perfiles=array();
		$perf = array();
		$busqueda = array( "empresa" => $rut  );
		$cursor = self::$ConnMDB->busca("perfiles", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
				    $perf['id']=$item->_id;
					$perf['codigoPerfil'] =$item->nombrePerfil;
					$perf['nombrePerfil'] =$item->descripcionPerfil;
					$perf['roles'] = $_roles->traeListaNombresRolPorListaId($item->roles);
					$perf['estado'] =$item->estadoPerfil;
					$perf['fechaUltEstado'] =$item->fechaUltimoEstado;
//					$listaClientes  = $_perfilesClientes->traeListaRutClientePorPerfiles($item->_id);
//					$perf['empresasAsignadas'] = $listaClientes;
					$perfiles[] =$perf;
			}
		//}
		return $perfiles;
	}

	public function traePerfilPorListaId($listaIdPerfiles){
		$_perfilesClientes = new PerfilesClientes();
		$_roles = new Roles();
		$listaClientes = array();
		$perfiles=array();
		$perf = array();
		$listaID = array('$in' => $listaIdPerfiles);
		$busqueda = array( "_id" => $listaID  );
		$cursor = self::$ConnMDB->busca("perfiles", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
					$perf['codigoPerfil'] =$item->nombrePerfil;
					$perf['nombrePerfil'] =$item->descripcionPerfil;
					$perf['roles'] = $_roles->traeListaNombresRolPorListaId($item->roles);
					$perf['estado'] =$item->estadoPerfil;
					$perf['fechaUltEstado'] =$item->fechaUltimoEstado;
//					$listaClientes  = $_perfilesClientes->traeListaRutClientePorPerfiles($item->_id);
//					$perf['empresasAsignadas'] = $listaClientes;
					$perfiles[] =$perf;
			}
		//}
		return $perfiles;
	}

	public function traeNombrePerfilPorListaId($listaIdPerfiles){
		$_perfilesClientes = new PerfilesClientes();
		$listaClientes = array();
		$perfiles=array();
		$perf = array();
		$listaID = array('$in' => $listaIdPerfiles);
		$busqueda = array( "_id" => $listaID  );
		$cursor = self::$ConnMDB->busca("perfiles", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
					$perf[] =$item->nombrePerfil;
			}
		//}
		return $perf;
	}

	public function traePerfilesPorListaId($listaIdPerfiles){
		$_perfilesClientes = new PerfilesClientes();
		$_roles = new Roles();
		$listaClientes = array();
		$perfiles=array();
		$perf = array();
		$listaID = array('$in' => $listaIdPerfiles);
		$busqueda = array( "_id" => $listaID  );
		$cursor = self::$ConnMDB->busca("perfiles", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
					$perf['codigoPerfil'] =$item->nombrePerfil;
					$perf['nombrePerfil'] =$item->descripcionPerfil;
					$perf['roles'] = $_roles->traeListaNombresRolPorListaId($item->roles);
					$perf['estado'] =$item->estadoPerfil;
					$perf['fechaUltEstado'] =$item->fechaUltimoEstado;
					$listaClientes  = $_perfilesClientes->traeListaRutClientePorPerfiles($item->_id);
					$perf['empresasAsignadas'] = $listaClientes;
					$perfiles[] =$perf;
			}
		//}
		return $perfiles;
	}

	public function estadosValidosPerfiles($estadoValidar){
		$estadoVal = strtoupper($estadoValidar);
		if($estadoVal == "ACTIVO" ||
		   $estadoVal == "INACTIVO" )
			return true;
		return false;
	}

	public function buscaPerfilesFiltros($busquedaPerfil){
        $perfilesIds = array();
        $cursor = self::$ConnMDB->busca("perfiles", $busquedaPerfil);
        //if($cursor->count()>0){
            foreach($cursor as $item){
                $perfilesIds[] = $item->_id;
            }   
        //}
        return $perfilesIds;
    }

    public function existePerfil($empresa, $nombrePerfil){
        $busqueda = array('nombrePerfil' => $nombrePerfil, "empresa" => $empresa );
        $cursor = self::$ConnMDB->busca("perfiles", $busqueda);
        foreach($cursor as $item){
            return true;
        }
        return false;
    }

    public function ingresaPerfil($document){
    	$rol = new Roles();
    	$cliente = new Clientes();
    	$usuario = new Usuarios();
    	$idCliente = 0;
    	$_idPerfilCliente = 0;
    	$arrRoles = $rol->traeListaIdRolPorListaNombres($document['mensaje_dec']['mensaje']['roles']);
		$rut_usuario = $document['mensaje_dec']['header']['usuario'];
		$empresa = $document['mensaje_dec']['mensaje']['empresa'];
    	$doc_perfil = array(
    		"roles" =>  $arrRoles,
			"nombrePerfil"  =>  $document['mensaje_dec']['mensaje']['nombrePerfil'],
			"descripcionPerfil"  =>  $document['mensaje_dec']['mensaje']['descripcionPerfil'],
			"empresa" => $empresa,
			"estadoPerfil"  =>  "ACTIVO",
			"tipoPerfil"  =>  "CLIENTES" ,
			"fechaUltimoEstado" => date("Y-m-d H:i:s")
    	);
		$_id =  self::$ConnMDB->ingresa("perfiles",$doc_perfil,"perfiles_id");

		$datos_cliente = $cliente->traeClientePorRut($empresa);
		if(!isset($datos_cliente->perfiles))
			$datos_cliente->perfiles=array();
		array_push($datos_cliente->perfiles,$_id);
		self::$ConnMDB->actualiza("clientes", array("_id"=>$datos_cliente->_id), array("perfiles"=>$datos_cliente->perfiles));

        return $_id;
    }

    public function actualizaPerfil($document){
    	$rol = new Roles();
    	$cliente = new Clientes();
    	$usuario = new Usuarios();
    	$perfCliente = new PerfilesClientes();
    	$idCliente = 0;
    	$_idPerfilCliente = 0;
    	$nombrePerfil = $document['mensaje_dec']['mensaje']['nombrePerfil'];
    	$idPerfil = $this->traeIdPerfilPorNombre($nombrePerfil);
    	$docModPerfil = array();
    	$rut_usuario = $document['mensaje_dec']['header']['usuario']; 
     	$idUsuario = $usuario->traeIdUsuarioPorRut($rut_usuario); 
		$arrRoles = $rol->traeListaIdRolPorListaNombres($document['mensaje_dec']['mensaje']['roles']);
		$docModPerfil['roles'] = $arrRoles;
		$docModPerfil['fechaUltimoEstado'] = date("Y-m-d H:i:s");
		self::$ConnMDB->actualizaPorId("perfiles",$idPerfil,$docModPerfil);
			
/*     	if (isset($document['mensaje_dec']['mensaje']['nuevaDescripcion'])){
    		$docModPerfil['descripcionPerfil'] = $document['mensaje_dec']['mensaje']['nuevaDescripcion'];
    	}

    	if (isset($document['mensaje_dec']['mensaje']['rolesAltas'])){
    		$arrRoles = $this->func->arrayMerge( $arrRoles ,$rol->traeListaIdRolPorListaNombres($document['mensaje_dec']['mensaje']['rolesAltas']));
    	}

    	if (isset($document['mensaje_dec']['mensaje']['rolesBajas'])){
    		$arrRoles = array_diff($arrRoles, $rol->traeListaIdRolPorListaNombres($document['mensaje_dec']['mensaje']['rolesBajas']));
    	}
    	
    	$arrRoles = $this->func->remove_duplicates_array($arrRoles);
		$docModPerfil['roles'] = $arrRoles;
		$docModPerfil['fechaUltimoEstado'] = date("Y-m-d H:i:s");


        if(self::$ConnMDB->actualizaPorId("perfiles",$idPerfil,$docModPerfil)){
        	$hayCambiosEnEmpresa =  0;
        	if(isset($document['mensaje_dec']['mensaje']['empresasSolicitadasAltas'])){
        		$arrClientes  = $document['mensaje_dec']['mensaje']['empresasSolicitadasAltas'];
		        foreach($arrClientes as $rutEmpresa) { 
					$idCliente = $cliente->traeIdClientePorRut($rutEmpresa);
			        $doc_perfilCliente= array(
			        	"idCliente" => $idCliente,
			        	"idPerfil" => $idPerfil,
			   			"estado"  => "ACTIVO" 
			        );
			        if (!self::$ConnMDB->buscaId("PerfilCliente",$doc_perfilCliente)){
				        $_idPerfilCliente =  self::$ConnMDB->ingresa("PerfilCliente",$doc_perfilCliente,"PerfilCliente_id");

				        $doc_perfilamiento = array(
							   "idPerfilCliente" => $_idPerfilCliente,
							   "idUsuario" => $idUsuario,
							   "estado" => "ACTIVO"
				        	);
				        if (!self::$ConnMDB->buscaId("perfilamiento",$doc_perfilamiento)){
				        	$_idPerfilamiento=  self::$ConnMDB->ingresa("perfilamiento",$doc_perfilamiento,"perfilamiento_id");	
				        }
				        		        	
			        }
		    	}

        	}
        	if(isset($document['mensaje_dec']['mensaje']['empresasSolicitadasBajas'])){
         		$arrClientes  = $document['mensaje_dec']['mensaje']['empresasSolicitadasBajas'];
		        foreach($arrClientes as $rutEmpresa) { 
					$idCliente = $cliente->traeIdClientePorRut($rutEmpresa);

			        $doc_perfilCliente= array(
			        	"idCliente" => $idCliente,
			        	"idPerfil" => $idPerfil
			        );
			        $_idPerfilCliente =  self::$ConnMDB->buscaId("PerfilCliente",$doc_perfilCliente);

			        if(self::$ConnMDB->eliminaPorId("PerfilCliente",$_idPerfilCliente)){
						$doc_perfilamiento = array(
							"idPerfilCliente" => $_idPerfilCliente,
							"idUsuario" => $idUsuario
						);
						$_idPerfilamiento =  self::$ConnMDB->buscaId("perfilamiento",$doc_perfilamiento);

						self::$ConnMDB->eliminaPorId("perfilamiento",$_idPerfilamiento);	
			        }

		    	}  		
        	}


        }
*/
        return $idPerfil;
    }

	public function traePerfilPorIdAct($IdPerfil){
		$_perfilesClientes = new PerfilesClientes();
		$_roles = new Roles();
		$listaClientes = array();
		$perfiles=array();
		$perf = array();
		$busqueda = array( "_id" => $IdPerfil  );
		$cursor = self::$ConnMDB->busca("perfiles", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
					$perf['codigoPerfil'] =$item->nombrePerfil;
					$perf['nombrePerfil'] =$item->descripcionPerfil;
					$perf['roles'] = $this->func->remove_duplicates_array($_roles->traeListaNombresRolPorListaId($item->roles));
					$perf['estado'] =$item->estadoPerfil;
					$perf['fechaUltEstado'] =$item->fechaUltimoEstado;
					$listaClientes  =$this->func->remove_duplicates_array( $_perfilesClientes->traeListaRutClientePorPerfiles($item->_id));
					$perf['empresasAsignadas'] = $listaClientes;
					$perfiles[] =$perf;
			}
		//}
		return $perfiles;
	}

	public function traePerfilPorIdCreacion($IdPerfil){
		$_perfilesClientes = new PerfilesClientes();
		$_roles = new Roles();
		$listaClientes = array();
		$perfiles=array();
		$perf = array();
		$busqueda = array( "_id" => $IdPerfil  );
		$cursor = self::$ConnMDB->busca("perfiles", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item ){
					$perf['codigoPerfil'] =$item->nombrePerfil;
					$perf['nombrePerfil'] =$item->descripcionPerfil;
					$perf['roles'] = $this->func->remove_duplicates_array($_roles->traeListaNombresRolPorListaId($item->roles));
					$perf['estado'] =$item->estadoPerfil;
					$perf['fechaUltEstado'] =$item->fechaUltimoEstado;
					$listaClientes  =$this->func->remove_duplicates_array( $_perfilesClientes->traeListaRutClientePorPerfiles($item->_id));
					$perf['empresasAsignadas'] = $listaClientes;
					$perfiles[] =$perf;
			}
		//}
		return $perfiles;
	}
    public function traePerfilPorId($idPerfil){
		$perfilesIds = array();
		$busqueda = array("_id" => $idPerfil,  "tipoPerfil" => "CLIENTES" );
		$cursor = self::$ConnMDB->busca("perfiles", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item){
				$perfilesIds = $item;
			}	
		//}
		return $perfilesIds;
	}

	public function traeNombrePerfilPorId($idPerfil){
		$nombre ="";
		$busqueda = array("_id" => $idPerfil,  "tipoPerfil" => "CLIENTES" );
		$cursor = self::$ConnMDB->busca("perfiles", $busqueda);
		//if($cursor->count()>0){
			foreach($cursor as $item){
				$nombre = $item->nombrePerfil;
			}	
		//}
		return $nombre;
	}
}
?>