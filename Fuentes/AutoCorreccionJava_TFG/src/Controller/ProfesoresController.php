<?php

namespace App\Controller;

require('/../../vendor/ims/blti.php');
use Lib\Ims;

class ProfesoresController extends AppController{
	
	/**
	 * Función que registra al profesor en el servicio web.
	 * Al registrarse obtendrá los parámetros LTI necesarios
	 * para poder configurar la tarea de tipo herramienta externa
	 * desde Moodle.
	 */
	public function registrar(){
		
		$nuevo_profesor = $this->Profesores->newEntity();
		
		if ($this->request->is('post')) {				
			$consumer_key = $this->__crearConsumerKey();
			$consumer_key_encriptada = $this->__encriptar($consumer_key);
			$secret_encriptada = $this->__encriptar($this->request->data['contraseña']);
			$nuevo_profesor->consumer_key = $consumer_key_encriptada;
			$nuevo_profesor->secret = $secret_encriptada;		
			$nuevo_profesor = $this->Profesores->patchEntity($nuevo_profesor, $this->request->data);
			
			if ($this->Profesores->save($nuevo_profesor)) {
				$this->Flash->success(__('Has sido registrado'));
				return $this->redirect(['action' => 'mostrarParametros', $this->request->data['correo']]);
			}
			$this->Flash->error(__('No ha sido posible registrar al profesor.'));		
		}
		$this->set('nuevo_profesor', $nuevo_profesor);
		
	}
	
	/**
	 * Función que establece la conexión entre Moodle y el servicio web
	 * gracias al plugin LTI.
	 * Los parámetros LTI son guardados y dependendiendo del rol del usuario
	 * que ha accedido se le redirigirá a su correspondiente página.
	 * 
	 * @throws NotFoundException
	 */
	public function establecerConexion(){
		
		session_start();		
		$consumer_key = $_REQUEST['oauth_consumer_key'];
		
		// Comprobar consumer_key correcto
		if($_REQUEST['roles'] == "Instructor"){			
			$email = $_REQUEST['lis_person_contact_email_primary'];
			$query = $this->Profesores->find('all')
									  ->where(['consumer_key' => $consumer_key, 'correo' => $email])
									  ->toArray();		
		}
		else{		
			$query = $this->Profesores->find('all')
									  ->where(['consumer_key' => $consumer_key])
									  ->toArray();			
		}
		
		if(!empty($query)){			
			// Obtención de la clave secreta
			//foreach ($query as $clave) {
			//	$secret_encriptada = $clave->secret;
			//}
			
			$secret_encriptada = $query[0]->secret;
			
			// Objeto de la conexión LTI
			$context = new Ims\BLTI($secret_encriptada, true, false);
			
			// Almacenamiento de la información LTI 
			$_SESSION['lti_tituloTarea'] = $context->info['resource_link_title'];
			$_SESSION['lti_idTarea'] = $context->info['resource_link_id'];
			$_SESSION['lti_nombreCompleto'] = $context->info['lis_person_name_full'];
			$_SESSION['lti_correo'] = $context->info['lis_person_contact_email_primary'];
			$_SESSION['lti_rol'] = $context->info['roles'];
			$_SESSION['lti_userId'] = $context->info['user_id'];
			$_SESSION['lti_idCurso'] = $context->info['context_id'];
			$_SESSION['lti_nombre'] = $context->info['lis_person_name_given'];
			$_SESSION['lti_apellidos'] = $context->info['lis_person_name_family'];
			
			if($_REQUEST['roles'] == 'Instructor'){			
				$tareas_controller = new TareasController;
				$tarea = $tareas_controller->obtenerTarea($_SESSION['lti_idTarea']);
				
				// Tarea registrada
				if(!empty($tarea)){
					return $this->redirect(['action' => 'mostrarPanel']);
				}
				else{
					return $this->redirect(['controller' => 'Tareas', 'action' => 'configurarParametros']);
				}		
			}
			else{
				return $this->redirect(['controller' => 'Alumnos', 'action' => 'registrar']);	
			}
		}
		else{
			//$this->Flash->error(__('La consumer key no es la correcta!!'));
			throw new NotFoundException();
		}
	}
	
	public function obtenerId($correo){
		
		$query = $this->Profesores->find('all')
								  ->where(['correo' => $correo])
								  ->toArray();
		return $query[0]->id;
		
	}
	
	/**
	 * Función que guarda en una variable los datos del profesor actual para que
	 * puedan ser mostrados desde su vista asociada.
	 */
	public function mostrarDatos(){
		
		session_start();
		
		$this->set('profesor', $this->Profesores->find('all')->where(['correo' => $_SESSION['lti_correo']]));
		
	}
	
	public function mostrarPanel(){
		
	}
	
	/**
	 * Función que guarda en una variable los parámetros LTI del profesor
	 * para que puedan ser mostrados desde su vista asociada.
	 * 
	 * @param string $correo	correo del profesor
	 */
	public function mostrarParametros($correo){
		
		$this->set('parametros', $this->Profesores->find('all')->where(['correo' => $correo]));
		
	}
	
	/**
	 * Función que encripta y devuelve la cadena pasada por parámetro.
	 * 
	 * @param string $cadena	cadena a encriptar.
	 */
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
	
	/**
	 * Función que crea un consumer_key aleatoriamente, que se le va a entregar al
	 * profesor al registrarse.
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