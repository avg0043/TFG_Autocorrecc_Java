<?php

namespace App\Controller;

/**
 * Controlador encargado de los Alumnos.
 * 
 * @author Álvaro Vázquez Gómez
 *
 */
class AlumnosController extends AppController{
	
	/**
	 * Functión encargada de registrar al alumno en su primer
	 * acceso en la aplicación. Tras el registro, redirecciona
	 * a la vista del formulario de subida de prácticas.
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
		
		$this->Flash->success(__('Primer acceso, has sido registrado'));
		return $this->redirect(['controller' => 'Intentos', 'action' => 'subirPractica']);
		
	}
	
}

?>