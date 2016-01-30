<?php

namespace App\Controller;

/**
 * Controlador encargado de las violaciones de código.
 * 
 * @author Álvaro Vázquez Gómez.
 *
 */
class ViolacionesController extends AppController{
	
	/**
	 * Función encargada de guardar en base de datos la violación de código.
	 * 
	 * @param int $id_intento	id del intento de subida de práctica realizada por el alumno.
	 * @param string $fichero_nombre	nombre del fichero que contiene la violación.
	 * @param string $tipo_violacion	tipo de la violación de código.
	 * @param string $descripcion_violacion	descripción de la violación.	
	 * @param string $prioridad	prioridad de la violación.
	 * @param string $inicio_linea	inicio de la línea en la que se encuentra la violación.
	 * @param string $fin_linea	fin de la línea en la que se encuentra la violación.
	 */
	public function guardarViolacion($id_intento, $fichero_nombre, $tipo_violacion, $descripcion_violacion, 
									 $prioridad, $inicio_linea = null, $fin_linea = null){
	
		$nueva_violacion = $this->Violaciones->newEntity();
		$nueva_violacion->intento_id = $id_intento;
		$nueva_violacion->nombre_fichero = $fichero_nombre;
		$nueva_violacion->tipo = $tipo_violacion;
		$nueva_violacion->descripcion = $descripcion_violacion;
		$nueva_violacion->prioridad = (int) $prioridad;
		if($inicio_linea != null && $fin_linea != null){
			$nueva_violacion->linea_inicio = (int) $inicio_linea;
			$nueva_violacion->linea_fin = (int) $fin_linea;
		}
		$this->Violaciones->save($nueva_violacion);
		
	}
	
}

?>