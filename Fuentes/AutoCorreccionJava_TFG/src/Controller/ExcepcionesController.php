<?php

namespace App\Controller;

/**
 * Controlador encargado de las excepciones.
 * 
 * @author Álvaro Vázquez Gómez.
 *
 */
class ExcepcionesController extends AppController{
	
	/**
	 * Función asociada a una vista encargada de mostrar
	 * el error que se corresponde por un acceso local
	 * inválido a la aplicación.
	 * 
	 */
	public function mostrarErrorAccesoLocal(){
		$this->set("hora_actual", date("Y-m-d H:i:s"));
	}
	
	/**
	 * Función asociada a una vista encargada de mostrar
	 * el error cometido por un consumer key incorrecto.
	 * 
	 * @param string $consumer_key	consumer key incorrecto.
	 */
	public function mostrarErrorConsumerKey($consumer_key){
		$this->set("consumer_key", $consumer_key);
	}
	
	/**
	 * Función asociada a una vista encargada de mostrar
	 * el error que se corresponde con un acceso de un usuario
	 * al panel del alumno, sin ser el usuario un alumno.
	 * 
	 */
	public function mostrarErrorAccesoIncorrectoAlumno(){
		$this->set("hora_actual", date("Y-m-d H:i:s"));
	}
	
	/**
	 * Función asociada a una vista encargada de mostrar
	 * el error que se corresponde con un acceso de un usuario
	 * al panel del profeosr, sin ser el usuario un profesor.
	 * 
	 */
	public function mostrarErrorAccesoIncorrectoProfesor(){
		$this->set("hora_actual", date("Y-m-d H:i:s"));
	}
	
}

?>