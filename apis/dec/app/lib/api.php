<?php
namespace Dec\lib;
use Dec\controller as Controller;
use Dec\model as Model;
use Dec\database as Database; 
use Dec\model\Logging as Logging;

abstract class Api
{
    /**
     * Property: method
     * The HTTP method this request was made in, either GET, POST, PUT or DELETE
     */
    protected $method = '';
    /**
     * Property: endpoint
     * The Model requested in the URI. eg: /files
     */
    protected $endpoint = '';
    /**
     * Property: verb
     * An optional additional descriptor about the endpoint, used for things that can
     * not be handled by the basic methods. eg: /files/process
     */
    protected $verb = '';
    /**
     * Property: args
     * Any additional URI components after the endpoint and verb have been removed, in our
     * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
     * or /<endpoint>/<arg0>
     */
    protected $args = Array();
    /**
     * Property: file
     * Stores the input of the PUT request
     */
     protected $file = Null;

	/**
     * Property: document
     * Array with input json document
     * Stores the input of the POST request
     */
	 protected $document = Array();
	 
	 protected $output= null;
    /**
     * Constructor: __construct
     * Allow for CORS, assemble and pre-process the data
     */	 
    public function __construct($request) {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");

        $this->args = explode('/', rtrim($request, '/'));
        $this->endpoint = array_shift($this->args);
        if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
            $this->verb = strtolower(array_shift($this->args));
        }
		
		$logging = new Logging();
		$docEntrada = array();
		$docEntrada['endpoint'] = $request;
		$docEntrada['message'] = json_decode(file_get_contents('php://input'), true);
        $logging->guardaDocumentoEntrada($docEntrada);


        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }

        switch($this->method) {
        case 'DELETE':
        case 'POST':
            $this->request = $this->_cleanInputs($_POST);
			$this->document= json_decode(file_get_contents('php://input'), true);
            break;
        case 'GET':
            $this->request = $this->_cleanInputs($_GET);
            break;
        case 'PUT':
            $this->request = $this->_cleanInputs($_GET);
            $this->file = file_get_contents("php://input");
            break;
        default:
            $this->_response('Invalid Method', 405);
            break;
        }
    }
	
	public function processAPI() {
        if (method_exists($this, $this->endpoint)) {
            return $this->_response($this->{$this->endpoint}($this->args));
        }
        return $this->_response("No Endpoint: $this->endpoint", 404);
    }

    private function _response($data, $status = 200) {
        header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));

    	$logging = new Logging();
	    $docSalida = array();
	    $docSalida['message'] = $data;
        $logging->guardaDocumentoEntrada($docSalida);

        return json_encode($data);
    }

    private function _cleanInputs($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }

    private function _requestStatus($code) {
        $status = array(  
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported');
        return ($status[$code])?$status[$code]:$status[500]; 
    }
}


class DecApi extends Api
{
    protected $User;
	protected $output;
	
	protected $_clientes;
	protected $_usuarios;
	protected $_perfiles;
	protected $_tipodocumentos;
	protected $_roles;


    public function __construct($request, $origin){
        parent::__construct($request);
/*
        Abstracted out for example
        $APIKey = new Models\APIKey();
        $User = new Models\User();

       if (!array_key_exists('apiKey', $this->request)) {
           throw new Exception('No API Key provided');
       } else if (!$APIKey->verifyKey($this->request['apiKey'], $origin)) {
           throw new Exception('Invalid API Key');
       } else if (array_key_exists('token', $this->request) && !$User->get('token', $this->request['token'])){
           throw new Exception('Invalid User Token');
       }

       $this->User = $User;
*/
	    $this->User = "";
	    $this->_test = new Controller\TestController();
		$this->_clientes = new Controller\ClientesController();
		$this->_usuarios = new Controller\UsuariosController();
		$this->_perfiles = new Controller\PerfilesController();
		$this->_tipodocumentos = new Controller\TipoDocumentosController();
		$this->_subtipodocumentos = new Controller\SubTipoDocumentosController();
		$this->_operadocumentos = new Controller\OperaDocumentosController();
		$this->_firmantes = new Controller\FirmantesController();
		$this->_roles = new Controller\RolesController();
    }   

