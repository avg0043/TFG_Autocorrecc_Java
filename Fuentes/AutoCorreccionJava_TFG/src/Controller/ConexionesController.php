<?php

namespace App\Controller;

require('/../../vendor/ims/blti.php');
use Lib\Ims;
use Cake\ORM\TableRegistry;

class ConexionesController extends AppController{
	
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
		$profesores_tabla = TableRegistry::get("Profesores");
		
		// Comprobar consumer_key
		/*
		if($_REQUEST['roles'] == "Instructor"){
			$correo = $_REQUEST['lis_person_contact_email_primary'];
			//$query = $this->obtenerProfesorPorKeyCorreo($consumer_key, $correo);
			$query = $profesores_tabla->find('all')
								      ->where(['consumer_key' => $consumer_key, 'correo' => $correo])
								      ->toArray();
		}
		else{
			//$query = $this->obtenerProfesorPorKey($consumer_key);
			$query = $profesores_tabla->find('all')
								      ->where(['consumer_key' => $consumer_key])
								      ->toArray();
		}
		*/
		
		// Comprobar consumer_key
		if($_REQUEST['roles'] == "Learner"){
			$query = $profesores_tabla->find('all')
									  ->where(['consumer_key' => $consumer_key])
									  ->toArray();
		}
		else{
			$correo = $_REQUEST['lis_person_contact_email_primary'];
			$query = $profesores_tabla->find('all')
									  ->where(['consumer_key' => $consumer_key, 'correo' => $correo])
									  ->toArray();
		}
		
		if(!empty($query)){	// consumer_key correcto		
			$secret_encriptada = $query[0]->secret;
			$this->__guardarDatosMoodle($secret_encriptada);
			$this->__redirigirPaginaUsuario();
		}
		else{
			return $this->redirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorConsumerKey', $consumer_key]);
		}
	}
	
	private function __guardarDatosMoodle($secret_encriptada){
	
		$context = new Ims\BLTI($secret_encriptada, true, false);	// Objeto conexión LTI
			
		$_SESSION['lti_tituloTarea'] = $context->info['resource_link_title'];
		$_SESSION['lti_idTarea'] = $context->info['resource_link_id'];
		$_SESSION['lti_nombreCompleto'] = $context->info['lis_person_name_full'];
		$_SESSION['lti_correo'] = $context->info['lis_person_contact_email_primary'];
		
		if($context->info['roles'] == "Learner"){
			$_SESSION['lti_rol'] = $context->info['roles'];
		}else{
			$_SESSION['lti_rol'] = "Instructor";
		}
		
		$_SESSION['lti_userId'] = $context->info['user_id'];
		$_SESSION['lti_idCurso'] = $context->info['context_id'];
		$_SESSION['lti_nombre'] = $context->info['lis_person_name_given'];
		$_SESSION['lti_apellidos'] = $context->info['lis_person_name_family'];
	
	}
	
	private function __redirigirPaginaUsuario(){
		
		/*
		if($_REQUEST['roles'] == 'Instructor'){				
			//$tarea = $this->obtenerTareaPorId($_SESSION['lti_idTarea']);
			$tareas_tabla = TableRegistry::get("Tareas");
			$tarea = $tareas_tabla->find('all')
							      ->where(['id' => $_SESSION['lti_idTarea']])
							      ->toArray();
		
			if(!empty($tarea)){	// Tarea registrada
				return $this->redirect(['controller' => 'Profesores', 'action' => 'mostrarPanel']);
			}
			else{
				return $this->redirect(['controller' => 'Tareas', 'action' => 'configurarParametrosTarea']);
			}
		}
		else{
			//$query = $this->obtenerAlumnoPorId($_SESSION['lti_userId']);
			$alumnos_tabla = TableRegistry::get("Alumnos");
			$query = $alumnos_tabla->find('all')
							       ->where(['id' => $_SESSION['lti_userId']])
							       ->toArray();
			
			if(!empty($query)){	// Alumno registrado
				return $this->redirect(['controller' => 'Intentos', 'action' => 'subirPractica']);
			}
			else{
				return $this->redirect(['controller' => 'Alumnos', 'action' => 'registrarAlumno']);
			}
		}
		*/
		
		
		if($_REQUEST['roles'] == 'Learner'){
			$alumnos_tabla = TableRegistry::get("Alumnos");
			$query = $alumnos_tabla->find('all')
								   ->where(['id' => $_SESSION['lti_userId']])
								   ->toArray();
				
			if(!empty($query)){	// Alumno registrado
				return $this->redirect(['controller' => 'Intentos', 'action' => 'subirPractica']);
			}
			else{
				return $this->redirect(['controller' => 'Alumnos', 'action' => 'registrarAlumno']);
			}
		}
		else{
			$tareas_tabla = TableRegistry::get("Tareas");
			$tarea = $tareas_tabla->find('all')
								  ->where(['id' => $_SESSION['lti_idTarea']])
								  ->toArray();
			
			if(!empty($tarea)){	// Tarea registrada
				return $this->redirect(['controller' => 'Profesores', 'action' => 'mostrarPanel']);
			}
			else{
				return $this->redirect(['controller' => 'Tareas', 'action' => 'configurarParametrosTarea']);
			}
		}
		
	}
	
}

?>