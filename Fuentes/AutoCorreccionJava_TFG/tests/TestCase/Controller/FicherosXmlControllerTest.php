<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use App\Controller\FicherosXmlController;
use Cake\ORM\TableRegistry;

class FicherosXmlControllerTest extends IntegrationTestCase{
	
	private $profesores_tabla;
	private $alumnos_tabla;
	private $tareas_tabla;
	private $intentos_tabla;
	private $violaciones_tabla;
	private $errores_tabla;
	private $ficherosXml_controller;
	
	public function setUp(){
		
		$this->profesores_tabla = TableRegistry::get("Profesores");
		$this->alumnos_tabla = TableRegistry::get("Alumnos");
		$this->tareas_tabla = TableRegistry::get("Tareas");
		$this->intentos_tabla = TableRegistry::get("Intentos");
		$this->violaciones_tabla = TableRegistry::get('Violaciones');
		$this->errores_tabla = TableRegistry::get("Errores");
		$this->__crearProfesor(1, "Luis", "Izquierdo", "ck1", "s1", "li@ubu.es");
		$this->__crearAlumno(3, 9, "Ivan", "Izquierdo", "ii@alu.ubu.es");
		$this->__crearTarea(18, 9, 1, "practica1", 20, "es.ubu", new \DateTime(date("Y-m-d H:i:s")),
							new \DateTime(date("Y-m-d H:i:s")));
		$this->__crearIntento(1, 18, 3, "practica.zip", 1, 0, "../1/", new \DateTime(date("Y-m-d H:i:s")));
		$this->ficherosXml_controller = new FicherosXmlController();
		
	}
	
	public function tearDown(){
		
		$this->errores_tabla->deleteAll(['1 = 1']);
		$this->violaciones_tabla->deleteAll(['1 = 1']);
		$this->intentos_tabla->deleteAll(['1 = 1']);
		$this->tareas_tabla->deleteAll(['1 = 1']);
		$this->alumnos_tabla->deleteAll(['1 = 1']);
		$this->profesores_tabla->deleteAll(['1 = 1']);
		
	}
	
	public function testEditarPom(){
		
		$this->ficherosXml_controller->editarPomArquetipoMaven("ficheros_test/");
		$this->assertXmlFileEqualsXmlFile("ficheros_test/pom.xml", "ficheros_test/pom_nuevo.xml");
	
	}
	
	public function testGuardarDatosXmlPluginPmd(){
		
		$this->ficherosXml_controller->guardarDatosXmlPluginPmd("ficheros_test/", 1, 1);
		$query = $this->violaciones_tabla->find('all')->where(['intento_id' => 1]);
		
		foreach ($query as $violacion){
			$this->assertEquals(1, $violacion->intento_id);
			$this->assertEquals("Controller.java", $violacion->nombre_fichero);
			$this->assertEquals("UnusedPrivateField", $violacion->tipo);
			$this->assertEquals(3, $violacion->prioridad);
			$this->assertEquals(10, $violacion->linea_inicio);
			$this->assertEquals(10, $violacion->linea_fin);
		}
		
	}
	
	public function testGuardarDatosXmlPluginFindbugs(){
		
		$this->ficherosXml_controller->guardarDatosXmlPluginFindbugs("ficheros_test/", 1, 1);
		$query = $this->violaciones_tabla->find('all')->where(['intento_id' => 1]);
		
		foreach ($query as $violacion){
			$this->assertEquals(1, $violacion->intento_id);
			$this->assertEquals("Controller.java", $violacion->nombre_fichero);
			$this->assertEquals("UUF_UNUSED_FIELD", $violacion->tipo);
			$this->assertEquals(2, $violacion->prioridad);
			$this->assertNull($violacion->linea_inicio);
			$this->assertNull($violacion->linea_fin);
		}
	}
	
	public function testGuardarDatosXmlPluginFindbugsConLineas(){
	
		$this->ficherosXml_controller->guardarDatosXmlPluginFindbugs("ficheros_test/", 1, 2);
		$query = $this->violaciones_tabla->find('all')->where(['intento_id' => 1]);
	
		foreach ($query as $violacion){
			$this->assertEquals(1, $violacion->intento_id);
			$this->assertEquals("Controller.java", $violacion->nombre_fichero);
			$this->assertEquals("UUF_UNUSED_FIELD", $violacion->tipo);
			$this->assertEquals(2, $violacion->prioridad);
			$this->assertEquals(8, $violacion->linea_inicio);
			$this->assertEquals(42, $violacion->linea_fin);
		}
	}
	
	public function testGuardarDatosXmlErrores(){
		
		$this->ficherosXml_controller->guardarDatosXmlErrores("ficheros_test/", 1, 1);
		$query_error_unitario = $this->errores_tabla->find('all')->where(['intento_id' => 1, 'tipo_error' => 'failure']);
		$query_error_excepcion = $this->errores_tabla->find('all')->where(['intento_id' => 1, 'tipo_error' => 'error']);	
		
		foreach ($query_error_unitario as $error_unitario){
			$this->assertEquals(1, $error_unitario->intento_id);
			$this->assertEquals("es.ubu.ControllerTest", $error_unitario->nombre_clase);
			$this->assertEquals("test10Ficheros", $error_unitario->nombre_test);
			$this->assertEquals("failure", $error_unitario->tipo_error);
			$this->assertEquals("junit.framework.AssertionFailedError", $error_unitario->tipo);
		}
		
		foreach ($query_error_excepcion as $error_excepcion){
			$this->assertEquals(1, $error_excepcion->intento_id);
			$this->assertEquals("es.ubu.ControllerTest", $error_excepcion->nombre_clase);
			$this->assertEquals("test10Ficheros", $error_excepcion->nombre_test);
			$this->assertEquals("error", $error_excepcion->tipo_error);
			$this->assertEquals("java.lang.ArrayIndexOutOfBoundsException", $error_excepcion->tipo);
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