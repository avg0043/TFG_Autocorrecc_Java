<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;

/**
 * Controlador encargado de las tareas, asociadas con los test
 * subidos por el profesor y por las prácticas de los alumnos.
 * 
 * @author Álvaro Vázquez Gómez.
 *
 */
class TareasController extends AppController{
	
	/**
	 * Función asociada a una vista, que se encarga de
	 * guardar en base de datos los datos de la tarea configurada
	 * desde el correspondiente formulario de configuración.
	 * 
	 */
	public function configurarParametrosTarea(){
		
		if(!isset($_SESSION["lti_userId"])){
			return $this->redirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoLocal']);
		}
		$this->comprobarRolProfesor();
		
		$this->set("tarea_actual", $this->Tareas->find('all')->where(['id' => $_SESSION["lti_idTarea"]])->toArray());
		$nueva_tarea = $this->Tareas->newEntity();
		
		if ($this->request->is('post')) {			
			$nueva_tarea = $this->Tareas->patchEntity($nueva_tarea, $this->request->data);
			$nueva_tarea->id = $_SESSION['lti_idTarea'];
			$nueva_tarea->curso_id = $_SESSION['lti_idCurso'];
			$nueva_tarea->nombre = $_SESSION['lti_tituloTarea'];
			date_default_timezone_set("Europe/Madrid");
			$nueva_tarea->fecha_modificacion = new \DateTime(date("Y-m-d H:i:s")); // fecha actual
			$profesores_tabla = TableRegistry::get("Profesores");
			$query = $profesores_tabla->find('all')
    								  ->where(['correo' => $_SESSION['lti_correo']])
    								  ->toArray();
    		$nueva_tarea->profesor_id = $query[0]->id;
    								  
			if ($this->Tareas->save($nueva_tarea)) {
				$this->Flash->success(__('La tarea ha sido configurada correctamente'));
				$tarea_actual = $this->Tareas->find('all')->where(['id' => $_SESSION["lti_idTarea"]]);
				return $this->redirect(['action' => 'configurarParametrosTarea']);
			}
			
			$this->Flash->error(__('No ha sido posible registrar la tarea.'));
			
		}
		$this->set('nueva_tarea', $nueva_tarea);
		
	}
	
}

?>