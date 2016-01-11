<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use App\Controller\AppController;
use Cake\ORM\TableRegistry;

class AppControllerTest extends IntegrationTestCase{
	
	private $profesores_tabla;
	private $alumnos_tabla;
	private $tareas_tabla;
	private $tests_tabla;
	private $intentos_tabla;
	private $violaciones_tabla;
	private $errores_tabla;
	private $app_controller;
	private $datos_profesor;
	private $datos_alumno1;
	
	public function setUp(){
	
		$this->app_controller = new AppController();
		
		$this->alumnos_tabla = TableRegistry::get("Alumnos");
		$this->profesores_tabla = TableRegistry::get("Profesores");
		$this->tareas_tabla = TableRegistry::get("Tareas");
		$this->tests_tabla = TableRegistry::get("Tests");
		$this->intentos_tabla = TableRegistry::get("Intentos");
		$this->violaciones_tabla = TableRegistry::get("Violaciones");
		$this->errores_tabla = TableRegistry::get("Errores");
	
	}
	
	public function tearDown(){
	
		$this->errores_tabla->deleteAll(['1 = 1']);
		$this->violaciones_tabla->deleteAll(['1 = 1']);
		$this->intentos_tabla->deleteAll(['1 = 1']);
		$this->tests_tabla->deleteAll(['1 = 1']);
		$this->tareas_tabla->deleteAll(['1 = 1']);
		$this->profesores_tabla->deleteAll(['1 = 1']);
		$this->alumnos_tabla->deleteAll(['1 = 1']);
	
	}
	
	public function testObtenerProfesorPorKeyCorreo(){
	
		$datos_profesor = [
				'nombre' => 'Luis',
				'apellidos' => 'Izquierdo',
				'consumer_key' => 'ck1',
				'secret' => 's1',
				'correo' => 'li@ubu.es'
		];
		$this->__crearProfesor($datos_profesor["nombre"], $datos_profesor["apellidos"], $datos_profesor["consumer_key"],
							   $datos_profesor["secret"], $datos_profesor["correo"]);
		$query = $this->app_controller->obtenerProfesorPorKeyCorreo($datos_profesor["consumer_key"],
																	$datos_profesor["correo"]);
			
		foreach ($query as $profesor){
			$this->assertEquals($datos_profesor["nombre"], $profesor->nombre);
			$this->assertEquals($datos_profesor["apellidos"], $profesor->apellidos);
			$this->assertEquals($datos_profesor["consumer_key"], $profesor->consumer_key);
			$this->assertEquals($datos_profesor["secret"], $profesor->secret);
			$this->assertEquals($datos_profesor["correo"], $profesor->correo);
		}
		//$this->__comprobarDatosProfesorCorrectos($query);
	
	}
	
	public function testObtenerProfesorPorKey(){
	
		$datos_profesor = [
				'nombre' => 'Luis',
				'apellidos' => 'Izquierdo',
				'consumer_key' => 'ck1',
				'secret' => 's1',
				'correo' => 'li@ubu.es'
		];
		$this->__crearProfesor($datos_profesor["nombre"], $datos_profesor["apellidos"], $datos_profesor["consumer_key"],
				$datos_profesor["secret"], $datos_profesor["correo"]);
		$query = $this->app_controller->obtenerProfesorPorKey($datos_profesor["consumer_key"]);
		
		foreach ($query as $profesor){
			$this->assertEquals($datos_profesor["nombre"], $profesor->nombre);
			$this->assertEquals($datos_profesor["apellidos"], $profesor->apellidos);
			$this->assertEquals($datos_profesor["consumer_key"], $profesor->consumer_key);
			$this->assertEquals($datos_profesor["secret"], $profesor->secret);
			$this->assertEquals($datos_profesor["correo"], $profesor->correo);
		}
		//$this->__comprobarDatosProfesorCorrectos($query);
	
	}
	
