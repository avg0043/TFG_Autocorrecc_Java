<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;
use App\Controller\TestsController;

class TestsControllerTest extends IntegrationTestCase{
	
	private $profesores_tabla;
	private $tareas_tabla;
	private $tests_tabla;
	private $tests_controller;
	private $datos;
	
	public function setUp(){
		
		$this->profesores_tabla = TableRegistry::get("Profesores");
		$this->tareas_tabla = TableRegistry::get("Tareas");
		$this->tests_tabla = TableRegistry::get('Tests');
		$this->__crearProfesor(1, "Luis", "Izquierdo", "ck1", "s1", "li@ubu.es");
		$this->__crearTarea(18, 9, 1, "practica1", 20, "es.ubu", new \DateTime(date("Y-m-d H:i:s")), 
							new \DateTime(date("Y-m-d H:i:s")));
		$this->tests_controller = new TestsController();
		$this->datos = [
				'tarea_id' => 18,
				'nombre' => 'test.zip',
				'fecha_subida' => new \DateTime(date("Y-m-d H:i:s"))
		];
		
	}
	
	public function tearDown(){
		
		$this->tests_tabla->deleteAll(['1 = 1']);
		$this->tareas_tabla->deleteAll(['1 = 1']);
		$this->profesores_tabla->deleteAll(['1 = 1']);
		
	}
	
	public function testGuardarTest(){
		
		$this->tests_controller->guardarTest($this->datos["tarea_id"], $this->datos["nombre"]);
		$query = $this->tests_tabla->find()->where(['tarea_id' => $this->datos['tarea_id'], 'nombre' => $this->datos['nombre']]);
		
		$this->assertEquals(1, $query->count());
		foreach ($query as $error){
			$this->assertEquals($this->datos["tarea_id"], $error->tarea_id);
			$this->assertEquals($this->datos["nombre"], $error->nombre);
		}
		
	}
	
	private function __crearProfesor($id, $nombre, $apellidos, $consumer_key, $secret, $correo){
		
		$nuevo_profesor = $this->profesores_tabla->newEntity();
		
		$nuevo_profesor->id = $id;
		$nuevo_profesor->nombre = $nombre;
		$nuevo_profesor->apellidos = $apellidos;
		$nuevo_profesor->consumer_key = $consumer_key;
		$nuevo_profesor->secret = $secret;
		$nuevo_profesor->correo = $correo;
		
		$this->profesores_tabla->save($nuevo_profesor);
		
	}
	
	private function __crearTarea($id, $curso_id, $profesor_id, $nombre, $num_max_intentos, $paquete,
								  $fecha_limite, $fecha_modificacion){
		
		$nueva_tarea = $this->tareas_tabla->newEntity();
		
		$nueva_tarea->id = $id;
		$nueva_tarea->curso_id = $curso_id;
		$nueva_tarea->profesor_id = $profesor_id;
		$nueva_tarea->nombre = $nombre;
		$nueva_tarea->num_max_intentos = $num_max_intentos;
		$nueva_tarea->paquete = $paquete;
		$nueva_tarea->fecha_limite = $fecha_limite;
		$nueva_tarea->fecha_modificacion = $fecha_modificacion;
		
		$this->tareas_tabla->save($nueva_tarea);
		
	}

}