    /** 
     * Example of an Endpoint
     */
    protected function example(){
		$_output =array();
		$_output['code']="OK";
		$_output['status']="Respuesta OK";
        if ($this->method == 'GET') {
            //return "Your name is " . $this->User->name;
        	$bdprueba = new Database\MongoDBConn();

			$_output['prueb2']=$bdprueba->getNextSequence("tipoDocumento_id");

        }elseif($this->method == 'PUT') {
			$arr_data = json_decode($this->file);
			$_output['nombre'] = $arr_data->nombre;
		}
		elseif($this->method == 'POST') {
			$_output['nombre'] =$arr_data->nombre;
		}
		else {
            $_output['code']="Error";
			$_output['status']="Only accepts GET or PUT requests";
        }
		return $_output;
    } 
	
	protected function clientesRespaldo(){
		$m = new MongoClient("mongodb://localhost:27017");	
		$db = $m->decdb;	
		$collection = $db->clientes;
		switch($this->method) {
			case 'DELETE':
				$output = array(
					'estado' => "Error",
					'descripcionEstado' => "No se pudo eliminar el cliente",
					'CLIENTE' => array()
				);				

				try{
					$criterio = array(
					  'cliente_id' => $document['mensaje']['cliente_id']
					);

					$collection->remove($criterio,array( 'safe' => true));
					$output = array(
						'estado' => "OK" ,
						'descripcionEstado' => "Eliminacion exitosa",
						'CLIENTE' => $document['mensaje']
					);
				}
				catch(MongoCursorException $e) {
					
				}
				break;
			case 'POST':
				$document= json_decode(file_get_contents('php://input'), true);
				$output = array(
					'estado' => "Error",
					'descripcionEstado' => "No se pudo crear el cliente"
				);				

				try{
					$idcliente = getNextSeq("cliente_id");
					if ($idcliente<0){
						$m->close();
						$output['descripcionEstado']="Error Aplicativo, No se pudo crear Id cliente";		
						$this->response($this->json($output), 200); 					
					}
					$doc_cliente = array(
						"cliente_id" => $idcliente,
						"estado" => "creado",
						"datosDemograficos"	 => $document['mensaje']['datosDemograficos'],
						"datosContractuales" => $document['mensaje']['datosContractuales']
					);
					$results = $collection->insert($doc_cliente);
					$output = array(
						'estado' => "OK",
						'descripcionEstado' => "Creación exitosa",
						'CLIENTE' => $document['mensaje']
					);
				} catch(MongoCursorException $e) {
					
				}
				break;
			case 'GET':
				$id=$this->args[0]+0;
				if ($id) {
					$cursor = $collection->find(array('cliente_id' => $id ));
				} else {
					$cursor = $collection->find();
				}
				$output = array(
					'estado' => "OK",
					'descripcionEstado' => "Lista Exitosa",
					'CLIENTES' => array()
				);	
				foreach ($cursor as $result) { 
					$output['CLIENTES'][] = $result;
				}
				break;
			case 'PUT':
				$output = array(
					'estado' => "Error",
					'descripcionEstado' => "No se pudo modificar el cliente",
					'CLIENTE' => array()
				);				

				try{
					$criterio = array(
					  'cliente_id' => $document['mensaje']['cliente_id']
					);
					//unset($doc_cliente['_id']);
					$doc_cliente = array(
						"datosDemograficos"	 => $document['mensaje']['datosDemograficos'],
						"datosContractuales" => $document['mensaje']['datosContractuales']
					);
					$collection->update($criterio,array('$set' => $doc_cliente));
					$output = array(
						'estado' => "OK" ,
						'descripcionEstado' => "Modificacion exitosa",
						'CLIENTE' => $document['mensaje']
					);
				}
				catch(MongoCursorException $e) {
					
				}
				break;
			default:
				$this->_response('Metodo Invalido', 405);
				break;
        }
		return $output;
	}

	protected function login_respaldo(){
		$document= json_decode(file_get_contents('php://input'), true);
		$date=new DateTime(); 
		$fecha = $date->format('r');
		$output = array(
			'header' => array(
				'fecha' => $fecha,
				'estado' => "1",
				'descripcion' => "Error interno de servicio, error con conexion a la BD",
				'listaErrores' => array()
			),
			'mensajeOriginal' => $document['mensaje'],
			'datosAuditoria' => array(
				'fechaOperacion' =>$fecha,
				'usuarioOperacion' => $document['mensaje']['rut']
			)
		);
		$m = new MongoClient("mongodb://localhost:27017");	
		$db = $m->decdb;	
		$collection = $db->usuarios;
		$rut = $document['mensaje']['rut'];	
		$password = $document['mensaje']['password'];
			
		switch ($this->method) {
			case "POST":
				
				$cursor = $collection->find(array('rut'=>$rut, 'password' => $password ));
				if($cursor->count()>0){
					$output['header']['descripcion']="OK";
				}
				else{
					
					
					$error=true;
					$error_arr=array(
						'codError' => 201,
						'descripcionError'=>'Usuario o Password no coincide $rut $password'							
					);
					$output['header']['descripcion']="Error Operacion con errores de negocio";
					$output['header']['listaErrores'][]	=$error_arr;
				}
			break;
		}
		$m->close();
		return $output;
	}
	
