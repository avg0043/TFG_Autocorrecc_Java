<?php

namespace App\Controller;

class ErroresController extends AppController{
	
	public function guardarError($id_intento, $nombre_clase, $nombre_test, $tipo_error, $tipo, $traza_error){
	
		$nuevo_error = $this->Errores->newEntity();
		$nuevo_error->intento_id = $id_intento;
		$nuevo_error->nombre_clase = $nombre_clase;
		$nuevo_error->nombre_test = $nombre_test;
		$nuevo_error->tipo_error = $tipo_error;
		$nuevo_error->tipo = $tipo;
		$nuevo_error->traza = $traza_error;
		$this->Errores->save($nuevo_error);
		
	}
	
	public function obtenerErroresPorIdIntento($id_intento){
	
		return $this->Errores->find('all')
							 ->where(['intento_id' => $id_intento])
							 ->toArray();
	
	}
	
}

?>