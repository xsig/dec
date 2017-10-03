<?php
namespace Dec\model;
use Dec\database\MongoDBConn as MongoDBConn;

class Salida{
	private static $ConnMDB;
	
	public $salida;
	
	public function __construct(){
		self::$ConnMDB = new MongoDBConn();
		$this->documentoSalida();
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
					"listaDeErroresSistema" => array(),
					"listaDeErroresNegocio" => array(),
					"listaDeErroresCampo" => array()
				),
				"mensaje" => "",
				"mensajeOriginal" => "",
				"datosAuditoria" => array()
			)
		);
	}
	
	private function getDocumentoSalida(){
		return $this->salida;
	}
	
	public function seteaSalida($accionCod,$document){
		$this->salida['mensaje_dec']['mensaje']=""; 
		$tbl_salida=array();
		if (isset($document['mensaje_dec']['header']['usuario']) ){
			$usuario = $document['mensaje_dec']['header']['usuario'];
		}
		else{
			$usuario = "Anonimo";
		}

		$this->salida['mensaje_dec']['datosAuditoria']['fechaOperacion'] = date("Y-m-d H:i:s");
		$this->salida['mensaje_dec']['datosAuditoria']['usuarioOperacion'] = $usuario;	
		$this->salida['mensaje_dec']['header']['usuario'] = $usuario ;
		
		// Busqueda Datos de Accion
		$busqueda = array('accionCod' => $accionCod );
		$cursor = self::$ConnMDB->busca("salida", $busqueda);
		$contador = self::$ConnMDB->count("salida", $busqueda);

		if($contador>0){
			foreach ($cursor as $result) { 
				$tbl_salida = $result;
			}
			$this->salida['mensaje_dec']['header']['accion'] =$tbl_salida->accion;
			$this->salida['mensaje_dec']['header']['descripcion'] = $tbl_salida->descripcion;		
		}
		else{
			$this->salida['mensaje_dec']['header']['accion'] =$accionCod;
			$this->salida['mensaje_dec']['header']['descripcion'] = "";		
		}
		// Fin Busqueda Datos Accion

		$this->salida['mensaje_dec']['mensajeOriginal'] = $document['mensaje_dec']['mensaje'];
		return $this->salida;
		
	}
	
	private function seteaSalida2($modelo,$accion,$document){
		if (isset($document['mensaje_dec']['header']['usuario']) || empty($document['mensaje_dec']['header']['usuario']))
			$usuario = $document['mensaje_dec']['header']['usuario'];
		else
			$usuario = "Anonimo";
			
		$this->salida['mensaje_dec']['datosAuditoria']['fechaOperacion'] = date("Y-m-d H:i:s");
		$this->salida['mensaje_dec']['datosAuditoria']['usuarioOperacion'] = $usuario;	
		$this->salida['mensaje_dec']['header']['usuario'] = $usuario;
		switch($modelo){
			case "usuarios" :
				switch ($accion){
					case 2:
						$this->salida['mensaje_dec']['header']['accion'] = 2;
						$this->salida['mensaje_dec']['header']['descripcion'] = "Registro Nuevo Usuario - Selección de Empresas";	
						break;
					case 4:
						$this->salida['mensaje_dec']['header']['accion'] = 4;
						$this->salida['mensaje_dec']['header']['descripcion'] = "Autentificación Usuario - Password";	
						break;
					case 6:
						$this->salida['mensaje_dec']['header']['accion'] = 6;
						$this->salida['mensaje_dec']['header']['descripcion'] = "Menu - Datos Usuario";	
						break;
					case 8:
						$this->salida['mensaje_dec']['header']['accion'] = 8;
						$this->salida['mensaje_dec']['header']['descripcion'] = "Menu - Datos Usuario - Administracion de Usuarios";	
						break;
					case 10:
						$this->salida['mensaje_dec']['header']['accion'] = 10;
						$this->salida['mensaje_dec']['header']['descripcion'] = "Menu - Datos Usuario - Tipos de Documentos";	
						break;
					case 12:
						$this->salida['mensaje_dec']['header']['accion'] = 12;
						$this->salida['mensaje_dec']['header']['descripcion'] = "Autoriza Usuarios";	
						break;
					default:		
				}
				break;
			case "clientes" :
			    break;
		}
		$this->salida['mensaje_dec']['mensajeOriginal'] = $document['mensaje_dec']['mensaje'];
	}
}

?>