	public function testObtenerProfesorPorCorreo(){
	
		$datos_profesor = [
				'nombre' => 'Luis',
				'apellidos' => 'Izquierdo',
				'consumer_key' => 'ck1',
				'secret' => 's1',
				'correo' => 'li@ubu.es'
		];
		$this->__crearProfesor($datos_profesor["nombre"], $datos_profesor["apellidos"], $datos_profesor["consumer_key"],
				$datos_profesor["secret"], $datos_profesor["correo"]);
		$query = $this->app_controller->obtenerProfesorPorCorreo($datos_profesor["correo"]);
		
		foreach ($query as $profesor){
			$this->assertEquals($datos_profesor["nombre"], $profesor->nombre);
			$this->assertEquals($datos_profesor["apellidos"], $profesor->apellidos);
			$this->assertEquals($datos_profesor["consumer_key"], $profesor->consumer_key);
			$this->assertEquals($datos_profesor["secret"], $profesor->secret);
			$this->assertEquals($datos_profesor["correo"], $profesor->correo);
		}
		//$this->__comprobarDatosProfesorCorrectos($query);
	
	}
	
	public function testObtenerAlumnoPorId(){
		
		$datos_alumno = [
				'id' => 3,
				'curso_id' => 9,
				'nombre' => 'Ivan',
				'apellidos' => 'Izquierdo',
				'correo' => 'ii@alu.ubu.es'
		];	
		$this->__crearAlumno($datos_alumno["id"], $datos_alumno["curso_id"], $datos_alumno["nombre"],
							 $datos_alumno["apellidos"], $datos_alumno["correo"]);
		$query = $this->app_controller->obtenerAlumnoPorId($datos_alumno["id"]);
		
		foreach ($query as $alumno){
			$this->assertEquals($datos_alumno["id"], $alumno->id);
			$this->assertEquals($datos_alumno["curso_id"], $alumno->curso_id);
			$this->assertEquals($datos_alumno["nombre"], $alumno->nombre);
			$this->assertEquals($datos_alumno["apellidos"], $alumno->apellidos);
			$this->assertEquals($datos_alumno["correo"], $alumno->correo);
		}
	
	}
	
	public function testObtenerAlumnos(){
	
		$datos_alumno1 = [
				'id' => 3,
				'curso_id' => 9,
				'nombre' => 'Ivan',
				'apellidos' => 'Izquierdo',
				'correo' => 'ii@alu.ubu.es'
		];
		$datos_alumno2 = [
				'id' => 5,
				'curso_id' => 9,
				'nombre' => 'Luis',
				'apellidos' => 'Vázquez',
				'correo' => 'lv@alu.ubu.es'
		];
		$this->__crearAlumno($datos_alumno1["id"], $datos_alumno1["curso_id"], $datos_alumno1["nombre"],
							 $datos_alumno1["apellidos"], $datos_alumno1["correo"]);
		$this->__crearAlumno($datos_alumno2["id"], $datos_alumno2["curso_id"], $datos_alumno2["nombre"],
							 $datos_alumno2["apellidos"], $datos_alumno2["correo"]);
		$query = $this->app_controller->obtenerAlumnos();
	
		$numero_alumno = 1;
		$this->assertEquals(2, $query->count());
		foreach ($query as $alumno){
			if($numero_alumno == 1){
				$this->assertEquals($datos_alumno1["id"], $alumno->id);
				$this->assertEquals($datos_alumno1["curso_id"], $alumno->curso_id);
				$this->assertEquals($datos_alumno1["nombre"], $alumno->nombre);
				$this->assertEquals($datos_alumno1["apellidos"], $alumno->apellidos);
				$this->assertEquals($datos_alumno1["correo"], $alumno->correo);
			}
			else{
				$this->assertEquals($datos_alumno2["id"], $alumno->id);
				$this->assertEquals($datos_alumno2["curso_id"], $alumno->curso_id);
				$this->assertEquals($datos_alumno2["nombre"], $alumno->nombre);
				$this->assertEquals($datos_alumno2["apellidos"], $alumno->apellidos);
				$this->assertEquals($datos_alumno2["correo"], $alumno->correo);
			}
			$numero_alumno++;
		}
	
	
	}
	
