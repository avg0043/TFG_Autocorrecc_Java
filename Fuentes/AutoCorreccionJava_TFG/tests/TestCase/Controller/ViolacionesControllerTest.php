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
	private $violaciones_controller;
	private $datos;
	
	public function setUp(){
		
		$this->profesores_tabla = TableRegistry::get("Profesores");
		$this->alumnos_tabla = TableRegistry::get("Alumnos");
		$this->tareas_tabla = TableRegistry::get("Tareas");
		$this->intentos_tabla = TableRegistry::get("Intentos");
		$this->violaciones_tabla = TableRegistry::get('Violaciones');
		$this->__crearProfesor(1, "Luis", "Izquierdo", "ck1", "s1", "li@ubu.es");
		$this->__crearAlumno(3, 9, "Ivan", "Izquierdo", "ii@alu.ubu.es");
		$this->__crearTarea(18, 9, 1, "practica1", 20, "es.ubu", new \DateTime(date("Y-m-d H:i:s")),
							new \DateTime(date("Y-m-d H:i:s")));
		$this->__crearIntento(1, 18, 3, "practica.zip", 1, 0, "../1/", new \DateTime(date("Y-m-d H:i:s")));
		$this->violaciones_controller = new ViolacionesController();
		$this->datos = [
				'intento_id' => 1,
				'nombre_fichero' => 'Operaciones.java',
				'tipo' => 'UnusedPrivateMethod',
				'descripcion' => 'Avoid unused private methods such as foo()',
				'prioridad' => 3,
				'linea_inicio' => 11,
				'linea_fin' => 15
		];
		
	}
	
	public function tearDown(){
		
		$this->violaciones_tabla->deleteAll(['1 = 1']);
		$this->intentos_tabla->deleteAll(['1 = 1']);
		$this->tareas_tabla->deleteAll(['1 = 1']);
		$this->alumnos_tabla->deleteAll(['1 = 1']);
		$this->profesores_tabla->deleteAll(['1 = 1']);
		
	}
	
	public function testGuardarViolacion(){
		
		$this->violaciones_controller->guardarViolacion($this->datos["intento_id"], $this->datos["nombre_fichero"], $this->datos["tipo"],
											 	 		$this->datos["descripcion"], $this->datos["prioridad"], $this->datos["linea_inicio"],
												 	 	$this->datos["linea_fin"]);	
		$query = $this->violaciones_tabla->find()->where(['intento_id' => $this->datos['intento_id'], 'nombre_fichero' => $this->datos['nombre_fichero']]);
		
		$this->assertEquals(1, $query->count());
		foreach ($query as $violacion){
			$this->assertEquals($this->datos["intento_id"], $violacion->intento_id);
			$this->assertEquals($this->datos["nombre_fichero"], $violacion->nombre_fichero);
			$this->assertEquals($this->datos["tipo"], $violacion->tipo);
			$this->assertEquals($this->datos["descripcion"], $violacion->descripcion);
			$this->assertEquals($this->datos["prioridad"], $violacion->prioridad);
			$this->assertEquals($this->datos["linea_inicio"], $violacion->linea_inicio);
			$this->assertEquals($this->datos["linea_fin"], $violacion->linea_fin);
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