	protected function usuarios(){
		switch($this->method) {
			case 'DELETE':
			break;
			case 'POST':
				switch($this->verb){
					case "actualizar":
						$this->output=$this->_usuarios->actualizaUsuario($this->document);
						break;
					case "datos":
						$this->output=$this->_usuarios->datosUsuario($this->document);
						break;
					case "tipodocumentos":
						$this->output=$this->_usuarios->tipoDocumentosUsuario($this->document);
						break;
					case "perfiles":
						$this->output=$this->_usuarios->perfilesUsuario($this->document);
						break;
					case "busqueda":
						$this->output=$this->_usuarios->busquedaUsuario($this->document);
						break;
					case "administraempresas":
						$this->output=$this->_usuarios->autorizaUsuario($this->document);
						break;
					case "autoriza":
						$this->output=$this->_usuarios->autorizaUsuario($this->document);
						break;
					case "autenticacion":				
						$this->output=$this->_usuarios->login($this->document);
						break;
					case "valida":				
						$this->output=$this->_usuarios->validaNuevoUsuario($this->document);
						break;
					case "olvidoclave":				
						$this->output=$this->_usuarios->olvidoClave($this->document);
						break;
					case "cambioclave":				
						$this->output=$this->_usuarios->cambioClave($this->document);
						break;
					default:
						$this->output=$this->_usuarios->creaNuevoUsuario($this->document);
						break;
				}
			break;
			case 'GET':
				$this->output=$this->_clientes->listaUsuarios();
			break;
			case 'PUT':
				switch ($this->verb) {
					case 'actualizaestado':
						$this->output=$this->_usuarios->actualizaEstado($this->document);
						break;
					default:
						$this->output=$this->_usuarios->actualizaUsuario($this->document);
						break;
				}
			    
			break;
		}
		return $this->output;
	}
	
	protected function errores(){
		switch($this->method) {
			case 'DELETE':
			break;
			case 'POST':
			break;
			case 'GET':
				$this->output=$this->_clientes->listaErrores();
				break;
			break;
			case 'PUT':
			break;
		}
		return $this->output;
	}

	protected function autenticacion(){
		switch($this->method) {
			case 'DELETE':
			break;
			case 'POST':
				$this->output=$this->_usuarios->login($this->document);
			break;
			case 'GET':
			break;
			case 'PUT':
			break;
		}
		return $this->output;
	}
	
	protected function clientes(){
		switch($this->method) {
			case 'DELETE':
				break;
			case 'POST':
				if (isset($this->verb)){
					switch($this->verb){
						case "empresasolicitada":
							$this->output=$this->_clientes->ClientePorRut($this->document);
							break;
						case "perfiles":
							$this->output=$this->_clientes->PerfilesEmpresa($this->document);
							break;
						default:
							break;
					}
				}
				else{
					//
				}
				break;
			case 'GET':
				$this->output=$this->_clientes->listaClientes();
				break;
			case 'PUT':
				break;
		}
		return $this->output;
	}

	protected function operadocumentos(){
		switch($this->method) {
			case 'DELETE':
				break;
			case 'POST':
				if (isset($this->verb)){
					switch($this->verb){
						case "carga":
							$this->output=$this->_operadocumentos->CargaDocumentos($this->document);
							break;
						case "cargaorden":
							$this->output=$this->_operadocumentos->CargaOrdenDocumentos($this->document);
							break;
						case "busqueda":
							$this->output=$this->_operadocumentos->ConsultaDocumentos($this->document);
							break;
						case "consulta":
							$this->output=$this->_operadocumentos->ConsultaDocumentos($this->document);
							break;
						case "firmantes":
							$this->output=$this->_operadocumentos->FirmantesDeDocumentos($this->document);
							break;
						default:
							break;
					}
				}
				else{
					//
				}
				break;
			case 'GET':
				$this->output=$this->_clientes->listaClientes();
				break;
			case 'PUT':
				break;
		}
		return $this->output;
	}

