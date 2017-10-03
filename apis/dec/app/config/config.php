<?php
namespace Dec\config;
Class ConfigData {
	public static $dbData;
	public static $websiteData;
	public function __construct(){
		$this->getDataBaseData();
	}
	public function getDataBaseData(){
		//$jsonStr = file_get_contents("../../config.json");
		//$config = json_decode($jsonStr); 
//		$this->dbData = array(
//			"host" => $config->database->host, 
//			"user" => $config->database->user, 
//			"password" => $config->database->password,
//			"dbname" => $config->database->dbname,
//			"port" => $config->database->port
//		);
		self::$dbData = array(
			"host" => "localhost", 
			"user" => "", 
			"pass" => "",
			"dbname" => "decdb",
			"port" => "27017"
		);
	}
	
	public function getJsonParameters(){
		$jsonStr = file_get_contents("../../config.json");
		return json_decode($jsonStr);
	}

	public function getUrl(){
		self::$websiteData= array("url" => "http://54.149.168.80/apis/dec-sandbox/app/archivos/");
		return "http://54.149.168.80/apis/dec-sandbox/app/archivos/";
	}
}