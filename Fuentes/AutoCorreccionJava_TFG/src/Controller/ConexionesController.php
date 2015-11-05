<?php

namespace App\Controller;

require('/../../vendor/ims/blti.php');
use Lib\Ims;

class ConexionesController extends AppController{

	public function index(){
		
		session_start();
		
		$consumer_key = $_REQUEST['oauth_consumer_key'];
		$consumer_key_encriptada = $this->__encriptar($consumer_key);
		
		$query = $this->Conexiones->find('all');
		$query->where(['clave' => $consumer_key_encriptada]);
		
		if(!$query->isEmpty()){		// La consulta tiene éxito
			
			foreach ($query as $q) {
				$secret_encriptada = $q->secreta;
			}
			
			$secret_desencriptada = $this->__desencriptar($secret_encriptada);
			
			$context = new Ims\BLTI($secret_desencriptada, true, false);
			
			// Almacenamiento de la información LTI 
			$_SESSION["lti_tituloActividad"] = $context->info["resource_link_title"];
			$_SESSION["lti_nombreCompleto"] = $context->info["lis_person_name_full"];
			$_SESSION["lti_correo"] = $context->info["lis_person_contact_email_primary"];
			$_SESSION["lti_rol"] = $context->info["roles"];
			$_SESSION["lti_userId"] = $context->info["user_id"];
			$_SESSION["lti_tituloLink"] = $context->info["resource_link_title"];
			$_SESSION["lti_tituloCurso"] = $context->info["context_title"];
			
			// Número de intentos máximo que tienen los alumnos para subir prácticas
			$_SESSION["num_max_intentos"] = 2;
			
			// Rol Alumno
			if($_SESSION['lti_rol'] == "Learner"){
				return $this->redirect(['controller' => 'Alumnos', 'action' => 'registrar']);
			}
			/* ROL PROFESOR
			else{
				redirigir a "ProfesoresController"
			}
			*/

		}else{
			$this->Flash->error(__('la key no existe!!'));
			
			//	-------------------------- LANZAR UN THROW EXCEPTION MEJOR? -------------------------------
		}

	}
	
	private function __encriptar($cadena){
		
		$key='clave_codificacion';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
		$encriptada = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $cadena, MCRYPT_MODE_CBC, md5(md5($key))));
		return $encriptada; //Devuelve el string encriptado
		
	}
	
	private function __desencriptar($cadena){
	
		$key='clave_codificacion';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
		$desencriptada = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($cadena), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
		return $desencriptada;  //Devuelve el string desencriptado
	
	}
	
}

?>