	protected function prueba(){
		switch ($this->method) {
			case 'GET':
				$this->output=$this->_test->pruebaMail($this->document);
				break;
			case 'POST':
				$this->output=$this->_perfiles->test($this->document);
				break;
			
			default:
				break;
		}
		return $this->output;
	}
	
	protected function perfiles(){
		switch($this->method) {
			case 'DELETE':
				break;
			case 'POST':
				if (isset($this->verb)){
					switch($this->verb){
						case "busqueda":
							$this->output=$this->_perfiles->busquedaPerfil($this->document);
						    break;
						case "crear":
							$this->output=$this->_perfiles->creaPerfil($this->document);
						    break;
						case 'actualizar':
							$this->output=$this->_perfiles->actualizaPerfil($this->document);
							break;
						default:
							$this->output=$this->_perfiles->creaPerfil($this->document);
						    break;
					}
				}
				else{
					$this->output=$this->_perfiles->creaPerfil($this->document);
				}
				break;
			case 'GET':
				break;
			case 'PUT':
				$this->output=$this->_perfiles->actualizaPerfil($this->document);
				break;
		}
		return $this->output;
	}

	protected function roles(){
		switch($this->method) {
			case 'DELETE':
				break;
			case 'POST':
				if (isset($this->verb)){
					switch($this->verb){
						case "busqueda":
							$this->output=$this->_roles->busquedaRoles($this->document);
						    break;
						default:
							$this->output=$this->_roles->busquedaRoles($this->document);
						    break;
					}
				}
				else{
					$this->output=$this->_roles->busquedaRoles($this->document);
				}
				break;
			case 'GET':
				break;
			case 'PUT':
				$this->output=$this->_roles->busquedaRoles($this->document);
				break;
		}
		return $this->output;
	}

	protected function subtipodocumentos(){
		switch($this->method) {
			case 'DELETE':
				break;
			case 'POST':
				if (isset($this->verb)){
					switch($this->verb){
						case "busqueda":
							$this->output=$this->_subtipodocumentos->busquedaSubTipoDocumento($this->document);
						    break;
						case "crear":
							$this->output=$this->_subtipodocumentos->creaSubTipoDocumento($this->document);
						    break;
						case 'actualizar':
							$this->output=$this->_subtipodocumentos->actualizaSubTipoDocumento($this->document);
							break;
						case "agregar":
							$this->output=$this->_firmantes->agregaFirmantes($this->document);
						    break;
						default:
							$this->output=$this->_subtipodocumentos->creaSubTipoDocumento($this->document);
						    break;
					}
				}
				else{
					$this->output=$this->_subtipodocumentos->creaSubTipoDocumento($this->document);
				}
				break;
			case 'GET':
				break;
			case 'PUT':
				$this->output=$this->_subtipodocumentos->actualizaSubTipoDocumento($this->document);
				break;
		}
		return $this->output;
	}

	protected function tipodocumentos(){
		switch($this->method) {
			case 'DELETE':
				break;
			case 'POST':
				if (isset($this->verb)){
					switch($this->verb){
						case "busqueda":
							$this->output=$this->_tipodocumentos->busquedaTipoDocumento($this->document);
						    break;
						case "crear":
							$this->output=$this->_tipodocumentos->creaTipoDocumento($this->document);
						    break;
						case 'actualizar':
							$this->output=$this->_tipodocumentos->actualizaTipoDocumento($this->document);
							break;
						default:
							$this->output=$this->_tipodocumentos->creaTipoDocumento($this->document);
						    break;
					}
				}
				else{
					$this->output=$this->_tipodocumentos->creaTipoDocumento($this->document);
				}
				break;
			case 'GET':
				break;
			case 'PUT':
				$this->output=$this->_tipodocumentos->actualizaTipoDocumento($this->document);
				break;
		}
		return $this->output;
	}

	protected function firmantes(){
		switch($this->method) {
			case 'DELETE':
				break;
			case 'POST':
				if (isset($this->verb)){
					switch($this->verb){
						case "busqueda":
							$this->output=$this->_firmantes->busquedaFirmantes($this->document);
						    break;
						case "firmar":
							$this->output=$this->_firmantes->firmarDocumento($this->document);
						    break;
						default:
							$this->output=$this->_firmantes->agregaFirmantes($this->document);
						    break;
					}
				}
				else{
					$this->output=$this->_firmantes->agregaFirmantes($this->document);
				}
				break;
			case 'GET':
				break;
			case 'PUT':
				$this->output=$this->_firmantes->actualizaFirmantes($this->document);
				break;
		}
		return $this->output;
	}
}


?>