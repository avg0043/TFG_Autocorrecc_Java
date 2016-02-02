<?php

namespace App\Controller;

require('/../../vendor/ims/blti.php');
use Lib\Ims;
use Cake\ORM\TableRegistry;

/**
 * Controlador encargado de la conexión entre
 * Moodle y la aplicación.
 * 
 * @author Álvaro Vázquez Gómez
 *
 */
class ConexionesController extends AppController{
	
	/**
	 * Función encargada de establecer la conexión entre Moodle
	 * y la aplicación. Para ello valida que el consumer_key
	 * sea el correcto.
	 * 
	 */
	public function establecerConexion(){
		
		session_start();		
		$consumer_key = $_REQUEST['oauth_consumer_key'];
		$profesores_tabla = TableRegistry::get("Profesores");
		
		// Comprobar consumer_key
		if(strpos($_REQUEST['roles'], 'Learner') !== false){
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
	
	/**
	 * Función privada encargada de crear el objeto LTI que
	 * contiene los datos de Moodle. Al crear dicho objeto
	 * se le pasa el secret. 
	 * 
	 * @param string $secret_encriptada secret encriptado.
	 */
	private function __guardarDatosMoodle($secret_encriptada){
	
		$context = new Ims\BLTI($secret_encriptada, true, false);	// Objeto conexión LTI
			
		$_SESSION['lti_tituloTarea'] = $context->info['resource_link_title'];
		$_SESSION['lti_idTarea'] = $context->info['resource_link_id'];
		$_SESSION['lti_nombreCompleto'] = $context->info['lis_person_name_full'];
		$_SESSION['lti_correo'] = $context->info['lis_person_contact_email_primary'];
		
		if(strpos($context->info['roles'], 'Learner') !== false){
			$_SESSION['lti_rol'] = "Learner";
		}else{
			$_SESSION['lti_rol'] = "Instructor";
		}
		
		$_SESSION['lti_userId'] = $context->info['user_id'];
		$_SESSION['lti_idCurso'] = $context->info['context_id'];
		$_SESSION['lti_nombre'] = $context->info['lis_person_name_given'];
		$_SESSION['lti_apellidos'] = $context->info['lis_person_name_family'];
	
	}
	
	/**
	 * Función privada encargada de redirigir a la correspondiente
	 * vista en función del rol del usuario que ha accedido a la 
	 * aplicación.
	 * 
	 */
	private function __redirigirPaginaUsuario(){	
		
		if(strpos($_REQUEST['roles'], 'Learner') !== false){
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