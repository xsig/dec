<?php
namespace Dec\controller;
use Dec\model\TipoDocumentos as TipoDocumentos;
use Dec\model\Clientes as Clientes;
use Dec\model\Perfiles as Perfiles;
use Dec\model\Roles as Roles;
use Dec\model\Usuarios as Usuarios;
use Dec\model\Logging as Logging;
use Dec\model\Salida as _Salida;
use Dec\model\Perfilamientos as Perfilamientos;
use Dec\model\Identidad as Identidad;
use Dec\error\MensajeError as MensajeError;
use Dec\utils\Funciones as Funciones;
use \Firebase\JWT\JWT;

class UsuariosController{
	private $valid;
	private $objSalida;
	private $salida;
	private $func;
	private $_usuarios;
	private $_roles;	
	private $_perfiles;
	private $_tiposDocumentos;
	private $_clientes;
	private $_identidad;
	private $privateKey;
	private $publicKey;

	public function __construct(){
		$this->objSalida = new _Salida();
		$this->func = new Funciones();
		$this->Mensaje = new MensajeError();
		$this->_usuarios = new Usuarios();
		$this->_roles = new Roles();
		$this->_perfiles = new Perfiles();
		$this->_tiposDocumentos = new TipoDocumentos();
		$this->_clientes = new Clientes();
		$this->_identidad = new Identidad();
		
		$this->privateKey="-----BEGIN RSA PRIVATE KEY-----\n";
		$this->privateKey=$this->privateKey."MIIJKAIBAAKCAgEAxtMJaVdleLw2Ata++6TqVPib8VYVJ5ctbTrl/W18W/33JVVU\n";
		$this->privateKey=$this->privateKey."8eyt/yMnIFOIPz6DIYFZSwkpBmWQDNOTPWZk+SHf/X1foEqvJMaqkHe4FT7+EqXy\n";
		$this->privateKey=$this->privateKey."ED8aL3KBHkOakExt2VPcn4J0crBxmveRr6PlyMx08Q8WskAt2O8MCfq5JwD2zePU\n";
		$this->privateKey=$this->privateKey."CwMW79Ls/6iq6OrOg7WZob9oEOE4zhBRsTU7FwxOG5X9Hrr6MnxuLzAIl1W4bYbO\n";
		$this->privateKey=$this->privateKey."DHYRQzvMIyS+yBbVEcwezvo3ziLJGPBK7539fCpfaEEDoRuGE17DxNYFdZi91dBe\n";
		$this->privateKey=$this->privateKey."cGT8lNPt9TQsdamvboeJHLJ9ynZth8XesqmAqRaL4hJdA2B8vRU+IWn+eWNBq8Ky\n";
		$this->privateKey=$this->privateKey."UnX/JgsBhrKXx7v0bboyptlAEOAXM8RAsNrZdwgBvclJ86mGhQlQOshT+kHSmOV2\n";
		$this->privateKey=$this->privateKey."JL/FKhcuCThyurKivgDVnnYZccloaoiAfXvTxYdlcOTYitqmCZdEkffzJuTJL/jv\n";
		$this->privateKey=$this->privateKey."4ljg0mgcwHzomPgsoaDugQqxJ3/1Z8x0an46kgdprB/XPIBtqVVJ7KaSgLkwmzUR\n";
		$this->privateKey=$this->privateKey."6aRfYbF4A0O3QJzZOKfFZT7m4Hzgulo2Tembo+nimnH0u31nF1JkQZr0Y1KTSMM9\n";
		$this->privateKey=$this->privateKey."0imjaJLj863SBcDPpULI8V5nYnq4W6tnTkA/kynbO7obFBEn1sfPXD+J68UCAwEA\n";
		$this->privateKey=$this->privateKey."AQKCAgBJOq06uU/MWjXiccnB0YnlZfO5vaTpAgtfMdRHtS2ajD2c6ILy3+NuFzpv\n";
		$this->privateKey=$this->privateKey."85Q5BwMxMfz7YBJWIs4di0et06rY/5sKOEUiOp+rgeiMcSvB14OoxqoTRcqVMy1P\n";
		$this->privateKey=$this->privateKey."QkMJZr0G0JZvwZK2MzqEgy6LbGhTvspLhu0rFexM/C6I9ml/biF4z7Lno2mtRxi3\n";
		$this->privateKey=$this->privateKey."SR45z1HkvNwq8N6ZaPqNGwGbrSloYcXa7zFdQiyor5+9jYl8g7v7yyzU9h+BjeKw\n";
		$this->privateKey=$this->privateKey."Bvalp5MujOnD/fDT4YgDwW04OA6Gzux474kwq3yEYeDk/JbiKzGwMKC38Nn+ztfT\n";
		$this->privateKey=$this->privateKey."+jVt2OOeaBkeEre1/Ex3N2/AiiGyftixP7zvimfMuNJl9oqcGxxJkAVtuwLU0ayT\n";
		$this->privateKey=$this->privateKey."S9OJZ8AwSAoTjTyu+tQSS1ERUUreUBApduRtME27ce1U3sT83yhtbYBDpNG/kwzR\n";
		$this->privateKey=$this->privateKey."BUiyGhRoeX/CJaFWWeZNvTgGG7hIu0eSIcfrK3jBbYHasS1BXZxxCqyLe2bLKBEl\n";
		$this->privateKey=$this->privateKey."uaYNLtVtnN2j4z+iLmYM6CiGcDgOaYgjkk2TwsuGGIBH9iti51do7jlc9MNwh1HO\n";
		$this->privateKey=$this->privateKey."Kjcvu0rKo+PSpbswLyLip6IEwjtRAXNQYdXbSvJOUfjog4HZl4H5sAmiHB31M8LH\n";
		$this->privateKey=$this->privateKey."52MhjfsTFAUEC7hkABKbMmN57cMEvOh2C0EY8PojZ6//nqPQgQKCAQEA8kLF+iC0\n";
		$this->privateKey=$this->privateKey."8rfBsGxBd559l33bkB2NJSzxVMqKs75z17igRjZYF7yPDxwf12HFjLNHmEj0hfjW\n";
		$this->privateKey=$this->privateKey."yOzb96uUjrv2Sx0Fcfkx1zGjpLxgiJ0b7EFN8P4WiUV9/48YvSr4/PefrCim4EWt\n";
		$this->privateKey=$this->privateKey."bQ6WvOhJBseiPXwfu/OE/n/ERJcqL8oxpE92bAVCIXQb7KPRwdfYzJ5RmMJyI96x\n";
		$this->privateKey=$this->privateKey."t41T53EQ9Tz+16kknK0ilunDKC8OWinwMk6JJvvwxDWZxp8+0P1MruL584RJgo8I\n";
		$this->privateKey=$this->privateKey."ZwUuiRgNrj0Mu3R8RPgWrrs5f25QG/F5pNJqgqgiJKzmjNEZqXksr0/3wmgTKsMy\n";
		$this->privateKey=$this->privateKey."i38cz+U/2ZDzEQKCAQEA0hmjP7qrjb3y92DlDHrk2SUlJVWw15Irq52SzdeC/QNG\n";
		$this->privateKey=$this->privateKey."H0N54sq2G4cPmsogE0kf5loaiKMCUB6t+Ox2adnm3DPXTprS007GZA77n0rnhY2i\n";
		$this->privateKey=$this->privateKey."6BFxFkJbkAUPqYv+2G5wGc7LEGi6pd27y8u/oURvj2tIX3VqkXu7s8bxCPgQ3FTW\n";
		$this->privateKey=$this->privateKey."rR+1Y2vHqAS+o0adRG4hqK6C8lmVXX2gtT2WB6D3TKSrHlcgSZboTSWwCe1sY47C\n";
		$this->privateKey=$this->privateKey."RJWLim7dREwG3jWgx1SpEDjAqhjhmG8BJ70l8SU+b/f9k4Md0ZNuNDs0hss4Sprn\n";
		$this->privateKey=$this->privateKey."UIi+loYIBoX4E0uQVwpDsiTASbG68BYWlD+ke9OFdQKCAQB0skeNik2/kVaitjL+\n";
		$this->privateKey=$this->privateKey."/QCAhebK0AFahACoGHyhwr8ojc3epHTg0jqTS7fm1zkC4qU9LP9kvY4w8S+waR1B\n";
		$this->privateKey=$this->privateKey."eDdWzV7/HMuuXkH2q6tQg2Wc84Qo7yxJ6YidHwAKt3WC3YEzu81OwSGeI+Xmj3oF\n";
		$this->privateKey=$this->privateKey."4wo61dyve8l3knInnC19Icex33kq5YmKddSxs+PpnSDYx/aEQD4dGzu+MDzXgrZp\n";
		$this->privateKey=$this->privateKey."e31Cwz5YnmnICkiwxaIDOqpygTQ97CR5T1yIudLXdvyGTd2bOQDz/BRPE8br0QNe\n";
		$this->privateKey=$this->privateKey."CBhm/+CQlHTQrG0w/iFmpHY1OAqIb5cq1YKlGGBlK3Kj7EBrjBiXg7mISq3FUyfj\n";
		$this->privateKey=$this->privateKey."lJgRAoIBAQCQIXRjgMy1pSxr1nXrVNdvu9K6xQlXKXh599RBD+pVZgyR3/lawxOg\n";
		$this->privateKey=$this->privateKey."2Mu0tZrBgDW5EfEH8UPh8NoKXTVyskB0qb+3tfCRc4YYGEs34OvpK9wo9eYtjgJA\n";
		$this->privateKey=$this->privateKey."T6iJ+HcwxLp0ie+2ZxI8PVvamADzQf6CVefFTMh523dOAllSfNMcQ7st8wW9ma/T\n";
		$this->privateKey=$this->privateKey."LMYXPpce3aqLjIRae2hDRa6dBw3IV/2u/3xAiSamNTdRzVvxw4XK2qGc6TZcFmgG\n";
		$this->privateKey=$this->privateKey."tV00zXdpp0N/1F8fkYgZyXTybQj9YD5wE6FKs/Ud09UTUdZb7kfErnWnQtf0bShO\n";
		$this->privateKey=$this->privateKey."SVOA8SKpA2qjaCDdrWZ/07dTpkFRjS8NAoIBAGbVJHIBAskaaJK0yF2/j3dA5wdx\n";
		$this->privateKey=$this->privateKey."EtZGWmLfYBlN/hBn6ZfybUj6+McZUFZQODJ92KF6aW3zDLm69xI3wUoqcGovun30\n";
		$this->privateKey=$this->privateKey."enNglm82/EpBFpNNovqTRuJROA9TcWxU+IeOdd82vtdb+z0xoAxmjc12ACbkXUUt\n";
		$this->privateKey=$this->privateKey."sj+7DUY8FfNQKRC/4535CtDd03i/nfJPzkODvYLakUKf0ezcQMKM5BbQ1RbSvhAK\n";
		$this->privateKey=$this->privateKey."7io6/4o+g95UOlM5lvd9Xy54XkZOTtrNziR9Wus+Vb3opXkJyDFeFqjQJc8sAjKt\n";
		$this->privateKey=$this->privateKey."Zt/NqmiBsIe3LszZEmx7H6fmoqBFvducAEafUIZ3naYWiFYj5nTfw0B+6Jk=\n";
		$this->privateKey=$this->privateKey."-----END RSA PRIVATE KEY-----\n";

		$this->publicKey="-----BEGIN PUBLIC KEY-----\n";
		$this->publicKey=$this->publicKey."MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAxtMJaVdleLw2Ata++6Tq\n";
		$this->publicKey=$this->publicKey."VPib8VYVJ5ctbTrl/W18W/33JVVU8eyt/yMnIFOIPz6DIYFZSwkpBmWQDNOTPWZk\n";
		$this->publicKey=$this->publicKey."+SHf/X1foEqvJMaqkHe4FT7+EqXyED8aL3KBHkOakExt2VPcn4J0crBxmveRr6Pl\n";
		$this->publicKey=$this->publicKey."yMx08Q8WskAt2O8MCfq5JwD2zePUCwMW79Ls/6iq6OrOg7WZob9oEOE4zhBRsTU7\n";
		$this->publicKey=$this->publicKey."FwxOG5X9Hrr6MnxuLzAIl1W4bYbODHYRQzvMIyS+yBbVEcwezvo3ziLJGPBK7539\n";
		$this->publicKey=$this->publicKey."fCpfaEEDoRuGE17DxNYFdZi91dBecGT8lNPt9TQsdamvboeJHLJ9ynZth8XesqmA\n";
		$this->publicKey=$this->publicKey."qRaL4hJdA2B8vRU+IWn+eWNBq8KyUnX/JgsBhrKXx7v0bboyptlAEOAXM8RAsNrZ\n";
		$this->publicKey=$this->publicKey."dwgBvclJ86mGhQlQOshT+kHSmOV2JL/FKhcuCThyurKivgDVnnYZccloaoiAfXvT\n";
		$this->publicKey=$this->publicKey."xYdlcOTYitqmCZdEkffzJuTJL/jv4ljg0mgcwHzomPgsoaDugQqxJ3/1Z8x0an46\n";
		$this->publicKey=$this->publicKey."kgdprB/XPIBtqVVJ7KaSgLkwmzUR6aRfYbF4A0O3QJzZOKfFZT7m4Hzgulo2Temb\n";
		$this->publicKey=$this->publicKey."o+nimnH0u31nF1JkQZr0Y1KTSMM90imjaJLj863SBcDPpULI8V5nYnq4W6tnTkA/\n";
		$this->publicKey=$this->publicKey."kynbO7obFBEn1sfPXD+J68UCAwEAAQ==\n";
		$this->publicKey=$this->publicKey."-----END PUBLIC KEY-----\n";
	}
	
