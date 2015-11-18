<?php

namespace App\Controller;

require('/../../vendor/ims/blti.php');
use Lib\Ims;

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
		$profesores_controller = new ProfesoresController();
		
		// Comprobar consumer_key
		if($_REQUEST['roles'] == "Instructor"){
			$correo = $_REQUEST['lis_person_contact_email_primary'];
			$query = $profesores_controller->obtenerProfesorPorKeyCorreo($consumer_key, $correo);
		}
		else{
			$query = $profesores_controller->obtenerProfesorPorKey($consumer_key);
		}
		
		if(!empty($query)){	// consumer_key correcto		
			$secret_encriptada = $query[0]->secret;
			$this->__guardarDatosMoodle($secret_encriptada);
			$this->__redirigirPaginaUsuario();
		}
		else{
			//$this->Flash->error(__('La consumer key no es la correcta!!'));
			throw new NotFoundException();
		}
	}
	
	private function __guardarDatosMoodle($secret_encriptada){
	
		$context = new Ims\BLTI($secret_encriptada, true, false);	// Objeto conexión LTI
			
		$_SESSION['lti_tituloTarea'] = $context->info['resource_link_title'];
		$_SESSION['lti_idTarea'] = $context->info['resource_link_id'];
		$_SESSION['lti_nombreCompleto'] = $context->info['lis_person_name_full'];
		$_SESSION['lti_correo'] = $context->info['lis_person_contact_email_primary'];
		$_SESSION['lti_rol'] = $context->info['roles'];
		$_SESSION['lti_userId'] = $context->info['user_id'];
		$_SESSION['lti_idCurso'] = $context->info['context_id'];
		$_SESSION['lti_nombre'] = $context->info['lis_person_name_given'];
		$_SESSION['lti_apellidos'] = $context->info['lis_person_name_family'];
	
	}
	
	private function __redirigirPaginaUsuario(){
		
		if($_REQUEST['roles'] == 'Instructor'){
			$tareas_controller = new TareasController();
			$tarea = $tareas_controller->obtenerTareaPorId($_SESSION['lti_idTarea']);
		
			if(!empty($tarea)){	// Tarea registrada
				return $this->redirect(['controller' => 'Profesores', 'action' => 'mostrarPanel']);
			}
			else{
				return $this->redirect(['controller' => 'Tareas', 'action' => 'configurarParametrosTarea']);
			}
		}
		else{
			$alumnos_controller = new AlumnosController();
			$query = $alumnos_controller->obtenerAlumnoPorId($_SESSION['lti_userId']);
		
			if(!empty($query)){	// Alumno registrado
				return $this->redirect(['controller' => 'Intentos', 'action' => 'subirPractica']);
			}
			else{
				return $this->redirect(['controller' => 'Alumnos', 'action' => 'registrarAlumno']);
			}
		}
		
	}
	
}

?>