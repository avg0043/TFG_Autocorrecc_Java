<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;
use App\Controller\ViolacionesController;

class ViolacionesControllerTest extends IntegrationTestCase{
	
	private $profesores_tabla;
	private $alumnos_tabla;
	private $tareas_tabla;
	private $intentos_tabla;
	private $violaciones_tabla;
	
	public function setUp(){
		
		$this->__crearProfesor();
		$this->__crearAlumno();
		$this->__crearTarea();
		$this->__crearIntento();
		
	}
	
	public function tearDown(){
		
		$this->violaciones_tabla->deleteAll(['1 = 1']);
		$this->intentos_tabla->deleteAll(['1 = 1']);
		$this->tareas_tabla->deleteAll(['1 = 1']);
		$this->alumnos_tabla->deleteAll(['1 = 1']);
		$this->profesores_tabla->deleteAll(['1 = 1']);
		
	}
	
	public function testGuardarViolacion(){
		
		$violaciones_controller = new ViolacionesController();
		$datos = [
				'intento_id' => 1,
				'nombre_fichero' => 'Operaciones.java',
				'tipo' => 'UnusedPrivateMethod',
				'descripcion' => 'Avoid unused private methods such as foo()',
				'prioridad' => 3,
				'linea_inicio' => 11,
				'linea_fin' => 15
		];
		
		$violaciones_controller->guardarViolacion($datos["intento_id"], $datos["nombre_fichero"], $datos["tipo"],
											 	  $datos["descripcion"], $datos["prioridad"], $datos["linea_inicio"],
												  $datos["linea_fin"]);	
		$this->violaciones_tabla = TableRegistry::get('Violaciones');
		$query = $this->violaciones_tabla->find()->where(['intento_id' => $datos['intento_id'], 'nombre_fichero' => $datos['nombre_fichero']]);
		
		$this->assertEquals(1, $query->count());
		foreach ($query as $violacion){
			$this->assertEquals($datos["intento_id"], $violacion->intento_id);
			$this->assertEquals($datos["nombre_fichero"], $violacion->nombre_fichero);
			$this->assertEquals($datos["tipo"], $violacion->tipo);
			$this->assertEquals($datos["descripcion"], $violacion->descripcion);
			$this->assertEquals($datos["prioridad"], $violacion->prioridad);
			$this->assertEquals($datos["linea_inicio"], $violacion->linea_inicio);
			$this->assertEquals($datos["linea_fin"], $violacion->linea_fin);
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
	
	private function __crearIntento(){
		
		$this->intentos_tabla = TableRegistry::get("Intentos");
		$nuevo_intento = $this->intentos_tabla->newEntity();
		
		$nuevo_intento->id = 1;
		$nuevo_intento->tarea_id = 18;
		$nuevo_intento->alumno_id = 3;
		$nuevo_intento->nombre = "practica.zip";
		$nuevo_intento->numero_intento = 1;
		$nuevo_intento->resultado = 0;
		$nuevo_intento->ruta = "../1/";
		$nuevo_intento->fecha_intento = new \DateTime(date("Y-m-d H:i:s"));
		
		$this->intentos_tabla->save($nuevo_intento);
		
	}

}