	public function datosUsuario($document){
		$this->salida = $this->objSalida->seteaSalida("datosUsuario",$document);
		$this->validaDatosUsuario($document);
		if ($this->valid){
			$this->traeUsuariodatosUsuario($document);
		}
		return $this->salida;
	}
	
	public function perfilesUsuario($document){
		$this->salida = $this->objSalida->seteaSalida("perfilUsuario",$document);
		$this->validaPerfilesUsuario($document);
		if ($this->valid){
			$this->traePerfilesUsuario($document);
		}
		return $this->salida;
	}
	public function olvidoClave($document){
		$this->salida = $this->objSalida->seteaSalida("olvidoClave",$document);
		$this->validaOlvidoClave($document);
		if ($this->valid){
			$this->ejecutaOlvidoClave($document);
		}
		return $this->salida;
	}
	public function cambioClave($document){
		$this->salida = $this->objSalida->seteaSalida("cambioClave",$document);
		$this->validaCambioClave($document);
		if ($this->valid){
			$this->ejecutaCambioClave($document);
		}
		return $this->salida;
	}

	public function actualizaEstado($document){
		$this->salida = $this->objSalida->seteaSalida("actualizaEstado",$document);
		$this->validaCambioEstado($document);
		if ($this->valid){
			$this->ejecutaCambioEstado($document);
		}
		return $this->salida;
	}

