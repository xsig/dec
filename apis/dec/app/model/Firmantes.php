<?php
namespace Dec\model;
use Dec\database\MongoDBConn as MongoDBConn;
use \setasign\Fpdi;
use \setasign\Fpdi\PdfParser\StreamReader;

class Firmantes {
	private static $ConnMDB;
	private $coll;

	public function __construct(){
		self::$ConnMDB = new MongoDBConn();
	}


	public function traeNombrePorIdFirmante($idFirmante){
		$nombre = "";
		$busqueda = array(
				"_id" => $idFirmante 
			);
		$cursor = self::$ConnMDB->busca("Firmantes", $busqueda);
		foreach ($cursor as $value) {
			$nombre = $value['nombrePerfil'];
		}
		return $nombre;
	}

	public function existeFirmanteEnEmpresa($nombrePerfil, $idCliente){
		$busqueda = array(
				"idCliente" => $idCliente 
			);
		$cursor = self::$ConnMDB->busca("FirmanteCliente", $busqueda);
		foreach ($cursor as $value) {
			$nombreFirmante = $this->traeNombrePorIdFirmante($value->idFirmante);
			if(strtolower($nombreFirmante) == strtolower($nombrePerfil)){
				return true;
			}
		}
		return false;
	}

	public function agregaFirmante($nombrePerfil,$descripcionPerfil ,$idSubTipoDoc, $idCliente){
		$doc_Firmante = array(
    		"nombrePerfil" =>  $nombrePerfil,
    		"descripcionPerfil" =>  $descripcionPerfil,
    	    "idSubTipoDoc"	=> $idSubTipoDoc, 
    	    "idCliente" => $idCliente,
    		"estado" =>  "ACTIVO"
    	);
		$_id =  self::$ConnMDB->ingresa("firmantes",$doc_Firmante,"firmantes_id");	
		return $_id;	
	}

	public function traeFirmanteEnEmpresa($nombrePerfil, $idCliente){
		$busqueda = array(
				"idCliente" => $idCliente 
			);
		$cursor = self::$ConnMDB->busca("FirmanteCliente", $busqueda);
		foreach ($cursor as $value) {
			$nombreFirmante = $this->traeNombrePorIdFirmante($value->idFirmante);
			if(strtolower($nombreFirmante) == strtolower($nombrePerfil)){
				return $value['idFirmante'];
			}
		}
		return 0;
	}

	public function existenFirmantesMismoOrden($arregloFirmantes, $orden){
		foreach ($arregloFirmantes as $firmante) {
			if ($firmante['orden'] == $orden){
				if ($firmante['estadoFirma'] == "DISPONIBLE FIRMA"){
					return true;
				}
			}
		}
		return false;
	}


	public function actualizaEstadosFirmantes($arregloFirmantes, $orden){
		$new_orden = $orden + 1;
		$resArregloFirmantes = array();
		foreach ($arregloFirmantes as $firmante) {
			if ($firmante['orden'] == $new_orden){
				if ($firmante['estadoFirma'] == "PENDIENTE FIRMA"){
					$firmante['estadoFirma'] = "DISPONIBLE FIRMA";
				}
			}
			$resArregloFirmantes[] = $firmante;
		}
		return $resArregloFirmantes;
	}



	public function estaFirmadoFirmantes($arregloFirmantes){
		foreach ($arregloFirmantes as  $firmante) {
			if ($firmante['estadoFirma'] != "FIRMADO"){
				return false;
			}
		}
		return true;
	}

	public function formatear_rut($rut)
	{
		$largo=strlen($rut);
		$dv=$rut[$largo-1];
		$rut=substr($rut,0,$largo-1);
		$i=strlen($rut)-1;
		$numero="-".$dv;
		$c=1;
		while($i>=0)
		{
			if($c%3==0 && $i!=0)
				$numero=".".$rut[$i].$numero;
			else
				$numero=$rut[$i].$numero;
			$c=$c+1;
			$i=$i-1;
		}
		return $numero;
	}

