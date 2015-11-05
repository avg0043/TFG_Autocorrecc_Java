<?php

namespace App\Controller;

require('/../../vendor/ims/blti.php');
use Lib\Ims;

class ProfesoresController extends AppController{
	
	public function registrar(){
		
		session_start();
		
		$nuevo_profesor = $this->Profesores->newEntity();
		
		if ($this->request->is('post')) {
			
			// Datos profesor
			$consumer_key = $this->__crearConsumerKey();
			$consumer_key_encriptada = $this->__encriptar($consumer_key);
			$secret_encriptada = $this->__encriptar($this->request->data['contraseña']);
			$nuevo_profesor->oauth_key = $consumer_key_encriptada;
			$nuevo_profesor->secret = $secret_encriptada;
			
			$_SESSION['datosProfesor'] = $nuevo_profesor;
			
			$nuevo_profesor = $this->Profesores->patchEntity($nuevo_profesor, $this->request->data);
			if ($this->Profesores->save($nuevo_profesor)) {
				$this->Flash->success(__('El profesor ha sido registrado.'));
				return $this->redirect(['action' => 'mostrarParametros']);
			}
			$this->Flash->error(__('No ha sido posible registrar al profesor.'));
		}
		$this->set('nuevo_profesor', $nuevo_profesor);
		
	}
	
	public function establecerConexion(){
		
		session_start();
		
		$consumer_key = $_REQUEST['oauth_consumer_key'];
		
		// Comprobación de si el consumer_key es el correcto
		if($_REQUEST['roles'] == "Instructor"){
			
			$email = $_REQUEST['lis_person_contact_email_primary'];
			$query = $this->Profesores->find('all');
			$query->where(['oauth_key' => $consumer_key, 'correo' => $email]);
			
		}else{
			
			$query = $this->Profesores->find('all');
			$query->where(['oauth_key' => $consumer_key]);
			
		}
		
		if(!$query->isEmpty()){		// La consulta tiene éxito
			
			// Obtención de la clave secreta
			foreach ($query as $q) {
				$secret_encriptada = $q->secret;
			}
			
			// Objeto de la conexión LTI
			$context = new Ims\BLTI($secret_encriptada, true, false);
			
			// Almacenamiento de la información LTI 
			$_SESSION["lti_tituloActividad"] = $context->info["resource_link_title"];
			$_SESSION["lti_idTituloActividad"] = $context->info["resource_link_id"];
			$_SESSION["lti_nombreCompleto"] = $context->info["lis_person_name_full"];
			$_SESSION["lti_correo"] = $context->info["lis_person_contact_email_primary"];
			$_SESSION["lti_rol"] = $context->info["roles"];
			$_SESSION["lti_userId"] = $context->info["user_id"];
			//$_SESSION["lti_tituloLink"] = $context->info["resource_link_title"];
			$_SESSION["lti_tituloCurso"] = $context->info["context_title"];
			
			// Número de intentos máximo que tienen los alumnos para subir prácticas
			$_SESSION["num_max_intentos"] = 2;
			
			if($_REQUEST['roles'] == "Instructor"){
			
				return $this->redirect(['action' => 'guardarDatos']);
			
			}else{
				
				return $this->redirect(['controller' => 'Alumnos', 'action' => 'registrar']);
				
			}

		}else{
			
			//$this->Flash->error(__('La consumer key no es la correcta!!'));
			//return $this->redirect(['action' => 'registrar']);
			//	-------------------------- LANZAR UN THROW EXCEPTION MEJOR? -------------------------------
			throw new NotFoundException();
		}

	}
	
	public function guardarDatos(){
	
		session_start();
	
		// Se comprueba si el profesor es la primera vez que accede al servicio web, y si es así, se actualizan sus campos de la BD.
		$query = $this->Profesores->find('all');
		$query->where(['id_moodle' => $_SESSION['lti_userId']]);
		
		if($query->isEmpty()){
			
			$query_actualiza = $this->Profesores->query();
			$query_actualiza->update()
							->set(['id_moodle' => $_SESSION['lti_userId'], 'nombre_completo' => $_SESSION['lti_nombreCompleto']])
							->where(['correo' => $_SESSION['lti_correo']])
							->execute();
			
			$this->Flash->success(__('Profesor registrado'));
			//return $this->redirect(['controller' => 'Tareas', 'action' => 'configurarParametros']);
			
		}
		
		return $this->redirect(['action' => 'mostrarPanel']);
	
	}
	
	public function mostrarDatos(){
		
		session_start();
		
		$this->set('profesor', $this->Profesores->find('all')->where(['id_moodle' => $_SESSION['lti_userId']]));
		
	}
	
	public function mostrarPanel(){
		
	}
	
	
	public function mostrarParametros(){
				
	}
	
	private function __encriptar($cadena){
		
		$key='clave_codificacion';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
		$encriptada = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $cadena, MCRYPT_MODE_CBC, md5(md5($key))));
		return $encriptada; //Devuelve el string encriptado
		
	}
	
	/*
	private function __desencriptar($cadena){
	
		$key='clave_codificacion';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
		$desencriptada = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($cadena), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
		return $desencriptada;  //Devuelve el string desencriptado
	
	}
	*/
	
	private function __crearConsumerKey()
    {

        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;

        for ($i = 0; $i < 7; $i++) {

            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];

        }

        return implode($pass);
    }
	
}

?>