<?php

namespace App\Controller;

class TestsController extends AppController{

	public function guardarTest($id_tarea, $nombre_test){
		
		$nuevo_test = $this->Tests->newEntity();
		$nuevo_test->tarea_id = $id_tarea;
		$nuevo_test->nombre = $nombre_test;
		$nuevo_test->fecha = new \DateTime(date("Y-m-d H:i:s"));
		
		if (!$this->Tests->save($nuevo_test)) {
			$this->Flash->error(__('No ha sido posible registrar el test'));
		}
		
	}
	
}

?>