<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use App\Controller\TareasController;
use Cake\ORM\TableRegistry;

class TareasControllerTest extends IntegrationTestCase{
	
	private $profesores_tabla;
	private $tareas_tabla;
	private $tareas_controller;
	private $datos;
	
	public function setUp(){
		
		$this->__crearProfesor();
		$this->tareas_controller = new TareasController();
		$this->tareas_tabla = TableRegistry::get("Tareas");
		$this->datos = [
				'id' => 18,
				'curso_id' => 9,
				'profesor_id' => 1,
				'nombre' => 'practica1',
				'num_max_intentos' => 20,
				'paquete' => 'es.ubu',
				'fecha_limite' => new \DateTime(date("Y-m-d H:i:s")),
				'fecha_modificacion' => new \DateTime(date("Y-m-d H:i:s"))
		];
		$this->__crearTarea($this->datos["id"], $this->datos["curso_id"], $this->datos["profesor_id"], 
							$this->datos["nombre"], $this->datos["num_max_intentos"], $this->datos["paquete"], 
							$this->datos["fecha_limite"], $this->datos["fecha_modificacion"]);
	}
	
	public function tearDown(){
		
		$this->tareas_tabla->deleteAll(['1 = 1']);
		$this->profesores_tabla->deleteAll(['1 = 1']);
		
	}
	
	public function testObtenerTareaPorId(){
		
		$query = $this->tareas_controller->obtenerTareaPorId($this->datos["id"]);
		
		foreach ($query as $tarea){
			$this->assertEquals($this->datos["id"], $tarea->id);
			$this->assertEquals($this->datos["curso_id"], $tarea->curso_id);
			$this->assertEquals($this->datos["profesor_id"], $tarea->profesor_id);
			$this->assertEquals($this->datos["nombre"], $tarea->nombre);
			$this->assertEquals($this->datos["num_max_intentos"], $tarea->num_max_intentos);
			$this->assertEquals($this->datos["paquete"], $tarea->paquete);	
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
	private function __crearTarea($id, $curso_id, $profesor_id, $nombre, $num_max_intentos, $paquete,
								  $fecha_limite, $fecha_modificacion){
	
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