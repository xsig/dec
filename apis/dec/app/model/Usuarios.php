<?php 
namespace Dec\model;
use Dec\database\MongoDBConn as MongoDBConn;
use Dec\utils\Funciones as Funciones;
use Dec\error\MensajeError as MensajeError;
//require_once '/usr/share/php/Mail.php';

class Usuarios {
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
        $this->func = new Funciones();
        $this->Mensaje = new MensajeError();
    }
    
    public function validaConexion(){
        if(self::$ConnMDB)
            return true;
        else
            return false;
    }
    
    private function validaUsuario($document){
        $rut_usuario = $document['mensaje_dec']['header']['usuario'];
        if ($rut_usuario != "Anonimo"){
            if (!$this->func->valida_rut($rut_usuario)){
                $this->valid=false;
                $this->salida = $this->Mensaje->grabarMensaje( $this->salida, "Negocio", "usuarios","UsuarioRutInvalidoErr");
            }
            if (empty($rut_usuario)){
                $this->valid=false;
                $this->salida = $this->Mensaje->grabarMensaje( $this->salida, "Negocio" , "usuarios","UsuarioCampoRutVacioErr");
            }
            if (isset($document['mensaje_dec']['mensaje']['rut']) && 
                $document['mensaje_dec']['mensaje']['rut'] != "" && 
                $document['mensaje_dec']['mensaje']['rut'] != NULL){
                    $rut_consultar = $document['mensaje_dec']['mensaje']['rut'];
                    if ($document['mensaje_dec']['mensaje']['rut'] == $rut_usuario ){
                        // Traer Datos Usuario
                        $this->getUsuarioPorRut($rut_usuario);
                    }
                    else{
                        if ($this->ValidaPerfilDatosPersonales($rut_usuario, $rut_consultar)){
                            // Traer Datos Rut Consultado
                            $this->getUsuarioPorRut($document['mensaje_dec']['mensaje']['rut']);
                        }
                        else{
                            //Error Perfil erroneo
                            $this->valid=false;
                            $this->salida = $this->Mensaje->grabarMensaje( $this->salida,"Negocio", "usuarios","PerfilNoValidoErr");
                        }
                    }
            }
            else{
                // Traer Datos Usuario
                $this->getUsuarioPorRut($rut_usuario);
            }
        }
        else{
            $this->valid=false;
            $this->salida = $this->Mensaje->grabarMensaje( $this->salida, "Negocio", "usuarios","UsuarioAnonimoErr");
        }
    }
    
    public function validaPerfilDatosPersonales2($rut_usuario, $rut_consultar){
        $response= array();
        $_perfiles = new Perfiles();
        $_perfilesclientes = new PerfilesClientes();
        $_perfilamientos = new Perfilamientos();
        $arrPerfilesClientes = array();
        $idPerfilList = $_perfiles->traeListaAdministradorCliente();
        $arrClientes = $this->traeListaIdClientesSolicitadosPorRut($rut_consultar);
        $idUsuario = $this->traeIdUsuarioPorRut($rut_usuario);
        $response['arrClientes'] =$arrClientes;
        $response['idPerfilList'] =$idPerfilList;
        $response['IdUsuario'] =$idUsuario;
        $response['rut_usuario'] =$rut_usuario;
        $response['rut_consultar'] =$rut_consultar;
        foreach($arrClientes as $idCliente) {
            $idPerfilClienteList = $_perfilesclientes->traeListaIdPerfilesClientes($idPerfilList, $idCliente);
            $response['idPerfilClienteList'][] =$idPerfilClienteList;
            $response['idCliente'][] =$idCliente;
            if (count($idPerfilClienteList) > 0) {
                $response['idPerfilClienteList0'][] =$idPerfilClienteList;
                if ($_perfilamientos->validaPerfilamientoPerfilClienteIdUsuario($idPerfilClienteList, $idUsuario))
                    $response['OK'][] =$idPerfilClienteList;
            }
            else{
                $response['idPerfilClienteList1'][] =$idPerfilClienteList;
            }
        }
        return $response;
    }
    
    public function validaPerfilDatosPersonales($rut_usuario, $rut_consultar){
        $_perfiles = new Perfiles();
        $_perfilesclientes = new PerfilesClientes();
        $_perfilamientos = new Perfilamientos();
        $_clientes = new Clientes();
        $idPerfilClienteList = array();
        $idPerfilList = $_perfiles->traeListaAdministradorCliente();
        $arrClientes = $this->traeListaIdClientesSolicitadosPorRut($rut_consultar);
        $idUsuario = $this->traeIdUsuarioPorRut($rut_usuario);
        if( !(is_array($idPerfilList)) || count($idPerfilList)<=0){
            return false;
        }        
        foreach($arrClientes as $rutCliente) {
            $idCliente = $_clientes->traeIdClientePorRut($rutCliente);
            $idPerfilClienteList = $_perfilesclientes->traeListaIdPerfilesClientes($idPerfilList, $idCliente);
            if (count($idPerfilClienteList) > 0) {
                if ($_perfilamientos->validaPerfilamientoPerfilClienteIdUsuario($idPerfilClienteList, $idUsuario))
                    return true;
            }
        }
        return false;
    }
    
    public function validaPerfilTipoDocumentos($rut_usuario, $rut_consultar){
        $_perfiles = new Perfiles();
        $_perfilesclientes = new PerfilesClientes();
        $_perfilamientos = new Perfilamientos();
        $_clientes = new Clientes();
        $idPerfilClienteList = array();
        $idPerfilList = $_perfiles->traeListaAdministradorCliente();
        $arrClientes = $this->traeListaIdClientesSolicitadosPorRut($rut_consultar);
        $idUsuario = $this->traeIdUsuarioPorRut($rut_usuario);
        foreach($arrClientes as $rutCliente) {
            $idCliente = $_clientes->traeIdClientePorRut($rutCliente);
            $idPerfilClienteList = $_perfilesclientes->traeListaIdPerfilesClientes($idPerfilList, $idCliente);
            if (count($idPerfilClienteList) > 0) {
                if ($_perfilamientos->validaPerfilamientoPerfilClienteIdUsuario($idPerfilClienteList, $idUsuario))
                    return true;
            }
        }
        return false;
    }
    
    public function validaRutExiste($rut){
        $busqueda = array('rut' => $rut );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        foreach($cursor as $item ){
            return true;
        }
        return false;
    }
    
    public function buscaUsuarioFiltros($busquedaUsuario){
        $usuarioIds = array();
        $cursor = self::$ConnMDB->busca("usuarios", $busquedaUsuario);
        //if($cursor->count()>0){
            foreach($cursor as $item){
                $usuarioIds[] = $item->_id;
            }   
        //}
        return $usuarioIds;
    }

    public function validaCorreoExisteAct($correoElectronico, $rut){
        $BusqRut = array('$ne' => $rut);
        $busqueda = array(
            'rut' => $BusqRut ,
            'correoElectronico' => $correoElectronico 
            );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        foreach($cursor as $item ){
            return true;
        }
        return false;
    }

    public function validaCorreoExiste($correoElectronico){
        $busqueda = array('correoElectronico' => $correoElectronico );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        foreach($cursor as $item ){
            return true;
        }
        return false;
    }

    public function validaEmpresaExista($rutEmpresa){
        $busqueda = array('datosDemograficos.Rut' => $rutEmpresa );
        $cursor = self::$ConnMDB->busca("clientes", $busqueda);
        foreach($cursor as $item ){
            return true;
        }
        return false;
    }
    
    public function ingresaUsuario($document){
        $arrEmpresasAutorizadas = array();
        $perfiles_globales = array();
        array_push($perfiles_globales,array("id"=>1,"nombrePerfil"=>"INGRESO WEB"));
        $doc_usuario = array(
            "estado" => "ACTIVO",
            "rut" =>strtoupper($document['mensaje_dec']['mensaje']['rut']) , 
            "password" => password_hash($document['mensaje_dec']['mensaje']['password'], PASSWORD_DEFAULT),
            "nombre" => strtoupper($document['mensaje_dec']['mensaje']['Nombre']),
            "segundoNombre" => strtoupper($document['mensaje_dec']['mensaje']['segundoNombre']),
            "apellidoPaterno" => strtoupper($document['mensaje_dec']['mensaje']['apellidoPaterno']),
            "apellidoMaterno" => strtoupper($document['mensaje_dec']['mensaje']['apellidoMaterno']),
            "correoElectronico" => $document['mensaje_dec']['mensaje']['correoElectronico'],
            "genero" => strtoupper($document['mensaje_dec']['mensaje']['genero']),
            "foto" => (isset($document['mensaje_dec']['mensaje']['foto']) == false )? " ":$document['mensaje_dec']['mensaje']['foto'],
            "empresasAutorizadas" => array(),
            "perfiles_globales" => $perfiles_globales
        );

        if (isset($document['mensaje_dec']['mensaje']['empresasSolicitadas'])){
            if (is_array($document['mensaje_dec']['mensaje']['empresasSolicitadas']) ){
                if (count($document['mensaje_dec']['mensaje']['empresasSolicitadas']) > 0){
                   $doc_usuario["conTramite"] = "SI"; 
                   $doc_usuario["empresasSolicitadas"] = $document['mensaje_dec']['mensaje']['empresasSolicitadas']; 
                }
                else{
                    $doc_usuario["conTramite"] = "NO"; 
                    $doc_usuario["empresasSolicitadas"] = array();                    
                }
            }
            else{
                $doc_usuario["conTramite"] = "NO"; 
                $doc_usuario["empresasSolicitadas"] = array();     
            }
        }
        else{
            $doc_usuario["conTramite"] = "NO"; 
            $doc_usuario["empresasSolicitadas"] = array();     
        }

        $idUsuario =  self::$ConnMDB->ingresa("usuarios",$doc_usuario,"usuario_id");

        return $idUsuario;
    }
    public function actualizaUsuario($document){
        $busqueda = array('rut' => strtoupper($document['mensaje_dec']['mensaje']['rut']) );
        $doc_actualiza = array();

        if(isset($document['mensaje_dec']['mensaje']['password'])){
            $doc_actualiza['password'] = password_hash($document['mensaje_dec']['mensaje']['password'], PASSWORD_DEFAULT);
        }
        if(isset($document['mensaje_dec']['mensaje']['Nombre'])){
            $doc_actualiza['nombre'] = strtoupper($document['mensaje_dec']['mensaje']['Nombre']);
        }
        if(isset($document['mensaje_dec']['mensaje']['segundoNombre'])){
            $doc_actualiza['segundoNombre'] = strtoupper($document['mensaje_dec']['mensaje']['segundoNombre']);
        }
        if(isset($document['mensaje_dec']['mensaje']['apellidoPaterno'])){
            $doc_actualiza['apellidoPaterno'] = strtoupper($document['mensaje_dec']['mensaje']['apellidoPaterno']);
        }
        if(isset($document['mensaje_dec']['mensaje']['apellidoMaterno'])){
            $doc_actualiza['apellidoMaterno'] = strtoupper($document['mensaje_dec']['mensaje']['apellidoMaterno']);
        }
        if(isset($document['mensaje_dec']['mensaje']['correoElectronico'])){
            $doc_actualiza['correoElectronico'] = $document['mensaje_dec']['mensaje']['correoElectronico'];
        }
        if(isset($document['mensaje_dec']['mensaje']['genero'])){
            $doc_actualiza['genero'] = strtoupper($document['mensaje_dec']['mensaje']['genero']);
        }
        if(isset($document['mensaje_dec']['mensaje']['foto'])){
            $doc_actualiza['foto'] = $document['mensaje_dec']['mensaje']['foto'];
        }
        if(isset($document['mensaje_dec']['mensaje']['empresasSolicitadas'])){
            if (is_array($document['mensaje_dec']['mensaje']['empresasSolicitadas'])){
                if (count($document['mensaje_dec']['mensaje']['empresasSolicitadas'])>0)
                {
                    $doc_actualiza['conTramite'] = "SI" ; 
                    foreach ( $document['mensaje_dec']['mensaje']['empresasSolicitadas'] as $value)
                    {
                        $doc_actualiza['empresasSolicitadas'][] = strtoupper($value) ;
                    }
                }
                else
                {
                    $doc_actualiza["conTramite"]="NO";
                    $doc_actualiza["empresasSolicitadas"]=array();
                }
            }
        }
        $id = self::$ConnMDB->actualiza("usuarios", $busqueda, $doc_actualiza);
        return $id;
    }
    public function enviaCorreo($document){
        $nombre = $document['mensaje_dec']['mensaje']['Nombre'];
        $correoElectronico = $document['mensaje_dec']['mensaje']['correoElectronico'];
        $asunto = "Solicitud acceso a DEC";
        $mensaje_correo = 'Verificar la direcci&oacute;n de correo electr&oacute;nico que se ha '
        . 'a&ntilde;adido<br><br>Hola, ' . $nombre . ':<br>'
        . 'Se ha realizado una solicitud para a&ntilde;adir ' . $correoElectronico . ' a tu cuenta '
        . 'de DEC.<br><br>'
        . '&iquest;Te sorprende haber recibido este correo electr&oacute;nico?<br>'
        . 'Ignora este correo electr&oacute;nico, Puede que alguien haya escrito mal su '
        . 'direcci&oacute;n de correo electr&oacute;nico y que haya a&ntilde;adido accidentalmente '
        . 'la tuya. En este caso, tu direcci&oacute;n de correo electr&oacute;nico no se '
        . 'a&ntilde;adir&aacute; a la otra cuenta.<br>'
        . 'Verificar la direcci&oacute;n de correo electr&oacute;nico que se ha a&ntilde;adido<br>'
        . 'Esta direcci&oacute;n de correo electr&oacute;nico no admite respuestas. <br>Para '
        . 'obtener m&aacute;s informaci&oacute;n, visita el DEC - UI Bienvenida a DEC Centro de '
        . 'ayuda de Cuentas de DEC.';
        if ($this->func->sendEmail($asunto,$correoElectronico,$nombre, $mensaje_correo )){
            return true;
        }
        return false;           
    }
    
    public function validaUsuarioPassword($rut , $password){
        $busqueda = array('rut' => $rut );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        foreach ($cursor as $result) { 
            $usuario = $result;
        }
        $hash_password = $usuario->password;
        if (password_verify($password, $hash_password)) {
            return true;
        } 
        return false;
    }
    
    public function validaUsuarioHuella($rut , $huella){
        $busqueda = array('rut' => $rut );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        foreach ($cursor as $result) { 
            $usuario = $result;
        }
        $hash_password = $usuario->password;
        if (password_verify($password, $hash_password)) {
            return true;
        } 
        return false;
    }
    
    public function traeEstadoUsuario($rut){
        $estado = "";
        $busqueda = array('rut' => $rut );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        foreach ($cursor as $result) { 
            $usuario = $result;
        }
        if (isset($usuario->estado)){
            $estado = $usuario->estado;
        }
        return $estado;
    }
    
     public function getUsuarioPorRutDatos($rut){
        $_clientes = new Clientes();
        $_usuario = array();
        $_usus = array();
        $busqueda = array('rut' => $rut );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        //if($cursor->count()>=0){
            foreach($cursor as $item){
                $_usus['rut'] = $item->rut;
                $_usus['password'] = $item->password;
                $_usus['nombre'] = $item->nombre;
                $_usus['segundoNombre'] = $item->segundoNombre;
                $_usus['apellidoPaterno'] = $item->apellidoPaterno;
                $_usus['apellidoMaterno'] = $item->apellidoMaterno;
                $_usus['correoElectronico'] = $item->correoElectronico;
                $_usus['genero'] = $item->genero;
                $_usus['estado'] = $item->estado;
                $_usus['conTramite'] = $item->conTramite;
                $_usus['empresasSolicitadas'] = array();
                if (isset($item->empresasSolicitadas) && $item->empresasSolicitadas != null){
                    $_usus['empresasSolicitadas'] = $_clientes->traeNombresDeEmpresasPorListaDeRuts($item->empresasSolicitadas);
                }
                $_usus['empresasAsignadas'] = ($item->empresasAutorizadas == null) ? array() : $_clientes->traeNombresDeEmpresasPorListaDeEmpresas($item->empresasAutorizadas);
                $_usuario[] = $_usus;
            }
        //}
        return $_usuario;
    }
    public function getEmpresasUsuarioPorRutDatos($rut){
        $_usuario = array();
        $_usus = array();
        $empr = array();
        $_clientes = new Clientes();
        $busqueda = array('rut' => $rut );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        //if($cursor->count()>=0){
            foreach($cursor as $item){
                if (isset($item->empresasAutorizadas)){
                    if (is_array($item->empresasAutorizadas)){
                        foreach ($item->empresasAutorizadas as $key => $value) {
                            $empr['empresa'] = $value;
                            $empr['perfiles'] = $_clientes->traeNombrePerfilesPorRutEmpresa($value);
                        }
                    }
                }
                $_usus['empresasAsignadas'] = $empr;

                $_usuario[] = $_usus;
            }
        //}
        return $_usuario;
    }


    public function getUsuarioPorRut($rut){
        $_usuario = array();
        $busqueda = array('rut' => $rut );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        foreach($cursor as $usuario){
            //$_usuario = $usuario;
            $_usuario["_id"] = $usuario->_id;
            $_usuario["estado"] = $usuario->estado;
            $_usuario["conTramite"] = $usuario->conTramite;
            $_usuario["rut"] = $usuario->rut;
            $_usuario["password"] = $usuario->password;
            $_usuario["nombre"] = $usuario->nombre;
            $_usuario["segundoNombre"] = $usuario->segundoNombre;
            $_usuario["apellidoPaterno"] = $usuario->apellidoPaterno;
            $_usuario["apellidoMaterno"] = $usuario->apellidoMaterno;
            $_usuario["correoElectronico"] = $usuario->correoElectronico;
            $_usuario["genero"] = $usuario->genero;
            $_usuario["foto"] = $usuario->foto;
            $_usuario["empresasAutorizadas"] = ($usuario->empresasAutorizadas == null) ? array() :$usuario->empresasAutorizadas ;
            $_usuario["empresasSolicitadas"] = ($usuario->empresasSolicitadas == null) ? array() :$usuario->empresasSolicitadas;

        }
        return $_usuario;
    }
    
    public function traeIdUsuarioPorRut($rut){
        $_usuario = 0;
        $busqueda = array('rut' => $rut );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        foreach($cursor as $users){
            $_usuario = $users->_id;
        }
        return $_usuario;
    }

    public function traeUsuarioPorRut($rut){
        $_usuario = 0;
        $busqueda = array('rut' => $rut );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        foreach($cursor as $users){
            $_usuario = $users;
        }
        return $_usuario;
    }

    public function traeUsuarioPorId($id){
        $_usuario = 0;
        $busqueda = array('_id' => $id );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        foreach($cursor as $users){
            $_usuario = $users;
        }
        return $_usuario;
    }

    private function validaPerfiles($document){
        $rut_solicitante = $document['mensaje_dec']['header']['usuario'];
        $rut_consultar = $document['mensaje_dec']['mensaje']['rut'];
        if(isset($rut_solicitante) && $rut_solicitante!="" ){
            if (isset($rut_consultar) && $rut_consultar!="" ){
                if ($rut_solicitante == $rut_consultar){
                    $busqueda = array('rut' => $rut_consultar );
                    $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
                    if(self::$ConnMDB->count("usuarios", $busqueda) <= 0){
                        $this->valid=false;
                        $this->salida = $this->Mensaje->grabarMensaje( $this->salida,"Negocio", "usuarios","UsuarioNoExisteErr");
                    }
                    else{
                        foreach($cursor as $usuario){
                            $this->salida['mensaje_dec']['mensaje'][] = $usuario;
                        }
                    }               
                }
                else{
                    // Buscar Perfil de Administrador Empresa
                }
            }
        }    
    }
    
    private function formatoRut($rut){
        return strtoupper(str_replace(".","",$rut));
    }
    
