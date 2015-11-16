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
			$nueva_tarea->curso_id = $_SESSION['lti_idCurso'];
			$nueva_tarea->nombre = $_SESSION['lti_tituloTarea'];
			
			// Fecha actual
			date_default_timezone_set("Europe/Madrid");
			$nueva_tarea->fecha_modificacion = new \DateTime(date("Y-m-d H:i:s"));
			
			// Obtención del id del profesor
			$profesores_controller = new ProfesoresController;		
			$nueva_tarea->profesor_id = $profesores_controller->obtenerProfesorPorCorreo($_SESSION['lti_correo'])[0]->id;
			
			if ($this->Tareas->save($nueva_tarea)) {
				$this->Flash->success(__('La tarea ha sido configurada.'));
				return $this->redirect(['controller' => 'Profesores', 'action' => 'mostrarPanel']);
			}
			$this->Flash->error(__('No ha sido posible registrar la tarea.'));
			
		}
		$this->set('nueva_tarea', $nueva_tarea);
		
	}
	
	public function obtenerTareaPorId($id){
		
		return $this->Tareas->find('all')
							->where(['id' => $id])
							->toArray();
		
	}
	
}

?>