<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;

class TareasControllerTest extends IntegrationTestCase{
	
	private $profesores_tabla;
	private $tareas_tabla;
	
	public function setUp(){
		
		error_reporting(0);
		@session_start();
		$this->profesores_tabla = TableRegistry::get("Profesores");
		$this->tareas_tabla = TableRegistry::get("Tareas");
	
	}
	
	public function tearDown(){
	
		$this->tareas_tabla->deleteAll(['1 = 1']);
		$this->profesores_tabla->deleteAll(['1 = 1']);
	
	}
	
	public function testConfigurarParametrosTarea(){
		
		$datos = [
				'num_max_intentos' => 20,
				'paquete' => "es.ubu",
				'fecha_limite' => new \DateTime(date("Y-m-d H:i:s"))
		];
		$_SESSION['lti_userId'] = 1;
		$_SESSION['lti_idTarea'] = 18;
		$_SESSION['lti_idCurso'] = 9;
		$_SESSION['lti_rol'] = "Instructor";
		$_SESSION['lti_tituloTarea'] = "practica1";
		$_SESSION['lti_correo'] = "li@ubu.es";
		
		$this->__crearProfesor(1, "Luis", "Izquierdo", "ck1", "s1", "li@ubu.es");
		$this->post('/tareas/configurarParametrosTarea', $datos);
		$this->assertResponseSuccess();
		//$this->assertRedirect(['controller' => 'Profesores', 'action' => 'mostrarPanel']);
		$this->assertRedirect(['controller' => 'Tareas', 'action' => 'configurarParametrosTarea']);
		
		$query = $this->tareas_tabla->find("all")->where(["id" => $_SESSION['lti_idTarea']])->toArray();
		foreach ($query as $tarea){
			$this->assertEquals($_SESSION['lti_idTarea'], $tarea->id);
			$this->assertEquals($_SESSION['lti_idCurso'], $tarea->curso_id);
			$this->assertEquals($_SESSION['lti_userId'], $tarea->profesor_id);
			$this->assertEquals($_SESSION['lti_tituloTarea'], $tarea->nombre);
			$this->assertEquals($datos["num_max_intentos"], $tarea->num_max_intentos);
			$this->assertEquals($datos["paquete"], $tarea->paquete);
			$this->assertEquals($datos["fecha_limite"], $tarea->fecha_limite);
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
	
}