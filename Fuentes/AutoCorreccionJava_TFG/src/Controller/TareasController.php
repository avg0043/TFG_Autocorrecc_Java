<?php

namespace App\Controller;

class TareasController extends AppController{

	public function configurarParametros(){
		
		session_start();
		
		$nueva_tarea = $this->Tareas->newEntity();
		
		if ($this->request->is('post')) {
			
			$nueva_tarea = $this->Tareas->patchEntity($nueva_tarea, $this->request->data);
			$nueva_tarea->id = $_SESSION['lti_idTituloActividad'];
			$nueva_tarea->nombre = $_SESSION['lti_tituloActividad'];
			
			if ($this->Tareas->save($nueva_tarea)) {
				$this->Flash->success(__('La tarea ha sido configurada.'));
				return $this->redirect(['controller' => 'Profesores', 'action' => 'mostrarPanel']);
			}
			$this->Flash->error(__('No ha sido posible registrar la tarea.'));
			
		}
		$this->set('nueva_tarea', $nueva_tarea);
		
	}
	
	public function obtenerIntentosPorId($id){
		
		// HACER ESTO PARA LOS INTENTOS
		$query = $this->Tareas->find('all')
							  ->where(['id' => $id])
							  ->toArray();
		//echo count($query);
		return $query[0]->num_intentos;
			
	}
	
}

?>