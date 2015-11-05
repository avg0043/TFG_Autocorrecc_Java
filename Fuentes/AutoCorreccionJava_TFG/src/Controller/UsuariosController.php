<?php

namespace App\Controller;

use Cake\DataSource\ConnectionManager;

class UsuariosController extends AppController{

	public function index(){
		$clave_encriptada = $this->__encriptar('key');
		$this->set('encriptada', $clave_encriptada);
		//$connection = ConnectionManager::get('default');
		//$results = $connection->execute('SELECT * FROM tfg_lti_claves')->fetchAll('assoc');
		//print_r($results);
		//echo $results[0];
		$conexion = mysqli_connect("localhost", "root", "", "autocorrecc_tfg") 
								   or die("Problemas con la conexión");
		$valores = mysqli_query($conexion, "select * from tfg_lti_claves") or
							die("Problemas en el select:".mysqli_error($conexion));
							
		if($val = mysqli_fetch_array($valores)){
			$secret_encriptada = $val["secret"];
			echo "EPA: " . $secret_encriptada;
		}
	}
	
	private function __encriptar($cadena){
		
		$key='clave_codificacion';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
		$encriptada = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $cadena, MCRYPT_MODE_CBC, md5(md5($key))));
		return $encriptada; //Devuelve el string encriptado
		
	}

	public function info(){
		session_start();
		$_SESSION['lti_rol'] = $_REQUEST['roles'];
		$clave = $_REQUEST['oauth_consumer_key'];
		$this->set('key', $clave);
	}
	
}

?>