	public function testObtenerTareaPorId(){
	
		$this->__crearProfesor("Luis", "Izquierdo", "ck1", "s1", "li@ubu.es", 1);
		$datos_tarea = [
				'id' => 18,
				'curso_id' => 9,
				'profesor_id' => 1,
				'nombre' => 'practica1',
				'num_max_intentos' => 20,
				'paquete' => 'es.ubu',
				'fecha_limite' => new \DateTime(date("Y-m-d H:i:s")),
				'fecha_modificacion' => new \DateTime(date("Y-m-d H:i:s"))
		];
		$this->__crearTarea($datos_tarea["id"], $datos_tarea["curso_id"], $datos_tarea["profesor_id"],
							$datos_tarea["nombre"], $datos_tarea["num_max_intentos"], $datos_tarea["paquete"],
							$datos_tarea["fecha_limite"], $datos_tarea["fecha_modificacion"]);
		
		$query = $this->app_controller->obtenerTareaPorId($datos_tarea["id"]);
	
		foreach ($query as $tarea){
			$this->assertEquals($datos_tarea["id"], $tarea->id);
			$this->assertEquals($datos_tarea["curso_id"], $tarea->curso_id);
			$this->assertEquals($datos_tarea["profesor_id"], $tarea->profesor_id);
			$this->assertEquals($datos_tarea["nombre"], $tarea->nombre);
			$this->assertEquals($datos_tarea["num_max_intentos"], $tarea->num_max_intentos);
			$this->assertEquals($datos_tarea["paquete"], $tarea->paquete);
			$this->assertEquals($datos_tarea["fecha_limite"], $tarea->fecha_limite);
			$this->assertEquals($datos_tarea["fecha_modificacion"], $tarea->fecha_modificacion);
		}
	
	}
	
	public function testObtenerTestPorIdTarea(){
		
		$this->__crearProfesor("Luis", "Izquierdo", "ck1", "s1", "li@ubu.es", 1);
		$this->__crearTarea(18, 9, 1, "practica1", 20, "es.ubu", new \DateTime(date("Y-m-d H:i:s")), 
							new \DateTime(date("Y-m-d H:i:s")));
		$datos_test = [
				'tarea_id' => 18,
				'nombre' => 'test.zip',
				'fecha_subida' => new \DateTime(date("Y-m-d H:i:s"))
		];
		$this->__crearTest($datos_test["tarea_id"], $datos_test["nombre"], $datos_test["fecha_subida"]);
		$query = $this->app_controller->obtenerTestPorIdTarea($datos_test["tarea_id"]);
	
		foreach ($query as $test){
			$this->assertEquals($datos_test["tarea_id"], $test->tarea_id);
			$this->assertEquals($datos_test["nombre"], $test->nombre);
			$this->assertEquals($datos_test["fecha_subida"], $test->fecha_subida);
		}
	
	}
	
	public function testObtenerUltimoIntentoPorIdTareaAlumno(){
		
		$this->__crearProfesor("Luis", "Izquierdo", "ck1", "s1", "li@ubu.es", 1);
		$this->__crearAlumno(3, 9, "Ivan", "Izquierdo", "ii@alu.ubu.es");
		$this->__crearTarea(18, 9, 1, "practica1", 20, "es.ubu", new \DateTime(date("Y-m-d H:i:s")),
							new \DateTime(date("Y-m-d H:i:s")));
		$datos_intento = [
				'tarea_id' => 18,
				'alumno_id' => 3,
				'nombre' => 'practica.zip',
				'numero_intento' => 1,
				'resultado' => 1,
				'ruta' => '../1/',
				'fecha_intento' => new \DateTime(date("Y-m-d H:i:s"))
		];
		$this->__crearIntento($datos_intento["tarea_id"], $datos_intento["alumno_id"], $datos_intento["nombre"], 
							  $datos_intento["numero_intento"], $datos_intento["resultado"], $datos_intento["ruta"], 
							  $datos_intento["fecha_intento"]);
		$query = $this->app_controller->obtenerUltimoIntentoPorIdTareaAlumno($datos_intento["tarea_id"],
																			 $datos_intento["alumno_id"]);
	
		$this->assertEquals($datos_intento["tarea_id"], $query["tarea_id"]);
		$this->assertEquals($datos_intento["alumno_id"], $query["alumno_id"]);
		$this->assertEquals($datos_intento["nombre"], $query["nombre"]);
		$this->assertEquals($datos_intento["numero_intento"], $query["numero_intento"]);
		$this->assertEquals($datos_intento["resultado"], $query["resultado"]);
		$this->assertEquals($datos_intento["ruta"], $query["ruta"]);
		$this->assertEquals($datos_intento["fecha_intento"], $query["fecha_intento"]);
		
	}
	
