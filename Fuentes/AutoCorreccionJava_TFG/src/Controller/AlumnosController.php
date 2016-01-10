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
	public function registrarAlumno(){
		
		session_start();
	
		$nuevo_alumno = $this->Alumnos->newEntity();
		$nuevo_alumno->id = $_SESSION['lti_userId'];
		$nuevo_alumno->curso_id = $_SESSION['lti_idCurso'];
		$nuevo_alumno->nombre = $_SESSION['lti_nombre'];
		$nuevo_alumno->apellidos = $_SESSION['lti_apellidos'];
		$nuevo_alumno->correo = $_SESSION['lti_correo'];
		$this->Alumnos->save($nuevo_alumno);
		
		$this->Flash->success(__('Primer acceso, ha sido registrado'));
		return $this->redirect(['controller' => 'Intentos', 'action' => 'subirPractica']);
		
	}
	
	/**
	 * Función que guarda en una variable todos los alumnos que están
	 * registrados en base de datos, para que puedan ser mostrados
	 * desde la vista asociada.
	 */
	/*
	public function mostrarAlumnos(){
		
		session_start();
		$this->comprobarSesion();
		
		$this->set('alumnos', $this->obtenerAlumnos());
		
	}
	*/
	
	/*
	public function obtenerAlumnos(){
		
		return $this->Alumnos->find('all');
		
	}
	
	public function obtenerAlumnoPorId($id){
		
		return $this->Alumnos->find('all')
							 ->where(['id' => $id])
							 ->toArray();
		
	}
	*/
	
}

?>