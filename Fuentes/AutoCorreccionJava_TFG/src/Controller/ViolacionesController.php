<?php

namespace App\Controller;

class ViolacionesController extends AppController{
	
	public function guardarViolacion($id_intento, $fichero_nombre, $tipo_violacion, $descripcion_violacion, 
									 $inicio_linea, $fin_linea){
	
		$nueva_violacion = $this->Violaciones->newEntity();
		$nueva_violacion->intento_id = $id_intento;
		$nueva_violacion->nombre_fichero = $fichero_nombre;
		$nueva_violacion->tipo = $tipo_violacion;
		$nueva_violacion->descripcion = $descripcion_violacion;
		$nueva_violacion->linea_inicio = (int) $inicio_linea;
		$nueva_violacion->linea_fin = (int) $fin_linea;
		$this->Violaciones->save($nueva_violacion);
		
	}
	
}

?>