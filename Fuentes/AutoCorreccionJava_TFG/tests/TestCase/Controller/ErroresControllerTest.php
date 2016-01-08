<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use App\Controller\ErroresController;
use Cake\ORM\TableRegistry;

class ErroresControllerTest extends IntegrationTestCase{
	
	private $profesores_tabla;
	private $alumnos_tabla;
	private $tareas_tabla;
	private $intentos_tabla;
	private $errores_tabla;
	
	public function setUp(){
		
		$this->__crearProfesor();
		$this->__crearAlumno();
		$this->__crearTarea();
		$this->__crearIntento();
		
	}
	
	public function tearDown(){
		
		$this->errores_tabla->deleteAll(['1 = 1']);
		$this->intentos_tabla->deleteAll(['1 = 1']);
		$this->tareas_tabla->deleteAll(['1 = 1']);
		$this->alumnos_tabla->deleteAll(['1 = 1']);
		$this->profesores_tabla->deleteAll(['1 = 1']);	
		
	}
	
	public function testGuardarError(){
		
		$errores_controller = new ErroresController();
		$datos = [
				'intento_id' => 1,
				'nombre_clase' => 'Producto.java',
				'nombre_test' => 'testCantidad',
				'tipo_error' => 'failure',
				'tipo' => 'junit.framework.AssertionFailedError',
		];
		
		$errores_controller->guardarError($datos['intento_id'], $datos['nombre_clase'], $datos['nombre_test'], 
										  $datos['tipo_error'], $datos['tipo']);
		$this->errores_tabla = TableRegistry::get('Errores');
		$query = $this->errores_tabla->find()->where(['intento_id' => $datos['intento_id'], 'nombre_clase' => $datos['nombre_clase']]);
		
		$this->assertEquals(1, $query->count());
		foreach ($query as $error){
			$this->assertEquals($datos["intento_id"], $error->intento_id);
			$this->assertEquals($datos["nombre_clase"], $error->nombre_clase);
			$this->assertEquals($datos["nombre_test"], $error->nombre_test);
			$this->assertEquals($datos["tipo_error"], $error->tipo_error);
			$this->assertEquals($datos["tipo"], $error->tipo);
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