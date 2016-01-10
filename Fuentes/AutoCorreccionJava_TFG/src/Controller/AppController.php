<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

	private $profesores_tabla;
	private $alumnos_tabla;
	private $tareas_tabla;
	private $tests_tabla;
	private $intentos_tabla;
	private $violaciones_tabla;
	private $errores_tabla;
	
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        
    	$this->profesores_tabla = TableRegistry::get("Profesores");
    	$this->alumnos_tabla = TableRegistry::get("Alumnos");
    	$this->tareas_tabla = TableRegistry::get("Tareas");
    	$this->tests_tabla = TableRegistry::get("Tests");
    	$this->intentos_tabla = TableRegistry::get("Intentos");
    	$this->violaciones_tabla = TableRegistry::get("Violaciones");
    	$this->errores_tabla = TableRegistry::get("Errores");
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }
    
    /**
     * Comprueba si la sesión está vacía, mirando el id del usuario.
     * Garantiza que únicamente se pueda acceder a la aplicación
     * desde las tareas de Moodle, y nunca desde local.
     * 
     * @throws NotFoundException excepción.
     */
    public function comprobarSesion(){
    
    	if(!isset($_SESSION["lti_userId"])){
    		//throw new NotFoundException();
    		return $this->redirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoLocal']);
    	}
    
    }
    
    public function obtenerProfesorPorKeyCorreo($consumer_key, $correo){
    
    	return $this->profesores_tabla->find('all')
								      ->where(['consumer_key' => $consumer_key, 'correo' => $correo])
								      ->toArray();
    
    }
    
    public function obtenerProfesorPorKey($consumer_key){
    
    	return $this->profesores_tabla->find('all')
								      ->where(['consumer_key' => $consumer_key])
								      ->toArray();
    
    }
    
    public function obtenerProfesorPorCorreo($correo){
    
    	return $this->profesores_tabla->find('all')
    								  ->where(['correo' => $correo])
    								  ->toArray();
    
    }
    
    public function obtenerAlumnos(){
    
    	return $this->alumnos_tabla->find('all');
    
    }
    
    public function obtenerAlumnoPorId($id){
    
    	return $this->alumnos_tabla->find('all')
							       ->where(['id' => $id])
							       ->toArray();
    
    }
    
    public function obtenerErroresPorIdIntento($id_intento){
    
    	return $this->errores_tabla->find('all')
							       ->where(['intento_id' => $id_intento])
							       ->toArray();
    
    }
    
    public function obtenerUltimoIntentoPorIdTareaAlumno($id_tarea, $id_alumno){
    
    	return $this->intentos_tabla->find('all')
							    	->where(['tarea_id' => $id_tarea, 'alumno_id' => $id_alumno])
							    	->last()
							    	->toArray();
    
    }
    
    public function obtenerIntentosPorIdTarea($id_tarea){
    
    	return $this->intentos_tabla->find('all')
    								->where(['tarea_id' => $id_tarea]);
    
    }
    
    public function obtenerIntentosTestPasados($id_tarea, $id_alumno){
    
    	return $this->intentos_tabla->find('all')
    								->where(['tarea_id' => $id_tarea, 'alumno_id' => $id_alumno, 'resultado' => 1]);
    
    }
    
    public function obtenerIntentosPorIdTareaAlumno($id_tarea, $id_alumno){
    
    	return $this->intentos_tabla->find('all')
    								->where(['tarea_id' => $id_tarea, 'alumno_id' => $id_alumno]);
    
    }
    
    public function obtenerIntentosConViolaciones(){
    
    	return $this->intentos_tabla->find('all')
							    	->contain(['Violaciones'])
							    	->where(['alumno_id' => $_SESSION["lti_userId"]]);
    
    }
    
    public function obtenerIntentosConErrores(){
    
    	return $this->intentos_tabla->find('all')
							    	->contain(['Errores'])
							    	->where(['alumno_id' => $_SESSION["lti_userId"]]);
    
    }
    
    public function obtenerTareaPorId($id){
    
    	return $this->tareas_tabla->find('all')
							      ->where(['id' => $id])
							      ->toArray();
    
    }
    
    public function obtenerTestPorIdTarea($id_tarea){
    
    	return $this->tests_tabla->find('all')
						    	 ->where(['tarea_id' => $id_tarea])
						    	 ->toArray();
    
    }
    
    public function obtenerViolacionPorIntentoTipo($id_intento, $tipo_violacion){
    
    	return $this->violaciones_tabla->find('all')
								       ->where(['intento_id' => $id_intento, 'tipo' => $tipo_violacion])
								       ->toArray();
    
    }
    
    public function obtenerViolacionesPorIdIntento($id_intento){
    
    	return $this->violaciones_tabla->find('all')
								       ->where(['intento_id' => $id_intento])
								       ->toArray();
    
    }
    
}
