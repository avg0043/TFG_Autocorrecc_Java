<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use App\Controller\IntentosController;
use Cake\ORM\TableRegistry;

class IntentosControllerTest extends IntegrationTestCase{
	
	private $profesores_tabla;
	private $alumnos_tabla;
	private $tareas_tabla;
	private $intentos_tabla;
	private $intentos_controller;
	private $datos_intento1;
	
	public function setUp(){
		
		$this->__crearProfesor();
		$this->__crearAlumno();
		$this->__crearTarea();
		$this->intentos_controller = new IntentosController();
		$this->intentos_tabla = TableRegistry::get("Intentos");
		$this->datos_intento1 = [
				'tarea_id' => 18,
				'alumno_id' => 3,
				'nombre' => 'practica.zip',
				'numero_intento' => 1,
				'resultado' => 1,
				'ruta' => '../1/',
				'fecha_intento' => new \DateTime(date("Y-m-d H:i:s"))
		];
		$this->__crearIntento($this->datos_intento1["tarea_id"], $this->datos_intento1["alumno_id"], $this->datos_intento1["nombre"],
							  $this->datos_intento1["numero_intento"], $this->datos_intento1["resultado"], $this->datos_intento1["ruta"],
							  $this->datos_intento1["fecha_intento"]);
		
	}
	
	public function tearDown(){
		
		$this->intentos_tabla->deleteAll(['1 = 1']);
		$this->tareas_tabla->deleteAll(['1 = 1']);
		$this->alumnos_tabla->deleteAll(['1 = 1']);
		$this->profesores_tabla->deleteAll(['1 = 1']);
		
	}
	
	public function testObtenerUltimoIntentoPorIdTareaAlumno(){
		
		$query = $this->intentos_controller->obtenerUltimoIntentoPorIdTareaAlumno($this->datos_intento1["tarea_id"], 
																				  $this->datos_intento1["alumno_id"]);	
		
		$this->assertEquals($this->datos_intento1["tarea_id"], $query["tarea_id"]);
		$this->assertEquals($this->datos_intento1["alumno_id"], $query["alumno_id"]);
		$this->assertEquals($this->datos_intento1["nombre"], $query["nombre"]);
		$this->assertEquals($this->datos_intento1["numero_intento"], $query["numero_intento"]);
		$this->assertEquals($this->datos_intento1["resultado"], $query["resultado"]);
		$this->assertEquals($this->datos_intento1["ruta"], $query["ruta"]);
		
	}
	
	public function testObtenerIntentosPorIdTarea(){
		
		$query = $this->intentos_controller->obtenerIntentosPorIdTarea($this->datos_intento1["tarea_id"]);
		$this->__comprobarDatosCorrectos($query);
		
	}
	
	public function testObtenerIntentosTestPasados(){
		
		$query = $this->intentos_controller->obtenerIntentosTestPasados($this->datos_intento1["tarea_id"], 
																	    $this->datos_intento1["alumno_id"]);
		$this->assertEquals(1, $query->count());
		$this->__comprobarDatosCorrectos($query);
		
	}
	
	public function testObtenerIntentosPorIdTareaAlumno(){
		
		$query = $this->intentos_controller->obtenerIntentosPorIdTareaAlumno($this->datos_intento1["tarea_id"], 
																			 $this->datos_intento1["alumno_id"]);
		$this->__comprobarDatosCorrectos($query);
		
	}
	
	private function __comprobarDatosCorrectos($query){
		
		foreach ($query as $intento){
			$this->assertEquals($this->datos_intento1["tarea_id"], $intento->tarea_id);
			$this->assertEquals($this->datos_intento1["alumno_id"], $intento->alumno_id);
			$this->assertEquals($this->datos_intento1["nombre"], $intento->nombre);
			$this->assertEquals($this->datos_intento1["numero_intento"], $intento->numero_intento);
			$this->assertEquals($this->datos_intento1["resultado"], $intento->resultado);
			$this->assertEquals($this->datos_intento1["ruta"], $intento->ruta);
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
	
	private function __crearAlumno(){
	
		$this->alumnos_tabla = TableRegistry::get("Alumnos");
		$nuevo_alumno = $this->alumnos_tabla->newEntity();
	
		$nuevo_alumno->id = 3;
		$nuevo_alumno->curso_id = 9;
		$nuevo_alumno->nombre = "Ivan";
		$nuevo_alumno->apellidos = "Izquierdo";
		$nuevo_alumno->correo = "ii@alu.ubu.es";
	
		$this->alumnos_tabla->save($nuevo_alumno);
	
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
	private function __crearIntento($tarea_id, $alumno_id, $nombre, $numero_intento, $resultado,
									$ruta, $fecha_intento){
	
		//$this->intentos_tabla = TableRegistry::get("Intentos");
		$nuevo_intento = $this->intentos_tabla->newEntity();
	
		$nuevo_intento->tarea_id = $tarea_id;
		$nuevo_intento->alumno_id = $alumno_id;
		$nuevo_intento->nombre = $nombre;
		$nuevo_intento->numero_intento = $numero_intento;
		$nuevo_intento->resultado = $resultado;
		$nuevo_intento->ruta = $ruta;
		$nuevo_intento->fecha_intento = $fecha_intento;
	
		$this->intentos_tabla->save($nuevo_intento);
	
	}
	
}