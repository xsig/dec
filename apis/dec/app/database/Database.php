<?php

namespace Dec\database;
use Dec\config\ConfigData as ConfigData; 
use Dec\utils\Funciones as Funciones;
use MongoDB\Driver as MongoDB;

abstract class DBConn {
    protected $con;
    abstract function __construct();
    abstract function getVarsDB();
    abstract function GetConnectionString($host, $port, $dbname, $user, $pass);
}

class MongoDBConn extends DBConn {
	public  $col;
	private static $host;
	private static $port;
	private static $dbname;
	private static $user;
	private static $pass;
	private $func;
	private $writeConcern;
	private $_instance = false;
	
	public function __construct(){
		try{
			self::getVarsDB();
			$server = Self::GetConnectionString(self::$host, self::$port, self::$dbname, self::$user, self::$pass);
			$this->con = (new MongoDB\Manager($server)); //->selectDatabase($dbname);
			$this->writeConcern = new MongoDB\WriteConcern(MongoDB\WriteConcern::MAJORITY, 1000);
			$this->func = new Funciones();
		}catch(Exception $ex){
			$em = "Error al conectarse a la Base de Datos";
			throw new Exception($em, $ex);
		}
	}

	public function GetConnectionString($host, $port, $dbname, $user=NULL, $pass=NULL) {
		if (!isset($user) || trim($user)==='')
			return "mongodb://" . $host . ":" . $port  ;
		else
			return "mongodb://" . $user . ":" . $pass . "@" . $host . ":" . $port  ;
	}

	public function getInstance($host = false,$port =false, $dbname = false, $user = false, $pass = false){
		if($this->_instance === false){
			if($host === false || $port === false || $dbname === false)
				throw new Exception('You must pass arguments at first MongoDBConn::GetInstance() Call');
			$_instance = new MongoDBConn();
		}
		return $this->_instance;
	}

	public function getVarsDB(){
		$dataDB = new ConfigData();
		self::$host = $dataDB::$dbData['host'];
		self::$port = $dataDB::$dbData['port'];
		self::$dbname = $dataDB::$dbData['dbname'];
		self::$user = $dataDB::$dbData['user'];
		self::$pass = $dataDB::$dbData['pass'];
	}
	
	public function lista($collection){
		$query = new MongoDB\Query([]); 
		$rows = $this->con->executeQuery(self::$dbname . "." . $collection, $query);
		return $rows;
	}
	public function busca($collection , $busqueda){
		$query = new MongoDB\Query($busqueda); 
		$rows = $this->con->executeQuery(self::$dbname . "." . $collection, $query);
		return $rows;
	}

	public function buscaId($collection , $busqueda){
		$id = 0;
		$query = new MongoDB\Query($busqueda); 
		$rows = $this->con->executeQuery(self::$dbname . "." . $collection, $query);
		foreach ($rows as $tabla) {
			$id = $tabla->_id;
		}
		return $id;
	}

	public function count($collection , $busqueda){
		$nroFilas = 0 ;
		$query = new MongoDB\Command(['count' => $collection, 'query' => $busqueda ]); 
		$rows = $this->con->executeCommand(self::$dbname , $query);
		foreach ($rows as $value) {
			$nroFilas = $value->n;
		}
		return $nroFilas;
	}

	public function eliminaPorId($collection, $id){
		try{
			$bulk = new MongoDB\BulkWrite;
			$bulk->delete(['_id' => $id], ['limit' => 1]);
			$result = $this->con->executeBulkWrite(self::$dbname . "." . $collection,$bulk,$this->writeConcern);
			//$result = $this->con->executeDelete(self::$dbname . "." . $collection, $filter);
			if ($result->getDeletedCount()>0)
				return true;
			return false;
		} catch(MongoCursorException $e) {
			return false;
		}	
	}

	public function eliminaPorBusqueda($collection, $busqueda){
		try{
			$bulk = new MongoDB\BulkWrite;
			$bulk->delete($busqueda, ['limit' => 1]);
			$result = $this->con->executeBulkWrite(self::$dbname . "." . $collection,$bulk,$this->writeConcern);
			//$result = $this->con->executeDelete(self::$dbname . "." . $collection, $filter);
			if ($result->getDeletedCount()>0)
				return true;
			return false;
		} catch(MongoCursorException $e) {
			return false;
		}	
	}

	public function actualiza($collection, $busqueda, $datosAct){
		try{
			$id= 0;
			$id = $this->buscaId($collection, $busqueda);
			if($id==0){
				return false;
			}
			//$criteria = array('_id' => $id);
			//$document = array('$set' => $datosAct);

			$bulk = new MongoDB\BulkWrite;

			$bulk->update(
				['_id' => $id ],
				['$set' => $datosAct],
				['multi' => false, 'upsert' => false]
			);
			$result = $this->con->executeBulkWrite(self::$dbname . "." . $collection,$bulk,$this->writeConcern);

			//$result = $this->con->executeUpdate(self::$dbname . "." . $collection, $criteria,$document);
			return true;

		} catch(MongoCursorException $e) {
			return false;
		}	
	}

	public function actualizaPorId($collection, $id, $datosAct){
		try{
			//$criteria = array('_id' => $id);
			//$document = array('$set' => $datosAct);

			$bulk = new MongoDB\BulkWrite;

			$bulk->update(
			    ['_id' => $id ],
			    ['$set' => $datosAct],
			    ['multi' => false, 'upsert' => false]
			);
			$result = $this->con->executeBulkWrite(self::$dbname . "." . $collection,$bulk,$this->writeConcern);


			//$result = $this->con->executeUpdate(self::$dbname . "." . $collection, $criteria,$document);
			return true;
		} catch(MongoCursorException $e) {
			return false;
		}	
	}

	public function ingresa($collection, $document, $name){
		try{
			$secuencia = $this->getNextSequence($name);
			$db_array=array('_id' => $secuencia);
			foreach ($document as $key => $value) {
				$db_array[$key] = $value;
			}
			$bulk = new MongoDB\BulkWrite;
			$bulk->insert($db_array);

			$result = $this->con->executeBulkWrite(self::$dbname . "." . $collection,$bulk,$this->writeConcern);

			return $secuencia;
			
		} catch(MongoCursorException $e) {
			return false;
		}	
	}

	public function getNextSequence($name){
		$sequence = 0 ;
		$collection = "counters";
		$criteria = array('_id' => $name);
		$document = array( '$inc' => array("seq" => 1) );
		$updateOptions = array( "upsert" => true );

		$bulk = new MongoDB\BulkWrite;

		$bulk->update(
		    ['_id' => $name ],
		    ['$inc' => ['seq' => 1]],
		    ['multi' => false, 'upsert' => false]
		);
		$result = $this->con->executeBulkWrite(self::$dbname . "." . $collection,$bulk,$this->writeConcern);


		//$rows = $this->busca($collection, $criteria);
		//foreach ($rows as $value) {
		//	$sequence = $rows->seq;
		//}
		//var_dump($result);

		// $resultado = array();
		// foreach ($result as $value) {
		// 	$resultado[] = $value;
		// }
		// return $resultado;


		//return $retval['seq'];
		// $collection = $this->con->counters;
		// $retval = $collection->findAndModify(
		// 	 array('_id' => $name),
		// 	 array('$inc' => array("seq" => 1)),
		// 	 null,
		// 	 array(
		// 		"new" => true,
		// 	)
		// );

		$rows  = $this->busca($collection , $criteria);
		foreach ($rows as  $value) {
			$sequence = $value->seq;
		}
		return $sequence;
		
	}

}