	public function testObtenerIntentosPorIdTarea(){
	
		$this->__crearProfesor("Luis", "Izquierdo", "ck1", "s1", "li@ubu.es", 1);
		$this->__crearAlumno(3, 9, "Ivan", "Izquierdo", "ii@alu.ubu.es");
		$this->__crearTarea(18, 9, 1, "practica1", 20, "es.ubu", new \DateTime(date("Y-m-d H:i:s")),
							new \DateTime(date("Y-m-d H:i:s")));
		$datos_intento = [
				'tarea_id' => 18,
				'alumno_id' => 3,
				'nombre' => 'practica.zip',
				'numero_intento' => 1,
				'resultado' => 1,
				'ruta' => '../1/',
				'fecha_intento' => new \DateTime(date("Y-m-d H:i:s"))
		];
		$this->__crearIntento($datos_intento["tarea_id"], $datos_intento["alumno_id"], $datos_intento["nombre"],
							  $datos_intento["numero_intento"], $datos_intento["resultado"], $datos_intento["ruta"],
							  $datos_intento["fecha_intento"]);
		
		$query = $this->app_controller->obtenerIntentosPorIdTarea($datos_intento["tarea_id"]);
		
		foreach ($query as $intento){
			$this->assertEquals($datos_intento["tarea_id"], $intento->tarea_id);
			$this->assertEquals($datos_intento["alumno_id"], $intento->alumno_id);
			$this->assertEquals($datos_intento["nombre"], $intento->nombre);
			$this->assertEquals($datos_intento["numero_intento"], $intento->numero_intento);
			$this->assertEquals($datos_intento["resultado"], $intento->resultado);
			$this->assertEquals($datos_intento["ruta"], $intento->ruta);
			$this->assertEquals($datos_intento["fecha_intento"], $intento->fecha_intento);
		}
	
	}
	
	public function testObtenerIntentosTestPasados(){
	
		$this->__crearProfesor("Luis", "Izquierdo", "ck1", "s1", "li@ubu.es", 1);
		$this->__crearAlumno(3, 9, "Ivan", "Izquierdo", "ii@alu.ubu.es");
		$this->__crearTarea(18, 9, 1, "practica1", 20, "es.ubu", new \DateTime(date("Y-m-d H:i:s")),
							new \DateTime(date("Y-m-d H:i:s")));
		$datos_intento = [
				'tarea_id' => 18,
				'alumno_id' => 3,
				'nombre' => 'practica.zip',
				'numero_intento' => 1,
				'resultado' => 1,
				'ruta' => '../1/',
				'fecha_intento' => new \DateTime(date("Y-m-d H:i:s"))
		];
		$this->__crearIntento($datos_intento["tarea_id"], $datos_intento["alumno_id"], $datos_intento["nombre"],
							  $datos_intento["numero_intento"], $datos_intento["resultado"], $datos_intento["ruta"],
							  $datos_intento["fecha_intento"]);
		
		$query = $this->app_controller->obtenerIntentosTestPasados($datos_intento["tarea_id"],
																   $datos_intento["alumno_id"]);
		$this->assertEquals(1, $query->count());
		foreach ($query as $intento){
			$this->assertEquals($datos_intento["tarea_id"], $intento->tarea_id);
			$this->assertEquals($datos_intento["alumno_id"], $intento->alumno_id);
			$this->assertEquals($datos_intento["nombre"], $intento->nombre);
			$this->assertEquals($datos_intento["numero_intento"], $intento->numero_intento);
			$this->assertEquals($datos_intento["resultado"], $intento->resultado);
			$this->assertEquals($datos_intento["ruta"], $intento->ruta);
			$this->assertEquals($datos_intento["fecha_intento"], $intento->fecha_intento);
		}
	
	}
	
