<?php

namespace App\Controller;

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
		
		$query = $this->Alumnos->find('all')
							   ->where(['id' => $_SESSION['lti_userId']]);
		
		if($query->isEmpty()){
			
			$nuevo_alumno = $this->Alumnos->newEntity();

			$nuevo_alumno->id = $_SESSION['lti_userId'];
			$nuevo_alumno->nombre_completo = $_SESSION['lti_nombreCompleto'];
			$nuevo_alumno->correo = $_SESSION['lti_correo'];

			$this->Alumnos->save($nuevo_alumno);
			
			$this->Flash->success(__('Este es tu primer acceso. Has sido registrado'));
			
		}
		
		return $this->redirect(['controller' => 'Intentos', 'action' => 'subida', 'alumno']);
		
	}
	
	/**
	 * Función que guarda en una variable todos los alumnos que están
	 * registrados en base de datos, para que puedan ser mostrados
	 * desde la vista asociada.
	 */
	public function mostrarAlumnos(){
		
		$this->set('alumnos', $this->Alumnos->find('all'));
		
	}
	
}

?>