//  validarRut(Si Cliente pidió acceso restringido ==> Qué el usuario exista en   lista de usuarios Permitidos)


    public function traeListaIdClientesSolicitadosPorId($idUsuario){
        $_clientes = array();
        $busqueda = array("_id" => $idUsuario );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        //if($cursor->count()>0){
            foreach($cursor as $item ){
                    $_clientes = $item->empresasSolicitadas;
            }
        //}
        return $_clientes;
    }
    
    public function traeListaIdClientesSolicitadosPorRut($rutUsuario){
        $_clientes = array();
        $busqueda = array("rut" => $rutUsuario );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        //if($cursor->count()>0){
            foreach($cursor as $item ){
                    $_clientes = $item->empresasSolicitadas;
            }
        //}
        return $_clientes;
    }
    
    public function traeListaIdClientesAutorizadasPorRut($rutUsuario){
        $_clientes = array();
        $_ClientesModel = new Clientes();
        $busqueda = array("rut" => $rutUsuario );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        //if($cursor->count()>0){
            foreach($cursor as $item ){
                $_clientes = $_ClientesModel->traeIdClientePorListaRut($item->empresasAutorizadas);
            }
        //}
        return $_clientes;
    }
    
    public function traeListaRutClientesAutorizadasPorRut($rutUsuario){
        $_clientes = array();
        $_ClientesModel = new Clientes();
        $busqueda = array("rut" => $rutUsuario );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        foreach($cursor as $item ){
                $_clientes = $item->empresasAutorizadas;
        }
        return $_clientes;
    }
    
    public function validaUsuarioExiste($rut){
        $busqueda = array('rut' => $rut );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        foreach($cursor as $item ){
            return true;
        }
        return false;
    }

    public function getEmpresasAsignadasPerfiles($rutUsuario){
        $empresas = array();
        $usuarios = array();
        $resultado = array();
        $result = array();
        $busqueda = array('rut' => $rutUsuario );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        //if($cursor->count()>0){
            foreach($cursor as $item ){
                if (isset($item->empresasAutorizadas)){
                    foreach ($item->empresasAutorizadas as $empresa) {
                       $resultado['empresa'] = $empresa;
                       $perfiles=array();
                       if(isset($item->perfiles))
                       {
                           foreach ($item->perfiles as $perfil)
                           {
                                if($perfil->empresa==$empresa)
                                    array_push($perfiles,$perfil);
                           }
                       }
                       $resultado['perfiles'] = $perfiles;
                       $result[] = $resultado;
                    }
                }
            }
        //}
        return $result;
    }

    public function traeUsuariosPorListaIdBusqueda($listaIdsUsuarios){
        $_clientes = new Clientes();
        $usuarios = array();
        $usus = array();
        if (count($listaIdsUsuarios)>0){
            $BusqlistaId = array('$in' => $listaIdsUsuarios);
            $busqueda = array('_id' => $BusqlistaId );
            $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
            //if($cursor->count()>0){
                foreach($cursor as $item ){
                    unset($item->foto);
                    $usus['rut'] = $item->rut;
                    $usus['nombre'] = $item->nombre;
                    $usus['segundoNombre'] = $item->segundoNombre;
                    $usus['apellidoPaterno'] = $item->apellidoPaterno;
                    $usus['apellidoMaterno'] = $item->apellidoMaterno;
                    $usus['correoElectronico'] = $item->correoElectronico;
                    $usus['genero'] = $item->genero;
                    $usus['estado'] = $item->estado;
                    $usus['conTramite'] = $item->conTramite;
                    $empSol = array();
                    if (isset($item->empresasSolicitadas)){
                        if ($item->empresasSolicitadas != null) {
                            $empSol = $item->empresasSolicitadas;
                        }
                    }
                    $usus['empresasSolicitadas'] = ($item->empresasSolicitadas ==null) ? array() : $item->empresasSolicitadas;
                    $usus['empresasAsignadas'] = ($item->empresasAutorizadas ==null )? array() : $item->empresasAutorizadas;
                    $usuarios[] = $usus;
                }
            //}
        }
        return $usuarios;
    }

    public function traeUsuarioNombrePorListaRuts($listaRutsUsuarios){
        $tmpUsuarios = array();
        $arrUsuarios = array();
        if (count($listaRutsUsuarios)>0){
            $BusqlistaId = array('$in' => $listaRutsUsuarios);
            $busqueda = array('rut' => $BusqlistaId );
            $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
            foreach($cursor as $item ){
                $tmpUsuarios['rut'] = $item->rut;
                $tmpUsuarios['nombre'] = $item->nombre . " " . $item->apellidoPaterno . " " . $item->apellidoMaterno ;
                $arrUsuarios[] = $tmpUsuarios;
            }
        }
        return $arrUsuarios;
            
    }

    public function traeUsuariosPorListaId($listaIdsUsuarios){
        $usuarios = array();
        if (count($listaIdsUsuarios)>0){
            $BusqlistaId = array('$in' => $listaIdsUsuarios);
            $busqueda = array('_id' => $BusqlistaId );
            $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
            //if($cursor->count()>0){
                foreach($cursor as $item ){
                    unset($item->foto);
                    $usuarios[] = $item;
                }
            //}
        }
        return $usuarios;
            
    }


    public function ActualizaClaveTemporal($rut,$claveTemporal){
        $busqueda = array('rut' => $rut );

        $_id =  $this->CambiaPassword($rut,$claveTemporal['claveTemporal']['password'], false);
        unset($claveTemporal['password']);
        $_id =  self::$ConnMDB->actualiza("usuarios",$busqueda,$claveTemporal);
        return $_id;
    }

    public function CambiaPassword($rut, $password, $temporal){
        $busqueda = array('rut' => $rut );
        if ($temporal){
            $claveActualizar = array(
                "claveTemporal" => array(),
                "password" =>  password_hash($password, PASSWORD_DEFAULT)
            );             
        }
        else{
            $claveActualizar = array(
                "password" =>  password_hash($password, PASSWORD_DEFAULT)
            );  
        }
        $_id =  self::$ConnMDB->actualiza("usuarios",$busqueda, $claveActualizar);
        return $_id;
    }

    public function enviaCorreoClaveTemporal($rut, $password){
        try{
            $usuarioConsultado =  $this->getUsuarioPorRut($rut);
            $nombre = $usuarioConsultado['nombre'];
            $segundoNombre = "";
            if (isset($usuarioConsultado['segundoNombre']) && $usuarioConsultado['segundoNombre'] != ""){
                $segundoNombre = $usuarioConsultado['segundoNombre'];
            }
            $apellidoPaterno = $usuarioConsultado['apellidoPaterno'];
            $apellidoMaterno = $usuarioConsultado['apellidoMaterno'];
            $correoElectronico = $usuarioConsultado['correoElectronico'];
            $nombreCompleto = rawurlencode($nombre . ' ' .  $segundoNombre . ' ' . $apellidoPaterno . ' ' . $apellidoMaterno);
            $asunto = 'Recuperacion de Clave de acceso a DEC';
            $mensaje_correo = 'Hola ' . $nombre . ':<br><br>'
            . 'Has solicitado cambiar tu clave de acceso a DEC Ratifica <br><br>'
            . 'Tu clave provisoria es: ' . $password . '<br><br>'
            . 'Si quieres reemplazar tu clave provisoria por una escogida por ti, ingresa a '
            . 'configuracion de usuario. <br><br>'
            . 'Si tienes cualquier duda o consulta, comun&iacute;cate con nuestro Servicio al Cliente '
            . 'al 26720628 o escr&iacute;benos a contacto-dec@ratifica.cl <br><br>'
            . 'Se despide cordialmente,'
            . 'Equipo DEC Ratifica';
            
            if ($this->func->sendEmail($asunto,$correoElectronico,$nombre, $mensaje_correo )){
                return true;
            }
            return false;   
        }
        catch(Exception $Ex){
            //$jsonClientes = json_decode($respClientes);
            return false;
        }
    }

    public function asociarPerfiles($rutUsuario,$empresa,$perfiles)
    {
        $this->agregaPerfiles($rutUsuario,$empresa,$perfiles);
    }

    public function autorizaEmpresa($rutUsuario,$empresa)
    {
        if (!$this->empresaAsociadaExiste($rutUsuario,$empresa)){
            $this->agregaEmpresaAutorizada($rutUsuario,$empresa);
        }
    }

    public function desautorizaEmpresa($rutUsuario,$empresa)
    {
        if ($this->empresaAsociadaExisteDES($rutUsuario,$empresa))
        {
            $this->eliminaEmpresaAutorizada($rutUsuario,$empresa);
        }
    }

    public function agregaPerfiles($rutUsuario,$rutEmpresa,$perfiles)
    {
        $id = 0;
        $_perfiles = new Perfiles();
        $usuario = $this->traeUsuarioPorRut($rutUsuario);
        $valido=true;
        foreach ($perfiles as $perf)
        {
            $idPerfil = $_perfiles->traeIdPerfilesPorNombre($perf["nombrePerfil"]);
            if(count($idPerfil)==0)
            {
                $valido=false;
                break;
            }
        }
        if(!$valido)
            return;
        $busqueda = array('rut' => $rutUsuario);
        foreach($usuario->empresasAutorizadas as $empresa)
        {
            if($empresa->rut==$rutEmpresa)
            {
                $empresa->perfiles=$perfiles;
                break;                
            }
        }
        self::$ConnMDB->actualiza("usuarios", $busqueda, $usuario);
    }

    public function verificaAutorizacionFirma($rutUsuario,$rutEmpresa,$perfil)
    {
        $perfiles=$this->perfilesFirmaUsuario($rutUsuario,$rutEmpresa);
        if(in_array($perfil,$perfiles))
            return true;
        else
            return false;
    }

    public function verificaAutorizacion($rutUsuario,$rutEmpresa,$rol)
    {
        $roles = $this->rolesUsuario($rutUsuario,$rutEmpresa);
        if(in_array($rol,$roles))
            return true;
        else
            return false;
    }

    public function perfilesFirmaUsuario($rutUsuario,$rutEmpresa)
    {
        $id = 0;
        $_perfiles = new Perfiles();
        $usuario = $this->traeUsuarioPorRut($rutUsuario);
        $perfilesFirmanteUsuario=array();
        foreach ($usuario->empresasAutorizadas as $empresa)
        {
            if($empresa->rut==$rutEmpresa)
            {
                foreach($empresa->perfiles as $perfil)
                {
                    $roles = $_perfiles->traeRolesPorIdPerfil($perfil->id);
                    if(in_array("FIRMANTE",$roles))
                        array_push($perfilesFirmanteUsuario,$perfil->nombrePerfil);
                }
            }
        }
        return $perfilesFirmanteUsuario;
    }

    public function rolesUsuario($rutUsuario,$rutEmpresa)
    {
        $id = 0;
        $_perfiles = new Perfiles();
        $usuario = $this->traeUsuarioPorRut($rutUsuario);
        $rolesUsuario=array();
        if(isset($usuario->perfiles_globales))
        {
            foreach ($usuario->perfiles_globales as $perfil)
            {
                $roles = $_perfiles->traeRolesPorIdPerfil($perfil->id);
                foreach($roles as $rol)
                {
                    if(!in_array($rol,$rolesUsuario))
                        array_push($rolesUsuario,$rol);
                }
            }
        }
        foreach ($usuario->empresasAutorizadas as $empresa)
        {
            if($empresa->rut==$rutEmpresa)
            {
                foreach($empresa->perfiles as $perfil)
                {
                    $roles = $_perfiles->traeRolesPorIdPerfil($perfil->id);
                    foreach($roles as $rol)
                    {
                        if(!in_array($rol,$rolesUsuario))
                            array_push($rolesUsuario,$rol);
                    }
                }
            }
        }
        return $rolesUsuario;
    }

    public function agregaEmpresaAutorizada($rutUsuario,$empresa){
        $clientes = array();
        $Solicitados = array();
        $busqueda = array("rut" => $rutUsuario );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        foreach($cursor as $item ){
            $clientes = $item->empresasAutorizadas;
            $Solicitados = $item->empresasSolicitadas;
        }
        $existe=false;
        foreach($clientes as $cliente)
        {
            if($cliente->rut==$empresa)
            {
                $existe=true;
                break;
            }
        }
        if(!$existe)
            $clientes[] = array("rut"=>$empresa,"perfiles" => array());
        $Solicitados = $this->func->array_delete($empresa,$Solicitados);
        if ($Solicitados == null ){ $Solicitados = array(); }
        $datosAct = array( 
            "empresasAutorizadas" => $clientes ,
            "empresasSolicitadas" => $Solicitados
            );
        $cursor = self::$ConnMDB->actualiza("usuarios", $busqueda, $datosAct);
    }

    public function eliminaEmpresaAutorizada($rutUsuario,$empresa){
        $clientes = array();
        $Solicitados = array();
        $busqueda = array("rut" => $rutUsuario );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        foreach($cursor as $item )
        {
            $autorizadas = $item->empresasAutorizadas;
            $solicitadas = $item->empresasSolicitadas;
        }
        for($i=0;$i<count($solicitadas);$i++)
        {
            if($solicitadas[$i]==$empresa)
            {
                array_splice($solicitadas,$i,1);
                break;
            }
        }
        for($i=0;$i<count($autorizadas);$i++)
        {
            if($autorizadas[$i]->rut==$empresa)
            {
                array_splice($autorizadas,$i,1);
                break;
            }
        }
        if ($solicitadas == null )
            $solicitadas=array();
        if($autorizadas==null)
            $autorizadas=array();
        $datosAct = array( 
            "empresasAutorizadas" => $autorizadas ,
            "empresasSolicitadas" => $solicitadas
            );
        $cursor = self::$ConnMDB->actualiza("usuarios", $busqueda, $datosAct);
    }

    public function empresaAsociadaExisteDES($rutUsuario,$empresa)
    {
        $_clientes = array();
        $resultado = false;
        $busqueda = array("rut" => $rutUsuario );
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        foreach($cursor as $item )
        {
            foreach ($item->empresasSolicitadas as $empsolic)
            {
                if($empsolic == $empresa)
                {
                    $resultado = true;
                }
            }
            foreach ($item->empresasAutorizadas as $empaut)
            {
                if($empaut->rut == $empresa)
                {
                    $resultado = true;
                }
            }
        }
        return $resultado;
    }

    public function empresaAsociadaExiste($rutUsuario,$empresa)
    {
        $_clientes = array();
        $busqueda = array("rut" => $rutUsuario );
        $listaEmpresas = array();
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        foreach($cursor as $item ){
            $listaEmpresas = $item->empresasAutorizadas;
        }            
        foreach ($listaEmpresas as $value) {
            if($value == $empresa){
                return true;
            }
        }
        return false;
    }

    public function validaEmpresaPermisosTipoDocumentos($rutUsuario, $rutEmpresa){
        if($this->empresaAsociadaExiste($rutUsuario,$rutEmpresa)){
            return true;
        }
        return false;
    }

    public function EliminaConTramite($rutUsuario){
        $busqueda = array("rut" => $rutUsuario);
        if ($this->ExistenEmpresasSolicitadas($rutUsuario)){
            $datosAct = array("conTramite" => "SI" );
        }
        else{
            $datosAct = array("conTramite" => "NO" );          
        }
        $cursor = self::$ConnMDB->actualiza("usuarios", $busqueda, $datosAct);

    }

    public function ExistenEmpresasSolicitadas($rutUsuario)
    {
        $Solicitados = array();
        $busqueda = array("rut" => $rutUsuario);
        $cursor = self::$ConnMDB->busca("usuarios", $busqueda);
        foreach($cursor as $item ){
            $Solicitados = $item->empresasSolicitadas;
        }
        if (count($Solicitados)>0){
            return true;
        }
        return false;
    }
}