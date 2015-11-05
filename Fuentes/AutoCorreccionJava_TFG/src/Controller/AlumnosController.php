<?php

namespace App\Controller;
use Cake\ORM\TableRegistry;

class AlumnosController extends AppController{
	
	/**
	 * Función que comprueba si el alumno que ha accedido al servicio web
	 * es la primera vez que lo hace, en cuyo caso se le registrará en base de datos.
	 * Finalmente redirecciona al formulario de subida de ficheros.
	 *
	 * @return $this->redirect	redirección a la vista del formulario de subida de ficheros.
	 */
	public function registrar(){
		
		session_start();
		
		// Se comprueba si el alumno es la primera vez que accede al servicio web, y si es así, se le registra en BD.
		$query = $this->Alumnos->find('all');
		$query->where(['id' => $_SESSION['lti_userId']]);
		
		if($query->isEmpty()){
			
			$tabla_alumnos = TableRegistry::get('Alumnos');
			$nuevo_alumno = $tabla_alumnos->newEntity();

			$nuevo_alumno->id = $_SESSION['lti_userId'];
			$nuevo_alumno->nombre_completo = $_SESSION['lti_nombreCompleto'];
			$nuevo_alumno->correo = $_SESSION['lti_correo'];

			$tabla_alumnos->save($nuevo_alumno);
			
			$this->Flash->success(__('Este es tu primer acceso. Has sido registrado'));
			
		}
		
		return $this->redirect(['controller' => 'Intentos', 'action' => 'subida', 'alumno']);
		
	}
	
	public function mostrarAlumnos(){
		
		$this->set('alumnos', $this->Alumnos->find('all'));
		
	}
	
}

?>