<?php

namespace App\Controller;

class TareasController extends AppController{

	/**
	 * Función que configura los parámetros que van a estar
	 * asociados a la tarea.
	 */
	public function configurarParametros(){
		
		session_start();
		
		$nueva_tarea = $this->Tareas->newEntity();
		
		if ($this->request->is('post')) {
			
			$nueva_tarea = $this->Tareas->patchEntity($nueva_tarea, $this->request->data);
			$nueva_tarea->id = $_SESSION['lti_idTarea'];
			$nueva_tarea->nombre = $_SESSION['lti_tituloTarea'];
			
			if ($this->Tareas->save($nueva_tarea)) {
				$this->Flash->success(__('La tarea ha sido configurada.'));
				return $this->redirect(['controller' => 'Profesores', 'action' => 'mostrarPanel']);
			}
			$this->Flash->error(__('No ha sido posible registrar la tarea.'));
			
		}
		$this->set('nueva_tarea', $nueva_tarea);
		
	}
	
	/**
	 * Función que comprueba si el id de la tarea recibida pertenece
	 * a una tarea registrada o no.
	 * 
	 * @param integer $id	id de la tarea.
	 * @return boolean	si la tarea está registrada o no.
	 */
	public function comprobarTareaRegistrada($id){
		
		$query = $this->Tareas->find('all')
					  ->where(['id' => $id]);
		
		if(!$query->isEmpty()){
			return true;
		}else{
			return false;
		}
					  
	}
	
	/**
	 * Función que obtiene el número de intentos máximo de la
	 * tarea recibida por id.
	 * 
	 * @param integer $id	id de la tarea.
	 * @return integer	número máximo de intentos de la tarea.
	 */
	public function obtenerIntentosMaximo($id){
		
		$query = $this->Tareas->find('all')
							  ->where(['id' => $id])
							  ->toArray();
		return $query[0]->num_max_intentos;
			
	}
	
	/**
	 * Función que obtiene la fecha límite de entrega
	 * de la tarea recibida por id.
	 * 
	 * @param integer $id	id de la tarea.
	 * @return date	fecha límite de la entrega de la tarea.
	 */
	public function obtenerFechaLimite($id){
	
		$query = $this->Tareas->find('all')
							  ->where(['id' => $id])
							  ->toArray();
		return $query[0]->fecha_limite;
			
	}
	
}

?>