	public function creaNuevoUsuario($document){
		$this->salida = $this->objSalida->seteaSalida("IngUsuario",$document);
		$this->validaCrearUsuario($document);
		if ($this->valid){
			$id = $this->_usuarios->ingresaUsuario($document);
			if($id){
				if(!$this->_usuarios->enviaCorreo($document)){
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EnvioCorreoErr");
				}
				$this->salida['mensaje_dec']['mensaje'] = $this->_usuarios->traeUsuarioPorId($id);
				$this->salida['mensaje_dec']['header']['glosaEstado'] = "Operación Exitosa";
			}else{
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"IngresoUsuarioErr");
			}
		}
		return $this->salida;
	}

	public function validaNuevoUsuario($document){
		$this->salida = $this->objSalida->seteaSalida("validaNuevoUsuario",$document);
		$this->validaCrearUsuario($document);
		return $this->salida;
	}

	public function identificar($document)
	{
		$this->salida = $this->objSalida->seteaSalida("identificar",$document);
		$this->validaIdentificar($document);
		if($this->valid)
		{
			$rut = strtoupper($document['mensaje_dec']['mensaje']['rut']);
			$resultado = $this->_identidad->identificar($rut);
			if($resultado)
			{
				$this->salida['mensaje_dec']['header']['glosaEstado'] = "Operación Exitosa";
				$this->salida['mensaje_dec']['mensaje']=$resultado;				
			}
			else
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"ActualizaUsuarioErr");
		}
		return $this->salida;
	}

	private function validaIdentificar($document)
	{
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoIdentificar($document);
	}

	private function validaFormatoIdentificar($document)
	{
		$this->validaRutCondicion($document,true);
	}

	public function enrolar($document)
	{
		$this->salida = $this->objSalida->seteaSalida("enrolar",$document);
		$this->validaEnrolar($document);
		if($this->valid)
		{
			if($this->_identidad->enrolar($document))
				$this->salida['mensaje_dec']['header']['glosaEstado'] = "Operación Exitosa";
			else
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"ActualizaUsuarioErr");
		}
		return $this->salida;
	}

	private function validaEnrolar($document)
	{
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoEnrolar($document);
	}

	private function validaFormatoEnrolar($document)
	{
		$this->validaRutCondicion($document,false);
		$this->validaNombresAct($document);
		$this->validaApellidoPaternoAct($document);
		$this->validaApellidoMaternoAct($document);
	}

	private function validaRutCondicion($document,$existe){
		$rut = strtoupper($document['mensaje_dec']['mensaje']['rut']);
		if (!$this->func->valida_rut($rut)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutInvalidoErr","rut");
		}
		if (empty($rut)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRutVacioErr","rut");
		}
		$existe_bd=$this->_identidad->validaRutExiste($rut);
		if($existe!=$existe_bd)
		{
			$this->valid=false;
			if($existe)
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutNoExisteErr","rut");
			else
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutExisteErr","rut");
		}
	}

	public function actualizaUsuario($document){
		$this->salida = $this->objSalida->seteaSalida("actualizaUsuario",$document);
		$this->validaActualizaUsuario($document);
		if ($this->valid){
			if($this->_usuarios->actualizaUsuario($document)){
				$this->salida['mensaje_dec']['header']['glosaEstado'] = "Operación Exitosa";
			}else{
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"ActualizaUsuarioErr");
			}
		}
		return $this->salida;
	}
	
	public function login($document){
		$this->salida = $this->objSalida->seteaSalida("loginUsuario",$document);
		$this->validaLogin($document);
		if ($this->valid){
			if(isset($document['mensaje_dec']['mensaje']['password'])){
				$this->validaClavePassword($document);
			}
			else{
				$this->validaClaveHuella($document);
			}
		}
		return $this->salida;
	}

	public function tipoDocumentosUsuario($document){
		// Modificar para Buscar Documentos
		$this->salida = $this->objSalida->seteaSalida("tipoDocumentoUsuario",$document);
		$this->validaTipoDocumnetos($document);
		if ($this->valid){
			$this->traeTipoDocumentos($document);
		}
		return $this->salida;
	}
	
	public function busquedaUsuario($document){
		$this->salida = $this->objSalida->seteaSalida("busquedaUsuario",$document);
		$this->validaBusquedaUsuario($document);
		if ($this->valid){
			$this->traeBusquedaUsuario($document);
		}			
		return $this->salida;
	}
	
	public function autorizaUsuario($document){
		$this->salida = $this->objSalida->seteaSalida("autorizaUsuario",$document);
		$this->validaAutorizaUsuario($document);
		if ($this->valid){
			$this->resultadoAutorizaUsuario($document);
		}
		return $this->salida;
	} 

	private function validaCambioClave($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaRutUsuario($document);	
		$this->validaRutCambioClave($document);	
		$this->validaRutyUsuario($document);		
	}

	private function validaOlvidoClave($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaRutUsuario($document);		
	}
	private function validaCambioEstado($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoCambioEstado($document);			
	}
	private function validaAutorizaUsuario($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoAutorizaUsuario($document);
	}
	
	private function validaBusquedaUsuario($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoBusquedaUsuario($document);
	}
	
	private function validaPerfilesUsuario($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoPerfilesUsuario($document);
	}

	private function validaActualizaUsuario($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoActualizaUsuario($document);
	}
	
	private function validaTipoDocumnetos($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoTipoDocumentos($document);
	}
	
	private function validaFormatoAutorizaUsuario($document){
		$rut_usuario = $document['mensaje_dec']['header']['usuario'];
		if(isset($document['mensaje_dec']['mensaje']['rutUsuario'])){
			if(!$this->func->valida_rut($document['mensaje_dec']['mensaje']['rutUsuario'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutInvalidoErr","rut");
			}
			if (empty($document['mensaje_dec']['mensaje']['rutUsuario'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRutVacioErr","rutUsuario");
			}
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRutNoViene");
		}

		if(isset($document['mensaje_dec']['mensaje']['decisiones'])){
			if(is_array($document['mensaje_dec']['mensaje']['decisiones'])){
				$desicionAS = $document['mensaje_dec']['mensaje']['decisiones'];
				$arrDesicion = array();
				foreach($desicionAS as $valor ){
					if(isset($valor['autoriza']) && isset($valor['empresa']) ){
						$autoriza = $valor['autoriza'];
						$empresa = $valor['empresa'];
						if (!$this->func->valida_rut($empresa)){
							$this->valid=false;
							$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutEmpInvalidoErr", "empresa",$empresa);
						}
						else{ 
							if(!$this->_clientes->existeClientePorRut($empresa)){
								$this->valid=false;
								$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresaSolicitadaNoExisteErr", "empresa",$empresa);
							}
						}

						if(strtoupper($autoriza) == "SI"){
							if(isset($valor['perfilesAsociados'])){
								$perfilesAsociados = $valor['perfilesAsociados'];	
								if(is_array($perfilesAsociados)){
									if(count($perfilesAsociados)>0){
										foreach($perfilesAsociados as $perf){
											if(!$this->_perfiles->existePerfil($empresa,$perf)){
												$this->valid=false;
												$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"perfilNoExiste", "perfilesAsociados",$perf);
											}
										}
									}
								}
								else{ 
									$this->valid=false;
									$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"perfilNoArreglo", "perfilesAsociados");
								}
							}
							if(isset($valor['perfilesDesasociados'])){
								$perfilesDesasociados = $valor['perfilesDesasociados'];
								if(is_array($perfilesDesasociados)){
									if(count($perfilesDesasociados)>0){
										foreach($perfilesDesasociados as $perf){
											if(!$this->_perfiles->existePerfil($empresa,$perf)){
												$this->valid=false;
												$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"perfilNoExiste", "perfilesDesasociados",$perf);
											}
										}
									}
								}
								else{ 
									$this->valid=false;
									$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"perfilNoArreglo", "perfilesDesasociados");
								}
							}
						}
						if(strtoupper($autoriza) == "NO"){
							if(isset($valor['perfilesDesasociados'])){
								$perfilesDesasociados = $valor['perfilesDesasociados'];
								if(is_array($perfilesDesasociados)){
									if(count($perfilesDesasociados)>0){
										foreach($perfilesDesasociados as $perf){
											if(!$this->_perfiles->existePerfil($empresa,$perf)){
													$this->valid=false;
													$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"perfilNoExiste", "perfilesDesasociados",$perf);
											}
										}
									}
									else{ 
										$this->valid=false;
										$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"perfilNoArreglo", "perfilesDesasociados");
									}
								}
							}
						}

					}
				}

			} // if is array
			else{
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"DesicionASFormatoErr");
			}
		} // if isset desiciones
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoDesicionAS");
		}// else isset desiciones

	}
	private function validaFormatoActualizaUsuario($document){
		$this->validaRutAct($document);
		$this->validaNombresAct($document);
		$this->validaApellidoPaternoAct($document);
		$this->validaApellidoMaternoAct($document);
		$this->validaCorreoElectronicoAct($document);
		$this->validaGeneroAct($document);
		$this->validaClaveAct($document);	
		$this->validaEmpresasAct($document);
	}
	private function validaFormatoTipoDocumentos($document){
		if(isset($document['mensaje_dec']['mensaje']['rut'])){
			if(!$this->func->valida_rut($document['mensaje_dec']['mensaje']['rut'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutInvalidoErr","rut");
			}
			if (empty($document['mensaje_dec']['mensaje']['rut'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRutVacioErr","rut");
			}
		}
		if(!$this->func->valida_rut($document['mensaje_dec']['header']['usuario'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"UsuarioRutInvalidoErr");
		}
		if (empty($document['mensaje_dec']['header']['usuario'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"UsuarioCampoRutVacioErr");
		}
	}
	private function validaFormatoCambioEstado($document){
		// Campo Rut
		if(isset($document['mensaje_dec']['mensaje']['rut'])){
			if(!$this->func->valida_rut($document['mensaje_dec']['mensaje']['rut'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutInvalidoErr","rut");
			}
			if (empty($document['mensaje_dec']['mensaje']['rut'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRutVacioErr","rut");
			}
		}
		if(!$this->func->valida_rut($document['mensaje_dec']['header']['usuario'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"UsuarioRutInvalidoErr");
		}
		if (empty($document['mensaje_dec']['header']['usuario'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"UsuarioCampoRutVacioErr");
		}
		// Campo Estado
		if (isset($document['mensaje_dec']['mensaje']['estado'])){
			$estado = strtoupper($document['mensaje_dec']['mensaje']['estado']);
			if ($estado != "ACTIVO" || $estado != "INACTIVO"){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EstadoNoValido");
			}
			if($estado = ""){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EstadoVacio", "estado");
			}
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EstadoNoIngresado");
		}

		// Empresas

		if(isset($document['mensaje_dec']['mensaje']['empresas'])){
			$empresas = $document['mensaje_dec']['mensaje']['empresas'];
			if(is_array($empresas)){
				$this->validaArrayEmpresas($empresas);
			}
			else{
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"ArrEmpresasMalFormado");
			}
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresasNoIngresadas");
		}
	}
	private function validaArrayEmpresas($empresas){
		if(count($empresas) > 0){
			foreach($empresas as $emps){
				if($this->usuarios->validaEmpresaExista($emps)){
					
				}
				else{
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"ArregloEmpresasVacio");
				}
			}
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"ArregloEmpresasVacio");
		}
	}
	private function validaFormatoOlvidoClave($document){
		if(!$this->func->valida_rut($document['mensaje_dec']['header']['usuario'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"UsuarioRutInvalidoErr");
		}
		if (empty($document['mensaje_dec']['header']['usuario'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"UsuarioCampoRutVacioErr");
		}
	}

	private function validaFormatoBusquedaUsuario($document){
		if (isset($document['mensaje_dec']['mensaje']['rutUsuario'])){
			$filtroRut = $document['mensaje_dec']['mensaje']['rutUsuario'];
			if (!$this->func->valida_rut($filtroRut)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"filtroRutInvalidoErr","rutUsuario");
			}
		}
		if (isset($document['mensaje_dec']['mensaje']['nombreUsuario'])){
			if (empty($document['mensaje_dec']['mensaje']['nombreUsuario']) && isset($document['mensaje_dec']['mensaje']['nombreUsuario'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoNombreVacioErr","nombreUsuario");
			}
		}

		if (isset($document['mensaje_dec']['mensaje']['apellidoUsuario'])){
			if (empty($document['mensaje_dec']['mensaje']['apellidoUsuario']) && isset($document['mensaje_dec']['mensaje']['apellidoUsuario'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoApellidoPaternoErr","apellidoUsuario");
			}
		}
				// Campo Estado
		if (isset($document['mensaje_dec']['mensaje']['estado'])){
			$estado = strtoupper($document['mensaje_dec']['mensaje']['estado']);
			if (!($estado == "ACTIVO" || $estado == "INACTIVO")){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EstadoNoValido", "estado");
			}
			if($estado = ""){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EstadoVacio", "estado");
			}
		}

		if (isset($document['mensaje_dec']['mensaje']['conTramite'])){
			$conTramite = strtoupper($document['mensaje_dec']['mensaje']['conTramite']);
			if (!($conTramite == "NO" || $conTramite == "SI")){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"conTramiteNoValido", "conTramite");
			}
			if($conTramite = ""){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"conTramiteVacio", "conTramite");
			}
		}

		if(isset($document['mensaje_dec']['mensaje']['empresa'])){
			$emp = $document['mensaje_dec']['mensaje']['empresa'];
			if (!$this->func->valida_rut($emp))
			{
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutEmpInvalidoErr", "empresa",$emp);
			}
		}
	}
	
	private function validaFormatoPerfilesUsuario($document){
		$rut_usuario = $document['mensaje_dec']['header']['usuario'];

		if (isset($document['mensaje_dec']['mensaje']['Filtros']['rutUsuario'])){
			$filtroRut = $document['mensaje_dec']['mensaje']['Filtros']['rutUsuario'];
			if (!$this->func->valida_rut($filtroRut)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"filtroRutInvalidoErr","rutUsuario");
			}
		}
		if(isset($document['mensaje_dec']['mensaje']['Filtros']['empresa'])){
			$emp = $document['mensaje_dec']['mensaje']['Filtros']['empresa'];
			if (!$this->func->valida_rut($emp)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutEmpInvalidoErr", "empresa",$emp);
			}
		}
	}
	private function traeTipoDocumentos($document){
		$tiposDocumentosArr = array();
		$rut_usuario = strtoupper($document['mensaje_dec']['header']['usuario']);
		if (isset($document['mensaje_dec']['header']['rut'])){
			$rut_consultar = strtoupper($document['mensaje_dec']['mensaje']['rut']);
			if ($this->func->valida_rut($rut_consultar)){
				if ($this->_usuarios->validaPerfilTipoDocumentos($rut_usuario, $rut_consultar)){
					$IdUsuarioConsultar = $this->_usuarios->traeIdUsuarioPorRut($rut_consultar);
					$ListaEmpresas = $this->_usuarios->traeListaIdClientesAutorizadasPorRut($rut_consultar);
					$tiposDocumentosArr = $this->_tiposDocumentos->traeListaTiposDocumentos($ListaEmpresas);
					$this->salida['mensaje_dec']['mensaje'] = $tiposDocumentosArr;
				}
				else{
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"PerfilNoValidoErr");
				}
			}
		}
		else{
			$IdUsuarioConsultar = $this->_usuarios->traeIdUsuarioPorRut($rut_usuario);
			$ListaEmpresas = $this->_usuarios->traeListaIdClientesAutorizadasPorRut($rut_usuario);
			$tiposDocumentosArr = $this->_tiposDocumentos->traeListaTiposDocumentos($ListaEmpresas);
			$this->salida['mensaje_dec']['mensaje'] = $tiposDocumentosArr;
		}
	}

	private function resultadoAutorizaUsuario($document){
		$rut_usuario = $document['mensaje_dec']['mensaje']['rutUsuario'];
		$decisiones = $document['mensaje_dec']['mensaje']['decisiones'];
		foreach ($decisiones as $decision) {
			if(strtoupper($decision['autoriza']) == "SI")
			{
				if(isset($decision['empresa']))
				{
					$this->_usuarios->autorizaEmpresa($rut_usuario,$decision['empresa']);
					$this->_usuarios->EliminaConTramite($rut_usuario,$decision['empresa']);
				}
				if(isset($decision['perfiles']))
				{
					$this->_usuarios->asociarPerfiles($rut_usuario,$decision['empresa'],$decision['perfiles']);
				}
			}
			if(strtoupper($decision['autoriza']) == "NO")
			{
				if(isset($decision['empresa']))
				{
					$this->_usuarios->desautorizaEmpresa($rut_usuario,$decision['empresa']);
					$this->_usuarios->EliminaConTramite($rut_usuario,$decision['empresa']);
				}
			}
		}
		$this->salida['mensaje_dec']['mensaje'] = $document['mensaje_dec']['mensaje'];

	}

	private function traeBusquedaUsuario($document){
		$validaBusq = 1;
		$usuariosBuscado ="";
		$busqueda = array();
		$filtroRut = "";
		$filtroNombre = "";
		$filtroApellido = "";
		$filtroPerfil = "";
		$filtroEstado = "";
		$filtroConTramite = "";
		$listaEmpresas = "";

		$rut_usuario = strtoupper($document['mensaje_dec']['header']['usuario']);
		
		if (isset($document['mensaje_dec']['mensaje']['rutUsuario'])){
			if ($document['mensaje_dec']['mensaje']['rutUsuario']!=NULL)
				$filtroRut = strtoupper($document['mensaje_dec']['mensaje']['rutUsuario']);
		}	
		
		if (isset($document['mensaje_dec']['mensaje']['nombreUsuario'])){
			if ($document['mensaje_dec']['mensaje']['nombreUsuario']!=NULL)
				$filtroNombre = strtoupper($document['mensaje_dec']['mensaje']['nombreUsuario']) ;
		}	
			
		if (isset($document['mensaje_dec']['mensaje']['apellidoUsuario'])){
			if ($document['mensaje_dec']['mensaje']['apellidoUsuario']!=NULL)
				$filtroApellido = strtoupper($document['mensaje_dec']['mensaje']['apellidoUsuario']) ;
		}
		
		if(isset($document['mensaje_dec']['mensaje']['perfilUsuario'])){
			if ($document['mensaje_dec']['mensaje']['perfilUsuario']!=NULL)
				$filtroPerfil = strtoupper($document['mensaje_dec']['mensaje']['perfilUsuario']) ;
		}
		
		if(isset($document['mensaje_dec']['mensaje']['estado'])){
			if ($document['mensaje_dec']['mensaje']['estado']!=NULL)
				$filtroEstado = $document['mensaje_dec']['mensaje']['estado'];
		}

		if(isset($document['mensaje_dec']['mensaje']['conTramite'])){
			if ($document['mensaje_dec']['mensaje']['conTramite']!=NULL)
				$filtroConTramite = strtoupper($document['mensaje_dec']['mensaje']['conTramite']) ;
		}
		
		$busquedaUsuario =  array();
		if ($filtroRut != "" ){
			$busquedaUsuario['rut'] = $filtroRut;
		}
		if ($filtroNombre != "" ){
			$busquedaUsuario['nombre'] = array('$regex' => $filtroNombre); 
		}
		if ($filtroApellido != "" ){
			$busquedaUsuario['apellidoPaterno'] = array('$regex' => $filtroApellido);
		}
		if ($filtroEstado != "" ){
			$busquedaUsuario['estado'] = $filtroEstado;
		}
		if ($filtroConTramite != "" ){
			$busquedaUsuario['conTramite'] = $filtroConTramite;
		}
		
		$usuariosId = $this->_usuarios->buscaUsuarioFiltros($busquedaUsuario);
		
		$this->salida['mensaje_dec']['mensaje']['ListaUsuarios'] = $this->_usuarios->traeUsuariosPorListaIdBusqueda($usuariosId);
	}

    private function ejecutaCambioClave($document){
    	$rut = $document['mensaje_dec']['mensaje']['rut'];
    	$clave = $document['mensaje_dec']['mensaje']['password'];
		$this->_usuarios->CambiaPassword($rut, $clave, true);
    }

    private function ejecutaOlvidoClave($document){
    	$rut = $document['mensaje_dec']['header']['usuario'];
    	$password = $this->rand_string(8);
		$fechaHora = date("Y-m-d H:i:s");
		$claveTemporal = array(
				"claveTemporal" => array(
						"password" => $password,
						"fechaHora" => $fechaHora
					)
			);

		if($this->_usuarios->ActualizaClaveTemporal($rut, $claveTemporal)){
			$this->_usuarios->enviaCorreoClaveTemporal($rut, $password);
		}
    }

    private function rand_string( $length = 8 ) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		return substr(str_shuffle($chars),0,$length);
	}

	private function validaDatosUsuario($document){
		$this->valid = true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoDatosUsuario($document);
	}
	
    private function validaCrearUsuario($document){
		$this->valid=true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoCreaUsuario($document);
	}
	
	private function validaLogin($document){
		$this->valid=true;
		$this->validaConexion();
		$this->validaDocumento($document);
		$this->validaFormatoLogin($document);
		$this->validaRutUsuario($document);
	}
	
	private function validaDocumento($document){
		if (!$this->func->validaDocumento($document)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"DocumentoErr");
		}
	}
	
	private function validaConexion(){
		if(!$this->_usuarios->validaConexion()){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"conexionErr");
		}		
	}
	
	private function validaFormatoLogin($document){
		if(!isset($document['mensaje_dec']['mensaje']['usuario'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"AutenticacionNoCampoUsuarioErr");			
		}
		if(isset($document['mensaje_dec']['mensaje']['password']) && isset($document['mensaje_dec']['mensaje']['huella'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"AmbosCamposPasswordHuellaErr");
		}
		if(!(isset($document['mensaje_dec']['mensaje']['password']) || isset($document['mensaje_dec']['mensaje']['huella']))){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"NoCamposPasswordHuellaErr");
		}
		if(isset($document['mensaje_dec']['mensaje']['password']))
			$this->validaClave($document);
	}

	private function validaFormatoDatosUsuario($document){
		if(isset($document['mensaje_dec']['mensaje']['rutUsuario'])){
			if(!$this->func->valida_rut($document['mensaje_dec']['mensaje']['rutUsuario'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutInvalidoErr","rutUsuario");
			}
			if (empty($document['mensaje_dec']['mensaje']['rutUsuario'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRutVacioErr","rutUsuario");
			}
		}
		if(!$this->func->valida_rut($document['mensaje_dec']['header']['usuario'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"UsuarioRutInvalidoErr");
		}
		if (empty($document['mensaje_dec']['header']['usuario'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"UsuarioCampoRutVacioErr");
		}
	}
	
	private function validaFormatoCreaUsuario($document){
		$this->validaRut($document);
		$this->validaNombres($document);
		$this->validaCorreoElectronico($document);
		$this->validaGenero($document);
		$this->validaClave($document);
		$this->validaEmpresas($document);
	}
	
	private function validaRut($document){
		$rut = strtoupper($document['mensaje_dec']['mensaje']['rut']);
		if (!$this->func->valida_rut($rut)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutInvalidoErr","rut");
		}
		if (empty($rut)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRutVacioErr","rut");
		}
		if ($this->_usuarios->validaRutExiste($rut)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutExisteErr","rut");
		}

	}

	private function validaRutAct($document){
		$rut = strtoupper($document['mensaje_dec']['mensaje']['rut']);
		if (!$this->func->valida_rut($rut)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutInvalidoErr","rut");
		}
		if (empty($rut)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRutVacioErr","rut");
		}
		if (!$this->_usuarios->validaRutExiste($rut)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutNoExisteErr","rut");
		}
	}
	
	private function validaRutCambioClave($document){
		$rut = $document['mensaje_dec']['mensaje']['rut'];
		if (!$this->func->valida_rut($rut)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutInvalidoErrNeg");
		}
		if (empty($rut)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoRutVacioErrNeg");
		}
		if (!$this->_usuarios->validaRutExiste($rut)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutNoExisteErrNeg");
		}
	}

	private function validaRutyUsuario($document){
		return $document['mensaje_dec']['mensaje']['rut'] == $document['mensaje_dec']['header']['usuario'] ;
	}

	private function validaRutUsuario($document){
		$rut = $document['mensaje_dec']['header']['usuario'];
		if (!$this->func->valida_rut($rut)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"UsuarioRutInvalidoErr");
		}
		if (empty($rut)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"UsuarioCampoRutVacioErr");
		}
		if (!$this->_usuarios->validaRutExiste($rut)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"RutNoExisteErrNeg");
		}

	}
	
	private function validaNombres($document){
		if (empty($document['mensaje_dec']['mensaje']['Nombre']) && isset($document['mensaje_dec']['mensaje']['Nombre'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoNombreVacioErr","Nombre");
		}
		if (empty($document['mensaje_dec']['mensaje']['apellidoPaterno']) && isset($document['mensaje_dec']['mensaje']['apellidoPaterno'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoApellidoPaternoErr","apellidoPaterno");
		}
	}

	private function validaNombresAct($document){
		if(isset($document['mensaje_dec']['mensaje']['Nombre'])){
			if (empty($document['mensaje_dec']['mensaje']['Nombre'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoNombreVacioErr","Nombre");
			}		
		}
	}
	
	private function validaApellidoPaternoAct($document){
		if(isset($document['mensaje_dec']['mensaje']['apellidoPaterno'])){
			if (empty($document['mensaje_dec']['mensaje']['apellidoPaterno'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoApellidoPaternoErr","apellidoPaterno");
			}			
		}
	}
	private function validaApellidoMaternoAct($document){
		if(isset($document['mensaje_dec']['mensaje']['apellidoMaterno'])){
			if (empty($document['mensaje_dec']['mensaje']['apellidoMaterno'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoApellidoMaternoErr","apellidoMaterno");
			}			
		}
	}

	private function validaCorreoElectronicoAct($document){
		if(isset($document['mensaje_dec']['mensaje']['correoElectronico'])){
			$correoElectronico = $document['mensaje_dec']['mensaje']['correoElectronico'];
			$rut = $document['mensaje_dec']['mensaje']['rut'];
			if (!$this->func->validaEmail($correoElectronico)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"correoElectronicoErr","correoElectronico");
			}
			if (empty($correoElectronico)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoCorreoElectronicoVacioErr","correoElectronico");
			}
	/*		if ($this->_usuarios->validaCorreoExisteAct($correoElectronico, $rut)){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoCorreoElectronicoExisteErr","correoElectronico");
			}*/
		}  
	}

	private function validaCorreoElectronico($document){
		$correoElectronico = $document['mensaje_dec']['mensaje']['correoElectronico'];
		if (!$this->func->validaEmail($correoElectronico)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"correoElectronicoErr","correoElectronico");
		}
		if (empty($correoElectronico)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoCorreoElectronicoVacioErr","correoElectronico");
		}
		if ($this->_usuarios->validaCorreoExiste($correoElectronico)){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoCorreoElectronicoExisteErr","correoElectronico");
		}
	}
	
	private function validaGenero($document){
		$genero = strtoupper($document['mensaje_dec']['mensaje']['genero']);
		if (empty($genero) && isset($document['mensaje_dec']['mensaje']['genero'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoGeneroVacioErr","genero");
		}
		if ($genero!="M" && $genero!="F"){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoGeneroNoValidoErr","genero");
		}
	}
	
	private function validaGeneroAct($document){
		if (isset($document['mensaje_dec']['mensaje']['genero'])){
			$genero = strtoupper($document['mensaje_dec']['mensaje']['genero']);
			if (empty($genero) && isset($document['mensaje_dec']['mensaje']['genero'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoGeneroVacioErr","genero");
			}
			if ($genero!="M" && $genero!="F"){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoGeneroNoValidoErr","genero");
			}
		}
	}

	private function validaClaveAct($document){
		if(isset($document['mensaje_dec']['mensaje']['password'])){
			$password = $document['mensaje_dec']['mensaje']['password'];
			if (empty($password) && isset($document['mensaje_dec']['mensaje']['password'])){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoPasswordVacioErr","password");
			}
			if (strlen($password)<5){
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoPasswordLargoErr","password");
			}	
		}
	}

	private function validaClave($document){
		$password = $document['mensaje_dec']['mensaje']['password'];
		if (empty($password) && isset($document['mensaje_dec']['mensaje']['password'])){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoPasswordVacioErr","password");
		}
		if (strlen($password)<5){
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"CampoPasswordLargoErr","password");
		}		
	}
	
	
	private function validaEmpresas($document){
		if (isset($document['mensaje_dec']['mensaje']['empresasSolicitadas'])) {
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
		
	}
	
	private function validaEmpresasAct($document){
		if (isset($document['mensaje_dec']['mensaje']['empresasSolicitadas'])) {
			$empresasSolicitadas = $document['mensaje_dec']['mensaje']['empresasSolicitadas'];
			if(is_array($empresasSolicitadas)){
				// if(count($empresasSolicitadas)>0){
					foreach ($empresasSolicitadas as $rutEmpresa) { 
						if(!$this->_usuarios->validaEmpresaExista(strtoupper($rutEmpresa))){
							$this->valid=false;
							$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresaSolicitadaNoExisteErr","empresasSolicitadas",$rutEmpresa);						
						}
					}
				// }
				// else{
				// 	$this->valid=false;
				// 	$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresasSolicitadasErr","empresasSolicitadas");
				// }
			}
			else{
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EmpresasSolicitadasErr","empresasSolicitadas");
			}
		}

	}
	private function traeUsuariodatosUsuario($document)
	{
		$rut_usuario = $document['mensaje_dec']['header']['usuario'];
		if ($rut_usuario != "Anonimo")
		{
			if (isset($document['mensaje_dec']['mensaje']['rutUsuario']) && 
				$document['mensaje_dec']['mensaje']['rutUsuario'] != "" && 
				$document['mensaje_dec']['mensaje']['rutUsuario'] != NULL)
			{
				$rut_consultar = $document['mensaje_dec']['mensaje']['rutUsuario'];
				if ($this->_usuarios->getUsuarioPorRut($rut_consultar))
				{
					$this->salida['mensaje_dec']['mensaje']['ListaUsuario'] = $this->_usuarios->getUsuarioPorRutDatos($rut_consultar);
				}
				else
				{
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"UsuarioNoExisteErr");
				}
			}
			else
			{
				if ($this->_usuarios->getUsuarioPorRut($rut_usuario)){
					$this->salida['mensaje_dec']['mensaje'] = $this->_usuarios->getUsuarioPorRutDatos($rut_usuario);
				}
				else{
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"UsuarioNoExisteErr");
				}
			}
		}
		else
		{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"UsuarioAnonimoErr");	
		}
	}
	
	private function validaClavePassword($document){
		$rut = $document['mensaje_dec']['mensaje']['usuario'];
		$password =$document['mensaje_dec']['mensaje']['password'];
				
		if($this->_usuarios->validaUsuarioExiste($rut)){
			if ($this->_usuarios->validaUsuarioPassword($rut,$password)){
				if($this->_usuarios->traeEstadoUsuario($rut) != "ACTIVO"){
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EstadoUsuarioErr");
				}
			}
			else{
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"AutenticacionUsuarioErr");
			}
			
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"AutenticacionUsuarioNoExisteErr");			
		}
		if($this->valid)
		{
			$token = array(
				"user" => $rut,
				"exp" => time() + 1800
			);
			$jwt = JWT::encode($token, $this->privateKey, 'RS256');
			$this->salida['mensaje_dec']['header']['token'] = $jwt;
		}
	}
	
	private function validaClaveHuella($document){
		$rut = $document['mensaje_dec']['mensaje']['usuario'];
		$huella =$document['mensaje_dec']['mensaje']['huella'];
		if($this->_usuarios->validaUsuarioExiste($rut)){
			if ($this->_usuarios->validaUsuarioHuella($rut,$huella)){
				if($this->_usuarios->traeEstadoUsuario($rut) != "ACTIVO"){
					$this->valid=false;
					$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"EstadoUsuarioErr");
				}
			}
			else{
				$this->valid=false;
				$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"AutenticacionUsuarioErr");
			}
		}
		else{
			$this->valid=false;
			$this->salida = $this->Mensaje->grabarMensaje( $this->salida,"AutenticacionUsuarioNoExisteErr");			
		}
	}


}

?>