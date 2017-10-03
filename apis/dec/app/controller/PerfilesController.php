<?php
namespace Dec\controller;
use Dec\model as Model;
use Dec\error\MensajeError as MensajeError;
use Dec\utils\Funciones as Funciones;

class PerfilesController{
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
	// Fin Funciones Generales

	// Inicio Busqueda de Perfiles
	public function busquedaPerfil($document){
		$this->salida = $this->objSalida->seteaSalida("BusquedaPerfiles",$document);
		$this->validaBusquedaPerfil($document);
		if ($this->valid){
			$this->traeBusquedaPerfil($document);
		}
		return $this->salida;
	}

	private function validaBusquedaPerfil($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoBusquedaPerfil($document);
	}

	private function validaFormatoBusquedaPerfil($document){
		if (isset($document['mensaje_dec']['mensaje']['Filtros']['codigoPerfil'])){
			$filtroNombre = $document['mensaje_dec']['mensaje']['Filtros']['codigoPerfil'];
			if (empty($filtroNombre)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"filtroNombrePerfVacio","codigoPerfil");
			}
		}
		if (isset($document['mensaje_dec']['mensaje']['Filtros']['nombrePerfil'])){
			$filtroDescripcion = $document['mensaje_dec']['mensaje']['Filtros']['nombrePerfil'];
			if (empty($filtroDescripcion)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"filtroDescripcionPerfVacio","nombrePerfil");
			}
		}
		if (isset($document['mensaje_dec']['mensaje']['Filtros']['estado'])){
			$filtroEstado = $document['mensaje_dec']['mensaje']['Filtros']['estado'];
			if (empty($filtroEstado)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"filtroEstadoPerfVacio","estado");
			}
			if(!$this->_perfiles->estadosValidosPerfiles($filtroEstado)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"filtroEstadoPerfNoValido","estado");				
			}
		}

		if(isset($document['mensaje_dec']['mensaje']['Filtros']['empresas'])){
			$listaEmpresas = $document['mensaje_dec']['mensaje']['Filtros']['empresas'];
			if(is_array($listaEmpresas) && count($listaEmpresas) > 0){
				foreach($listaEmpresas as $emp){
					if (!$this->func->valida_rut($emp)){
						$this->valid=false;
						$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutEmpInvalidoErr", "empresas",$emp);
					}
					if(!$this->_clientes->existeClientePorRut($emp)){
						$this->valid=false;
						$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"ClienteNoExisteErr", "empresas",$emp);						
					}
				}
			}
			else{
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoEmpNoValido");				
			}
		}
	}

	private function traeBusquedaPerfil($document){
		$validaBusq = 1;
		$usuariosBuscado ="";
		$busqueda = array();
		$filtroIdPerfil = "";
		$filtroNombrePerfil = "";
		$filtroDescripcionPerfil = "";
		$filtroEstadoPerfil = "";
		$listaEmpresas = "";

		$rut_usuario = $document['mensaje_dec']['header']['usuario'];
		
		if (isset($document['mensaje_dec']['mensaje']['Filtros']['idPerfil'])){
			if ($document['mensaje_dec']['mensaje']['Filtros']['idPerfil']!=NULL)
				$filtroIdPerfil = $document['mensaje_dec']['mensaje']['Filtros']['idPerfil'];
		}	

		if (isset($document['mensaje_dec']['mensaje']['Filtros']['codigoPerfil'])){
			if ($document['mensaje_dec']['mensaje']['Filtros']['codigoPerfil']!=NULL)
				$filtroNombrePerfil = $document['mensaje_dec']['mensaje']['Filtros']['codigoPerfil'];
		}	
			
		if (isset($document['mensaje_dec']['mensaje']['Filtros']['nombrePerfil'])){
			if ($document['mensaje_dec']['mensaje']['Filtros']['nombrePerfil']!=NULL)
				$filtroDescripcionPerfil = $document['mensaje_dec']['mensaje']['Filtros']['nombrePerfil'];
		}

		if(isset($document['mensaje_dec']['mensaje']['Filtros']['estado'])){
			if ($document['mensaje_dec']['mensaje']['Filtros']['estado']!=NULL)
				$filtroEstadoPerfil = $document['mensaje_dec']['mensaje']['Filtros']['estado'];
		}

		
		$rutEmpresasPermitidas = array();
		$idEmpresasPermitidas = array();
		$empresasId = array();
		$ClientesBusq = array();
		
		$ClientesBusq = $this->_usuarios->traeListaRutClientesAutorizadasPorRut($rut_usuario);
		
		// if(isset($document['mensaje_dec']['mensaje']['empresa'])){
		// 	if ($document['mensaje_dec']['mensaje']['empresa']!=NULL)
		// 		$ClientesBusq = $document['mensaje_dec']['mensaje']['empresa'];
		// }

		if(count($ClientesBusq)>0 && is_array($ClientesBusq)){
			$empresasId = $this->_clientes->traeIdClientePorListaRut($ClientesBusq);
		}
		
		//$Clientes = array_unique(array_merge($idEmpresasPermitidas,$empresasFiltro));
		
		
		// Busqueda de Perfil
		$busquedaPerfil =  array();
		if ($filtroIdPerfil != "" ){
			$busquedaPerfil['_id'] = $filtroIdPerfil;
		}
		if ($filtroNombrePerfil != "" ){
			// $busquedaPerfil['nombrePerfil'] = $filtroNombrePerfil;
			$busquedaPerfil['nombrePerfil'] =array('$regex' => $filtroNombrePerfil);
		}
		if ($filtroDescripcionPerfil != "" ){
			// $busquedaPerfil['descripcionPerfil'] = $filtroDescripcionPerfil;
			$busquedaPerfil['descripcionPerfil'] =array('$regex' => $filtroDescripcionPerfil);
		}
		
		if ($filtroEstadoPerfil != "" ){
			$busquedaPerfil['estadoPerfil'] = $filtroEstadoPerfil;
		}
		else{
			$busquedaPerfil['estadoPerfil'] = 'ACTIVO';
		}

		////////////////////////
		//Incorporar consulta si es cliente agregar este filtro en caso contrario no agregarlo
		////////////////////////
		$busquedaPerfil['tipoPerfil'] = 'Cliente';
		////////////////////////
		////////////////////////

		$PerfilesId = $this->_perfiles->buscaPerfilesFiltros($busquedaPerfil);
		
		////////////////////////
		//Incorporar consulta si tiene permisos necesarios para realizar consulta
		////////////////////////
		// $busquedaPerfil = array();
		// $PerfilesId = array();
		// if ($filtroPerfil != "" ){
		// 	$busquedaPerfil['nombrePerfil'] = $filtroPerfil;
		// 	$PerfilesId = $this->_perfiles->traeIdPerfilesPorNombre($busquedaUsuario);
		// }
		
		$listaPerfiles = array();

		$listaPerfiles = $this->_perfiles->traePerfilesPorListaId($PerfilesId);
		//$this->salida['mensaje_dec']['mensaje']['prueba'] = $busquedaPerfil;
		$this->salida['mensaje_dec']['mensaje']['Lista Perfil'] = $listaPerfiles;
	}
	// Fin de Busqueda de Perfiles

	/////////////////////////////////////////
	/////////////////////////////////////////

	// Inicio Creacion de Perfil
	public function creaPerfil($document){
		$this->salida = $this->objSalida->seteaSalida("CreaPerfil",$document);
		$this->validaCrearPerfil($document);
		if ($this->valid){
			$id = $this->_perfiles->ingresaPerfil($document);
			if($id){
				$this->salida['mensaje_dec']['mensaje'] = $this->_perfiles->traePerfilPorIdCreacion($id);
				$this->salida['mensaje_dec']['header']['glosaEstado'] = "Operación Exitosa";
			}else{
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"IngresoPerfilErr");
			}
		}
		return $this->salida;
	}

	private function validaCrearPerfil($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoCreaPerfil($document);
	}

	private function validaFormatoCreaPerfil($document){
		$this->validaUsuario($document);
		$this->validaNombre($document);
		$this->validaDescripcion($document);
		$this->validaRoles($document);
		$this->validaEmpresas($document);
	}

	private function validaUsuario($document){
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

	private function validaNombre($document){
		$rut_usuario = $document['mensaje_dec']['header']['usuario'];
		if (isset($document['mensaje_dec']['mensaje']['codigoPerfil'])){
			if(empty($document['mensaje_dec']['mensaje']['codigoPerfil'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoCodigoPerfilVacio");
			}
			else{
				if ($this->_perfiles->existePerfil($rut_usuario, $document['mensaje_dec']['mensaje']['codigoPerfil'])){
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoCodigoPerfilExiste");
				}
			}
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoCodigoPerfilNoIngresado");
		}
	}

	private function validaDescripcion($document){
		if (isset($document['mensaje_dec']['mensaje']['descripcion'])){
			if(empty($document['mensaje_dec']['mensaje']['descripcion'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoDescripcionPerfilVacio");
			}
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoDescripcionPerfilNoIngresado");
		}
	}

	private function validaRoles($document){
		$rolesInsertar = array();
		if (isset($document['mensaje_dec']['mensaje']['roles'])){
			if(empty($document['mensaje_dec']['mensaje']['roles'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRolesPerfilVacio");
			}
			else{
				if(is_array($document['mensaje_dec']['mensaje']['roles'])){
					$rolesInsertar = $document['mensaje_dec']['mensaje']['roles'];
					if(count($rolesInsertar)>0){
						foreach ($rolesInsertar as $rolIns) { 
							if(!$this->_roles->validaRolExistaPorNombre($rolIns)){
								$this->valid=false;
								$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRolesPerfilNoExiste","roles",$rolIns);						
							}
						}
					}
					else{
						$this->valid=false;
						$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRolesPerfilNoesArreglo");
					}					
				}
				else{
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRolesPerfilNoesArreglo");
				}
			}
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRolesPerfilNoIngresado");			
		}
	}

	private function validaEmpresas($document){
		$empresasSolicitadas = $document['mensaje_dec']['mensaje']['empresasSolicitadas'];
		if(is_array($empresasSolicitadas)){
			if(count($empresasSolicitadas)>0){
				foreach ($empresasSolicitadas as $rutEmpresa) { 
					if(!$this->_usuarios->validaEmpresaExista($rutEmpresa)){
						$this->valid=false;
						$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresaSolicitadaNoExisteErr","empresasSolicitadas",$rutEmpresa);						
					}
				}
			}
			else{
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresasSolicitadasErr","empresasSolicitadas");
			}
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresasSolicitadasErr","empresasSolicitadas");
		}
	}


	// Fin de Creacion de Perfil

	// Inicio Actualizacion Perfil
	public function actualizaPerfil($document){
		$this->salida = $this->objSalida->seteaSalida("ActualizaPerfil",$document);
		$this->validaActualizaPerfil($document);
		if ($this->valid){
			$id = $this->_perfiles->actualizaPerfil($document);
			if($id){
				$this->salida['mensaje_dec']['mensaje'] = $this->_perfiles->traePerfilPorIdAct($id);
				$this->salida['mensaje_dec']['header']['glosaEstado'] = "Operación Exitosa";
			}else{
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"IngresoPerfilErr");
			}
		}
		return $this->salida;		
	}

	private function validaActualizaPerfil($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoActualizaPerfil($document);
	}

	private function validaFormatoActualizaPerfil($document){
		$this->validaUsuario($document);
		$this->validaNombreAct($document);
		$this->validaDescripcionAct($document);
		$this->validaRolesAct($document);
		$this->validaEmpresasAct($document);
	}

	private function validaNombreAct($document){
		$rut_usuario = $document['mensaje_dec']['header']['usuario'];
		if (isset($document['mensaje_dec']['mensaje']['codigoPerfil'])){
			if(empty($document['mensaje_dec']['mensaje']['codigoPerfil'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoCodigoPerfilVacio");
			}
			else{
				if (!$this->_perfiles->existePerfil($rut_usuario, $document['mensaje_dec']['mensaje']['codigoPerfil'])){
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoCodigoPerfilNoExiste");
				}
			}
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoCodigoPerfilNoIngresado");
		}
	}

	private function validaDescripcionAct($document){
		if (isset($document['mensaje_dec']['mensaje']['nuevaDescripcion'])){
			if(empty($document['mensaje_dec']['mensaje']['nuevaDescripcion'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoDescripcionPerfilVacio");
			}
		}
	}

	private function validaRolesAct($document){
		$rolesEliminar = array();
		$rolesInsertar = array();


		if (isset($document['mensaje_dec']['mensaje']['rolesAltas'])){
			if(empty($document['mensaje_dec']['mensaje']['rolesAltas'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRolesPerfilVacio");
			}
			else{
				if(is_array($document['mensaje_dec']['mensaje']['rolesAltas'])){
					$rolesInsertar = $document['mensaje_dec']['mensaje']['rolesAltas'];
					if(count($rolesInsertar)>0){
						foreach ($rolesInsertar as $rolIns) { 
							if(!$this->_roles->validaRolExistaPorNombre($rolIns)){
								$this->valid=false;
								$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRolesPerfilNoExiste","rolesAltas",$rolIns);						
							}
						}
					}
					else{
						$this->valid=false;
						$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRolesPerfilNoesArreglo");
					}					
				}
				else{
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRolesPerfilNoesArreglo");
				}
			}
		}

		if (isset($document['mensaje_dec']['mensaje']['rolesBajas'])){
			if(empty($document['mensaje_dec']['mensaje']['rolesBajas'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRolesPerfilVacio");
			}
			else{
				if(is_array($document['mensaje_dec']['mensaje']['rolesBajas'])){
					$rolesEliminar = $document['mensaje_dec']['mensaje']['rolesBajas'];
					if(count($rolesEliminar)>0){
						foreach ($rolesEliminar as $rolEli) { 
							if(!$this->_roles->validaRolExistaPorNombre($rolEli)){
								$this->valid=false;
								$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRolesPerfilNoExiste","rolesBajas",$rolEli);						
							}
						}
					}
					else{
						$this->valid=false;
						$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRolesPerfilNoesArreglo");
					}					
				}
				else{
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRolesPerfilNoesArreglo");
				}
			}
		}


	}

	private function validaEmpresasAct($document){
		$empresasSolicitadasAltas = array();
		$empresasSolicitadasBajas = array();
		if(isset($document['mensaje_dec']['mensaje']['empresasSolicitadasAltas'] )){
			if(!empty($document['mensaje_dec']['mensaje']['empresasSolicitadasAltas'])){
				$empresasSolicitadasAltas = $document['mensaje_dec']['mensaje']['empresasSolicitadasAltas'];
				if(is_array($empresasSolicitadasAltas)){
					if(count($empresasSolicitadasAltas)>0){
						foreach ($empresasSolicitadasAltas as $rutEmpresa) { 
							if(!$this->_usuarios->validaEmpresaExista(strtoupper($rutEmpresa))){
								$this->valid=false;
								$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresaSolicitadaNoExisteErr","empresasSolicitadasAltas",$rutEmpresa);						
							}
						}
					}
					else{
						$this->valid=false;
						$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresasSolicitadasErr","empresasSolicitadasAltas");
					}
				}
				else{
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresasSolicitadasErr","empresasSolicitadasAltas");
				}
			}
		}

		if(isset($document['mensaje_dec']['mensaje']['empresasSolicitadasBajas']  )){
			if(!empty($document['mensaje_dec']['mensaje']['empresasSolicitadasBajas'])){
				$empresasSolicitadasBajas = $document['mensaje_dec']['mensaje']['empresasSolicitadasBajas'];
				if(is_array($empresasSolicitadasBajas)){
					if(count($empresasSolicitadasBajas)>0){
						foreach ($empresasSolicitadasBajas as $rutEmpresa) { 
							if(!$this->_usuarios->validaEmpresaExista($rutEmpresa)){
								$this->valid=false;
								$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresaSolicitadaNoExisteErr","empresasSolicitadasBajas",$rutEmpresa);						
							}
						}
					}
					else{
						$this->valid=false;
						$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresasSolicitadasErr","empresasSolicitadasBajas");
					}
				}
				else{
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresasSolicitadasErr","empresasSolicitadasBajas");
				}
			}
		}
	}


	// Fin Actualizacion Perfil

	// prueba borrar
	public function test($document){
		$nombrePerfil = $document['mensaje_dec']['mensaje']['codigoPerfil'];
    	$idPerfil = $this->_perfiles->traeIdPerfilPorNombre($nombrePerfil);
    	$arrRoles = $this->_perfiles->traeRolesPorIdPerfil($idPerfil);
		$this->salida['mensaje_dec']['perfil']  = $idPerfil;
		$this->salida['mensaje_dec']['rolesActuales']  = $arrRoles;	
		$this->salida['mensaje_dec']['roles Altas']  = $this->_roles->traeListaIdRolPorListaNombres($document['mensaje_dec']['mensaje']['rolesAltas']);
		$this->salida['mensaje_dec']['roles Bajas']  = $this->_roles->traeListaIdRolPorListaNombres($document['mensaje_dec']['mensaje']['rolesBajas']);

		if (isset($document['mensaje_dec']['mensaje']['rolesAltas'])){
    		$arrRoles = array_merge( $arrRoles , $this->_roles->traeListaIdRolPorListaNombres($document['mensaje_dec']['mensaje']['rolesAltas']));
    	}
    	$this->salida['mensaje_dec']['suma altas']  = $arrRoles;
    	if (isset($document['mensaje_dec']['mensaje']['rolesBajas'])){
    		$arrRoles = array_diff($arrRoles, $this->_roles->traeListaIdRolPorListaNombres($document['mensaje_dec']['mensaje']['rolesBajas']));
    	}	
    	$this->salida['mensaje_dec']['resta roles']  = $arrRoles;
    	$arrRoles = $this->func->remove_duplicates_array($arrRoles);
    	$this->salida['mensaje_dec']['elimina duplicados']  = $arrRoles;
    	
		$this->salida['mensaje_dec']['mensaje']  = $document['mensaje_dec']['mensaje'];
		return $this->salida;
	}
	// fin prueba borrar
}

?>