<?php

namespace App\Controller;

/**
 * Controlador encargado de lo errores.
 * 
 * @author Álvaro Vázquez Gómez.
 *
 */
class ErroresController extends AppController{
	
	/**
	 * Función encargada de guardar el nuevo error
	 * en la correspondiente tabla de la base de datos.
	 * 
	 * @param int $id_intento	id del intento.
	 * @param string $nombre_clase	nombre de la clase.
	 * @param string $nombre_test	nombre del test.
	 * @param string $tipo_error	tipo del error que ha saltado.
	 * @param string $tipo	tipo del error cometido.
	 */
	public function guardarError($id_intento, $nombre_clase, $nombre_test, $tipo_error, $tipo){
	
		$nuevo_error = $this->Errores->newEntity();
		$nuevo_error->intento_id = $id_intento;
		$nuevo_error->nombre_clase = $nombre_clase;
		$nuevo_error->nombre_test = $nombre_test;
		$nuevo_error->tipo_error = $tipo_error;
		$nuevo_error->tipo = $tipo;
		$this->Errores->save($nuevo_error);
		
	}
	
}

?>