	public function firmarDocumento($rut_usuario,$rut_empresa,$idAcepta,$codigoFirma,$imagenHuella,$rutFirmante,$nombreFirmante,$emailFirmante,$nombrePerfilFirmante,$descripcionFirmante){
		$_documentos = new Documentos();
		$new_firmante = array();
		$tmp_firmante = array();
		$datosAct =array();
		$documento = $_documentos->traeDocumentoPorIdAcepta($idAcepta);
		$idDoc = $documento->_id;
		$archivo = base64_decode($documento->archivo);
		$pdf = new Fpdi\Fpdi();
		$pageCount=$pdf->setSourceFile(StreamReader::createByString($archivo));
		$lastFirmante = 0 ;
		$_usuarios = new Usuarios();
		$perfilesUsuario = $_usuarios->perfilesFirmaUsuario($rut_usuario,$rut_empresa);
		foreach ($documento->firmantes as  $firmante) {
			$tmp_firmante['rutFirmante'] = $firmante->rutFirmante;
			$tmp_firmante['nombreFirmante'] = $firmante->nombreFirmante;
			$tmp_firmante['emailFirmante'] = $firmante->emailFirmante;
			$tmp_firmante['nombrePerfil'] = $firmante->nombrePerfil;
			$tmp_firmante['descripcionPerfil'] = $firmante->descripcionPerfil;
			$tmp_firmante['fechaFirma'] = $firmante->fechaFirma;
			$tmp_firmante['estadoFirma'] = $firmante->estadoFirma;
			$tmp_firmante['codigoFirma'] = $firmante->codigoFirma;
			$tmp_firmante['orden'] = $firmante->orden;
			if ($nombrePerfilFirmante == $firmante->nombrePerfil && $descripcionFirmante == $firmante->descripcionPerfil && $firmante->estadoFirma == "DISPONIBLE FIRMA"){
				$tmp_firmante['rutFirmante'] = $rutFirmante;
				$tmp_firmante['nombreFirmante'] = $nombreFirmante;
				$tmp_firmante['emailFirmante'] = $emailFirmante;
				$tmp_firmante['nombrePerfil'] = $nombrePerfilFirmante;
				$tmp_firmante['descripcionPerfil'] = $descripcionFirmante;
				$tmp_firmante['fechaFirma'] = date("Y-m-d H:i:s");
				$tmp_firmante['estadoFirma'] = "FIRMADO";
				$tmp_firmante['codigoFirma'] = $codigoFirma;
				$lastFirmante = $firmante->orden;

				for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
					// import a page
					$templateId = $pdf->importPage($pageNo);
				
					$pdf->AddPage();
					// use the imported page and adjust the page size
					$pdf->useTemplate($templateId, ['adjustPageSize' => true]);
				}
				$pic = "data://text/plain;base64,". $imagenHuella;
				$pdf->SetFont('Arial','',10);
				if($lastFirmante==1)
					$pdf->AddPage();
				$pdf->SetXY(10,$firmante->orden*40);
				$pdf->Cell(20,30,$pdf->Image($pic,$pdf->GetX(),$pdf->GetY(),20,30,'png'),1);
				$rut_formateado=$this->formatear_rut($rutFirmante);
				$pdf->MultiCell(100,10,$nombrePerfilFirmante."\n".$rut_formateado."\n".$nombreFirmante,1,'C');
				
				$archivo=$pdf->Output('','S');
				$url=$documento->url;
				$p=strpos($url,"/archivos/");
				$file=substr($url,$p+10);
				$directorio = __DIR__ . '/../archivos/'.$file;
				$pdf->Output($directorio,'F');
			}

			$new_firmante[] = $tmp_firmante;
		}

		if ($lastFirmante > 0){

			$datosAct['firmantes'] =  $new_firmante;
			$datosAct['archivo'] = base64_encode($archivo);
			$cursor = self::$ConnMDB->actualizaPorId("documentos", $idDoc, $datosAct);	

			if(!$this->existenFirmantesMismoOrden($new_firmante, $lastFirmante) && $lastFirmante > 0 ){
				$new_firmante = $this->actualizaEstadosFirmantes($new_firmante, $lastFirmante);
			}

			if ($this->estaFirmadoFirmantes($new_firmante)){
				$datosAct['estado'] =  "FIRMADO";
			}

			$datosAct['firmantes'] =  $new_firmante;
			$cursor = self::$ConnMDB->actualizaPorId("documentos", $idDoc, $datosAct);
		}
		else{
			$idDoc = 0 ;
		}

		return $idDoc;
	}
}

?>