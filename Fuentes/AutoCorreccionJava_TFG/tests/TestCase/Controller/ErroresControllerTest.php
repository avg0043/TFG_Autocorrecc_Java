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
	private $errores_controller;
	private $datos;
	
	public function setUp(){
		
		$this->profesores_tabla = TableRegistry::get("Profesores");
		$this->alumnos_tabla = TableRegistry::get("Alumnos");
		$this->tareas_tabla = TableRegistry::get("Tareas");
		$this->intentos_tabla = TableRegistry::get("Intentos");
		$this->errores_tabla = TableRegistry::get('Errores');
		$this->__crearProfesor(1, "Luis", "Izquierdo", "ck1", "s1", "li@ubu.es");
		$this->__crearAlumno(3, 9, "Ivan", "Izquierdo", "ii@alu.ubu.es");
		$this->__crearTarea(18, 9, 1, "practica1", 20, "es.ubu", new \DateTime(date("Y-m-d H:i:s")), 
							new \DateTime(date("Y-m-d H:i:s")));
		$this->__crearIntento(1, 18, 3, "practica.zip", 1, 0, "../1/", new \DateTime(date("Y-m-d H:i:s")));
		$this->errores_controller = new ErroresController();
		$this->datos = [
				'intento_id' => 1,
				'nombre_clase' => 'Producto.java',
				'nombre_test' => 'testCantidad',
				'tipo_error' => 'failure',
				'tipo' => 'junit.framework.AssertionFailedError',
		];
		
	}
	
	public function tearDown(){
		
		$this->errores_tabla->deleteAll(['1 = 1']);
		$this->intentos_tabla->deleteAll(['1 = 1']);
		$this->tareas_tabla->deleteAll(['1 = 1']);
		$this->alumnos_tabla->deleteAll(['1 = 1']);
		$this->profesores_tabla->deleteAll(['1 = 1']);	
		
	}
	
	public function testGuardarError(){
		
		$this->errores_controller->guardarError($this->datos['intento_id'], $this->datos['nombre_clase'], $this->datos['nombre_test'], 
										  		$this->datos['tipo_error'], $this->datos['tipo']);
		$query = $this->errores_tabla->find()->where(['intento_id' => $this->datos['intento_id'], 'nombre_clase' => $this->datos['nombre_clase']]);
		
		$this->assertEquals(1, $query->count());
		foreach ($query as $error){
			$this->assertEquals($this->datos["intento_id"], $error->intento_id);
			$this->assertEquals($this->datos["nombre_clase"], $error->nombre_clase);
			$this->assertEquals($this->datos["nombre_test"], $error->nombre_test);
			$this->assertEquals($this->datos["tipo_error"], $error->tipo_error);
			$this->assertEquals($this->datos["tipo"], $error->tipo);
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
	
	private function __crearAlumno($id, $curso_id, $nombre, $apellidos, $correo){
		
		$nuevo_alumno = $this->alumnos_tabla->newEntity();
		
		$nuevo_alumno->id = $id;
		$nuevo_alumno->curso_id = $curso_id;
		$nuevo_alumno->nombre = $nombre;
		$nuevo_alumno->apellidos = $apellidos;
		$nuevo_alumno->correo = $correo;
		
		$this->alumnos_tabla->save($nuevo_alumno);
		
	}
	
	private function __crearTarea($id, $curso_id, $profesor_id, $nombre, $num_max_intentos,
								  $paquete, $fecha_limite, $fecha_modificacion){
		
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
	
	private function __crearIntento($id, $tarea_id, $alumno_id, $nombre, $numero_intento,
									$resultado, $ruta, $fecha_intento){
		
		$nuevo_intento = $this->intentos_tabla->newEntity();
		
		$nuevo_intento->id = $id;
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