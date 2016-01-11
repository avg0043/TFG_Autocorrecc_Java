<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;

class ConexionesControllerTest extends IntegrationTestCase{
	
	private $profesores_tabla;
	private $tareas_tabla;
	
	public function setUp(){
	
		error_reporting(0);		// ---- BIENNN??¿?¿?¿?¿?¿?
		$this->profesores_tabla = TableRegistry::get("Profesores");
		$this->tareas_tabla = TableRegistry::get("Tareas");
		
	}
	
	public function tearDown(){
	
		$this->tareas_tabla->deleteAll(['1 = 1']);
		$this->profesores_tabla->deleteAll(['1 = 1']);
	
	}
	
	public function testErrorProfesorEstablecerConexion(){
	
		@session_start();
		$this->__crearProfesor(1, "Luis", "Izquierdo", "ck1", "s1", "li@ubu.es");
		$_REQUEST['oauth_consumer_key'] = "mal";
		$_REQUEST['roles'] = "Instructor";
		$_REQUEST['lis_person_contact_email_primary'] = "mal";
		
		$this->post('/conexiones/establecerConexion');
		$this->assertRedirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorConsumerKey', "mal"]);
	
	}
	
	public function testErrorAlumnoEstablecerConexion(){
	
		@session_start();
		$this->__crearProfesor(1, "Luis", "Izquierdo", "ck1", "s1", "li@ubu.es");
		$_REQUEST['oauth_consumer_key'] = "mal";
		$_REQUEST['roles'] = "Learner";
		
		$this->post('/conexiones/establecerConexion');
		$this->assertRedirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorConsumerKey', "mal"]);
	
	}
	
	public function testProfesorPrimerAccesoEstablecerConexion(){
	
		@session_start();
		$this->__crearProfesor(1, "Luis", "Izquierdo", "ck1", "s1", "li@ubu.es");
		$_REQUEST['oauth_consumer_key'] = "ck1";
		$_REQUEST['roles'] = "Instructor";
		$_REQUEST['lis_person_contact_email_primary'] = "li@ubu.es";
	
		$this->post('/conexiones/establecerConexion');
		$this->assertRedirect(['controller' => 'Tareas', 'action' => 'configurarParametrosTarea']);
	
	}
	
	public function testAlumnoEstablecerConexion(){
	
		@session_start();
		$this->__crearProfesor(1, "Luis", "Izquierdo", "ck1", "s1", "li@ubu.es");
		$_REQUEST['oauth_consumer_key'] = "ck1";
		$_REQUEST['roles'] = "Learner";
	
		$this->post('/conexiones/establecerConexion');
		$this->assertRedirect(['controller' => 'Alumnos', 'action' => 'registrarAlumno']);
	
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
	
	private function __crearTarea(){
	
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