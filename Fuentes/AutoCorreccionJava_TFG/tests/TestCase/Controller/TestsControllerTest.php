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
		
		$this->__crearProfesor();
		$this->__crearTarea();
		$this->tests_controller = new TestsController();
		$this->tests_tabla = TableRegistry::get('Tests');
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
	
	private function __crearProfesor(){
		
		$this->profesores_tabla = TableRegistry::get("Profesores");
		$nuevo_profesor = $this->profesores_tabla->newEntity();
		
		$nuevo_profesor->id = 1;
		$nuevo_profesor->nombre = "Luis";
		$nuevo_profesor->apellidos = "Izquierdo";
		$nuevo_profesor->consumer_key = "ck1";
		$nuevo_profesor->secret = "s1";
		$nuevo_profesor->correo = "li@ubu.es";
		
		$this->profesores_tabla->save($nuevo_profesor);
		
	}
	
	private function __crearTarea(){
		
		$this->tareas_tabla = TableRegistry::get("Tareas");
		$nueva_tarea = $this->tareas_tabla->newEntity();
		
		$nueva_tarea->id = 18;
		$nueva_tarea->curso_id = 9;
		$nueva_tarea->profesor_id = 1;
		$nueva_tarea->nombre = "practica1";
		$nueva_tarea->num_max_intentos = 20;
		$nueva_tarea->paquete = "es.ubu";
		$nueva_tarea->fecha_limite = new \DateTime(date("Y-m-d H:i:s"));
		$nueva_tarea->fecha_modificacion = new \DateTime(date("Y-m-d H:i:s"));
		
		$this->tareas_tabla->save($nueva_tarea);
		
	}

}