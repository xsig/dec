<?php
namespace Dec\model;
use Dec\database\MongoDBConn as MongoDBConn;
use Dec\utils\Funciones as Funciones;

class OperaDocumentos {
	private static $ConnMDB;

	public function __construct(){
		self::$ConnMDB = new MongoDBConn();
		$this->func = new Funciones();
	}

	public function validaConexion(){
        if(self::$ConnMDB)
            return true;
        return false;
    }
    public function ejecutaCargaDocumentos($document){
    	$_Doctos = new Documentos();
    	$_subtipoDocumentos = new SubTipoDocumentos();
    	$empresa = $document['mensaje_dec']['header']['empresa'];
    	$TipoDocumentos = $document['mensaje_dec']['mensaje']['TipoDocumentos'];
    	$subTipoDocumentos = $document['mensaje_dec']['mensaje']['subTipoDocumentos'];

    	//define("ENCRYPTION_KEY", "!@#$%^&*");
		//$string = "This is the original data string!";

		// echo $encrypted = encrypt($string, ENCRYPTION_KEY);
		// echo "<br />";
		// echo $decrypted = decrypt($encrypted, ENCRYPTION_KEY);
    	$cont = 1;

    	foreach ($document['mensaje_dec']['mensaje']['documentos'] as  $docs) {
    		$_id = 0 ;
    		$nombre  =  $empresa . $TipoDocumentos . $subTipoDocumentos . (string) date("YmdHis") . (string) $cont;
    		$nombreEncrypt = $this->func->encrypt_decrypt('encrypt', $nombre) ;
			
			$encoded = $docs['archivo'];

			$decoded = base64_decode($encoded);

			$firmas = $_subtipoDocumentos->traeFirmantesPorCodigoSubTipoDocumento($empresa ,$TipoDocumentos , $subTipoDocumentos);
			$firmantes = array();
			foreach ($firmas as $value) {
				$firmante['rutFirmante'] = "";
				$firmante['nombreFirmante'] = "";
				$firmante['emailFirmante'] = "";
				$firmante['nombrePerfil'] = $value->nombrePerfil;
				$firmante['descripcionPerfil'] = $value->descripcionPerfil;
				$firmante['orden'] = $value->orden;
				$firmante['fechaFirma'] = "";
				$firmante['codigoFirma'] = "";
				$firmante['estadoFirma'] = ($value->orden == 1 ) ? "DISPONIBLE FIRMA" : "PENDIENTE FIRMA";	

				$firmantes[] = $firmante;
			}

            $doc_documento = array(
	           "idAcepta" => 0,
	           "empresa" => $empresa,
	           "subtipoDocumento" => $subTipoDocumentos ,
	           "archivo" => $docs['archivo'],
	           "url" => "",
	           "fechaCarga" => date("Y-m-d H:i:s"),
	           "nombre" => $docs['nombre'],
	           "tamano" => $docs['tamano'],
	           "usuarioCreador" => $docs['usuarioCreador'],
	           "comentario" => $docs['comentario'],
	           "estado" => "PENDIENTE FIRMA",
	           "firmantes" => $firmantes
            );

            $_id =  self::$ConnMDB->ingresa("documentos",$doc_documento,"documento_id");


			$directorio = __DIR__ . '/../archivos/' . $empresa . '/' . $subTipoDocumentos . '/' . str_pad($_id,10,'0',STR_PAD_LEFT);

			$id_conCeros = str_pad($_id,10,'0',STR_PAD_LEFT);

			$directorio = __DIR__ . '/../archivos/' . $empresa ;
			if (!is_dir($directorio)) {
				mkdir($directorio,0777,true);
				chmod($directorio,0777);
			}
			$directorio = __DIR__ . '/../archivos/' . $empresa . '/' . $subTipoDocumentos ;
			if (!is_dir($directorio)) {
				mkdir($directorio,0777,true);
				chmod($directorio,0777);
			}
			$directorio = __DIR__ . '/../archivos/' . $empresa . '/' . $subTipoDocumentos . '/' . $id_conCeros;
			if (!is_dir($directorio)) {
				mkdir($directorio,0777,true);
				chmod($directorio,0777);
			}
			$directorio = __DIR__ . '/../archivos/' . $empresa . '/' . $subTipoDocumentos . '/' . $id_conCeros;

			file_put_contents( $directorio . '/' . $nombreEncrypt . '.pdf', $decoded);

			$url = $this->func->getURL() . $empresa . '/' . $subTipoDocumentos . '/' . $id_conCeros . '/' . $nombreEncrypt . '.pdf';
			$datosAct = array( 
				"idAcepta" => "ACEPTA-" . str_pad($_id,10,'0',STR_PAD_LEFT) ,
				"url" => $url
			);
			$cursor = self::$ConnMDB->actualizaPorId("documentos", $_id, $datosAct);

			$cont = $cont + 1;
		}

		return $_Doctos->traeDocumentoPorId($_id); 

    }

 //    public 	function file_put_contents($filename, $data, $file_append = false) {
	// 	$fp = fopen($filename, (!$file_append ? 'w+' : 'a+'));
	// 	if(!$fp) {
	// 		trigger_error('file_put_contents cannot write in file.', E_USER_ERROR);
	// 		return;
	// 	}
	// 	fputs($fp, $data);
	// 	fclose($fp);
	// }




}