	public function testObtenerIntentosPorIdTareaAlumno(){
	
		$this->__crearProfesor("Luis", "Izquierdo", "ck1", "s1", "li@ubu.es", 1);
		$this->__crearAlumno(3, 9, "Ivan", "Izquierdo", "ii@alu.ubu.es");
		$this->__crearTarea(18, 9, 1, "practica1", 20, "es.ubu", new \DateTime(date("Y-m-d H:i:s")),
							new \DateTime(date("Y-m-d H:i:s")));
		$datos_intento = [
				'tarea_id' => 18,
				'alumno_id' => 3,
				'nombre' => 'practica.zip',
				'numero_intento' => 1,
				'resultado' => 1,
				'ruta' => '../1/',
				'fecha_intento' => new \DateTime(date("Y-m-d H:i:s"))
		];
		$this->__crearIntento($datos_intento["tarea_id"], $datos_intento["alumno_id"], $datos_intento["nombre"],
							  $datos_intento["numero_intento"], $datos_intento["resultado"], $datos_intento["ruta"],
							  $datos_intento["fecha_intento"]);
		
		$query = $this->app_controller->obtenerIntentosPorIdTareaAlumno($datos_intento["tarea_id"],
																		$datos_intento["alumno_id"]);
		
		foreach ($query as $intento){
			$this->assertEquals($datos_intento["tarea_id"], $intento->tarea_id);
			$this->assertEquals($datos_intento["alumno_id"], $intento->alumno_id);
			$this->assertEquals($datos_intento["nombre"], $intento->nombre);
			$this->assertEquals($datos_intento["numero_intento"], $intento->numero_intento);
			$this->assertEquals($datos_intento["resultado"], $intento->resultado);
			$this->assertEquals($datos_intento["ruta"], $intento->ruta);
			$this->assertEquals($datos_intento["fecha_intento"], $intento->fecha_intento);
		}
	
	}
	
	public function testObtenerViolacionPorIntentoTipo(){
	
		$this->__crearProfesor("Luis", "Izquierdo", "ck1", "s1", "li@ubu.es", 1);
		$this->__crearAlumno(3, 9, "Ivan", "Izquierdo", "ii@alu.ubu.es");
		$this->__crearTarea(18, 9, 1, "practica1", 20, "es.ubu", new \DateTime(date("Y-m-d H:i:s")),
							new \DateTime(date("Y-m-d H:i:s")));
		$this->__crearIntento(18, 3, "practica.zip", 1, 1, "../1/", new \DateTime(date("Y-m-d H:i:s")), 1);
		$datos_violacion = [
				'intento_id' => 1,
				'nombre_fichero' => 'Operaciones.java',
				'tipo' => 'UnusedPrivateMethod',
				'descripcion' => 'Avoid unused private methods such as foo()',
				'prioridad' => 3,
				'linea_inicio' => 11,
				'linea_fin' => 15
		];
		$this->__crearViolacion($datos_violacion["intento_id"], $datos_violacion["nombre_fichero"], 
								$datos_violacion["tipo"], $datos_violacion["descripcion"], 
								$datos_violacion["prioridad"], $datos_violacion["linea_inicio"], 
								$datos_violacion["linea_fin"]);
		$query = $this->app_controller->obtenerViolacionPorIntentoTipo($datos_violacion["intento_id"], $datos_violacion["tipo"]);
	
		foreach ($query as $violacion){
			$this->assertEquals($datos_violacion["intento_id"], $violacion->intento_id);
			$this->assertEquals($datos_violacion["nombre_fichero"], $violacion->nombre_fichero);
			$this->assertEquals($datos_violacion["tipo"], $violacion->tipo);
			$this->assertEquals($datos_violacion["descripcion"], $violacion->descripcion);
			$this->assertEquals($datos_violacion["prioridad"], $violacion->prioridad);
			$this->assertEquals($datos_violacion["linea_inicio"], $violacion->linea_inicio);
			$this->assertEquals($datos_violacion["linea_fin"], $violacion->linea_fin);
		}
	
	}
	
	public function testObtenerViolacionesPorIdIntento(){
	
		$this->__crearProfesor("Luis", "Izquierdo", "ck1", "s1", "li@ubu.es", 1);
		$this->__crearAlumno(3, 9, "Ivan", "Izquierdo", "ii@alu.ubu.es");
		$this->__crearTarea(18, 9, 1, "practica1", 20, "es.ubu", new \DateTime(date("Y-m-d H:i:s")),
							new \DateTime(date("Y-m-d H:i:s")));
		$this->__crearIntento(18, 3, "practica.zip", 1, 1, "../1/", new \DateTime(date("Y-m-d H:i:s")), 1);
		$datos_violacion = [
				'intento_id' => 1,
				'nombre_fichero' => 'Operaciones.java',
				'tipo' => 'UnusedPrivateMethod',
				'descripcion' => 'Avoid unused private methods such as foo()',
				'prioridad' => 3,
				'linea_inicio' => 11,
				'linea_fin' => 15
		];
		$this->__crearViolacion($datos_violacion["intento_id"], $datos_violacion["nombre_fichero"],
								$datos_violacion["tipo"], $datos_violacion["descripcion"],
								$datos_violacion["prioridad"], $datos_violacion["linea_inicio"],
								$datos_violacion["linea_fin"]);
		$query = $this->app_controller->obtenerViolacionesPorIdIntento($datos_violacion["intento_id"]);
	
		foreach ($query as $violacion){
			$this->assertEquals($datos_violacion["intento_id"], $violacion->intento_id);
			$this->assertEquals($datos_violacion["nombre_fichero"], $violacion->nombre_fichero);
			$this->assertEquals($datos_violacion["tipo"], $violacion->tipo);
			$this->assertEquals($datos_violacion["descripcion"], $violacion->descripcion);
			$this->assertEquals($datos_violacion["prioridad"], $violacion->prioridad);
			$this->assertEquals($datos_violacion["linea_inicio"], $violacion->linea_inicio);
			$this->assertEquals($datos_violacion["linea_fin"], $violacion->linea_fin);
		}
	
	}
	
	public function testObtenerErroresPorIdIntento(){
	
		$this->__crearProfesor("Luis", "Izquierdo", "ck1", "s1", "li@ubu.es", 1);
		$this->__crearAlumno(3, 9, "Ivan", "Izquierdo", "ii@alu.ubu.es");
		$this->__crearTarea(18, 9, 1, "practica1", 20, "es.ubu", new \DateTime(date("Y-m-d H:i:s")),
							new \DateTime(date("Y-m-d H:i:s")));
		$this->__crearIntento(18, 3, "practica.zip", 1, 1, "../1/", new \DateTime(date("Y-m-d H:i:s")), 1);
		$datos_error = [
				'intento_id' => 1,
				'nombre_clase' => 'Producto.java',
				'nombre_test' => 'testCantidad',
				'tipo_error' => 'failure',
				'tipo' => 'junit.framework.AssertionFailedError',
		];
		$this->__crearError($datos_error["intento_id"], $datos_error["nombre_clase"], $datos_error["nombre_test"], 
							$datos_error["tipo_error"], $datos_error["tipo"]);
		$query = $this->app_controller->obtenerErroresPorIdIntento($datos_error["intento_id"]);
	
		foreach ($query as $error){
			$this->assertEquals($datos_error["intento_id"], $error->intento_id);
			$this->assertEquals($datos_error["nombre_clase"], $error->nombre_clase);
			$this->assertEquals($datos_error["nombre_test"], $error->nombre_test);
			$this->assertEquals($datos_error["tipo_error"], $error->tipo_error);
			$this->assertEquals($datos_error["tipo"], $error->tipo);
		}
	
	}
	
	public function testObtenerIntentosConViolacionesVacio(){
	
		$this->__crearProfesor("Luis", "Izquierdo", "ck1", "s1", "li@ubu.es", 1);
		$this->__crearAlumno(3, 9, "Ivan", "Izquierdo", "ii@alu.ubu.es");
		$this->__crearTarea(18, 9, 1, "practica1", 20, "es.ubu", new \DateTime(date("Y-m-d H:i:s")),
							new \DateTime(date("Y-m-d H:i:s")));
		$this->__crearIntento(18, 3, "practica.zip", 1, 1, "../1/", new \DateTime(date("Y-m-d H:i:s")), 1);
	
		@session_start();
		$_SESSION['lti_userId'] = 3;
		$query = $this->app_controller->obtenerIntentosConViolaciones();
	
		foreach ($query as $intento){
			$this->assertEquals(0, count($intento->violaciones));
		}
	
	}
	
	public function testObtenerIntentosConErroresVacio(){
		
		$this->__crearProfesor("Luis", "Izquierdo", "ck1", "s1", "li@ubu.es", 1);
		$this->__crearAlumno(3, 9, "Ivan", "Izquierdo", "ii@alu.ubu.es");
		$this->__crearTarea(18, 9, 1, "practica1", 20, "es.ubu", new \DateTime(date("Y-m-d H:i:s")),
							new \DateTime(date("Y-m-d H:i:s")));
		$this->__crearIntento(18, 3, "practica.zip", 1, 1, "../1/", new \DateTime(date("Y-m-d H:i:s")), 1);
		
		@session_start();
		$_SESSION['lti_userId'] = 3;
		$query = $this->app_controller->obtenerIntentosConErrores();
		
		foreach ($query as $intento){
			$this->assertEquals(0, count($intento->errores));
		}
		
	}
	
	private function __crearProfesor($nombre, $apellidos, $consumer_key, $secret, $correo, $id = null){
	
		$nuevo_profesor = $this->profesores_tabla->newEntity();
	
		if($id != null){
			$nuevo_profesor->id = $id;
		}
		$nuevo_profesor->nombre = "Luis";
		$nuevo_profesor->apellidos = "Izquierdo";
		$nuevo_profesor->consumer_key = "ck1";
		$nuevo_profesor->secret = "s1";
		$nuevo_profesor->correo = "li@ubu.es";
	
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
	
	private function __crearTest($tarea_id, $nombre, $fecha_subida){
		
		$nuevo_test = $this->tests_tabla->newEntity();
		
		$nuevo_test->tarea_id = $tarea_id;
		$nuevo_test->nombre = $nombre;
		$nuevo_test->fecha_subida = $fecha_subida;
		
		$this->tests_tabla->save($nuevo_test);
		
	}
	
	private function __crearIntento($tarea_id, $alumno_id, $nombre, $numero_intento, $resultado,
									$ruta, $fecha_intento, $id = null){
	
		$nuevo_intento = $this->intentos_tabla->newEntity();

		if($id != null){
			$nuevo_intento->id = $id;
		}
		$nuevo_intento->tarea_id = $tarea_id;
		$nuevo_intento->alumno_id = $alumno_id;
		$nuevo_intento->nombre = $nombre;
		$nuevo_intento->numero_intento = $numero_intento;
		$nuevo_intento->resultado = $resultado;
		$nuevo_intento->ruta = $ruta;
		$nuevo_intento->fecha_intento = $fecha_intento;

		$this->intentos_tabla->save($nuevo_intento);
	
	}
	
	private function __crearViolacion($intento_id, $nombre_fichero, $tipo, $descripcion, $prioridad,
									  $linea_inicio, $linea_fin){
		
		$nueva_violacion = $this->violaciones_tabla->newEntity();
		
		$nueva_violacion->intento_id = $intento_id;
		$nueva_violacion->nombre_fichero = $nombre_fichero;
		$nueva_violacion->tipo = $tipo;
		$nueva_violacion->descripcion = $descripcion;
		$nueva_violacion->prioridad = $prioridad;
		$nueva_violacion->linea_inicio = $linea_inicio;
		$nueva_violacion->linea_fin = $linea_fin;
		
		$this->violaciones_tabla->save($nueva_violacion);
									  	
	}
	
	private function __crearError($intento_id, $nombre_clase, $nombre_test, $tipo_error, $tipo){
		
		$nuevo_error = $this->errores_tabla->newEntity();
		
		$nuevo_error->intento_id = $intento_id;
		$nuevo_error->nombre_clase = $nombre_clase;
		$nuevo_error->nombre_test = $nombre_test;
		$nuevo_error->tipo_error = $tipo_error;
		$nuevo_error->tipo = $tipo;

		$this->errores_tabla->save($nuevo_error);
		
	}
	
	/*
	private function __comprobarDatosProfesorCorrectos($query){
	
		foreach ($query as $profesor){
			$this->assertEquals($this->datos_profesor["nombre"], $profesor->nombre);
			$this->assertEquals($this->datos_profesor["apellidos"], $profesor->apellidos);
			$this->assertEquals($this->datos_profesor["consumer_key"], $profesor->consumer_key);
			$this->assertEquals($this->datos_profesor["secret"], $profesor->secret);
			$this->assertEquals($this->datos_profesor["correo"], $profesor->correo);
		}
	
	}
	*/
	
}