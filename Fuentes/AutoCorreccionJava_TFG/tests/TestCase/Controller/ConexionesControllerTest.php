<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;

class ConexionesControllerTest extends IntegrationTestCase{
	
	private $profesores_tabla;
	
	public function setUp(){
	
		error_reporting(0);
		@session_start();
		$this->profesores_tabla = TableRegistry::get("Profesores");
		
	}
	
	public function tearDown(){
	
		$this->profesores_tabla->deleteAll(['1 = 1']);
	
	}
	
	public function testErrorProfesorEstablecerConexion(){
	
		$this->__crearProfesor(1, "Luis", "Izquierdo", "ck1", "s1", "li@ubu.es");
		$_REQUEST['oauth_consumer_key'] = "mal";
		$_REQUEST['roles'] = "Instructor";
		$_REQUEST['lis_person_contact_email_primary'] = "mal";
		
		$this->post('/conexiones/establecerConexion');
		$this->assertResponseSuccess();
		$this->assertRedirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorConsumerKey', "mal"]);
	
	}
	
	public function testErrorAlumnoEstablecerConexion(){
	
		$this->__crearProfesor(1, "Luis", "Izquierdo", "ck1", "s1", "li@ubu.es");
		$_REQUEST['oauth_consumer_key'] = "mal";
		$_REQUEST['roles'] = "Learner";
		
		$this->post('/conexiones/establecerConexion');
		$this->assertResponseSuccess();
		$this->assertRedirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorConsumerKey', "mal"]);
	
	}
	
	public function testProfesorPrimerAccesoEstablecerConexion(){
	
		$this->__crearProfesor(1, "Luis", "Izquierdo", "ck1", "s1", "li@ubu.es");
		$_REQUEST['oauth_consumer_key'] = "ck1";
		$_REQUEST['roles'] = "Instructor";
		$_REQUEST['lis_person_contact_email_primary'] = "li@ubu.es";
	
		$this->post('/conexiones/establecerConexion');
		$this->assertResponseSuccess();
		$this->assertRedirect(['controller' => 'Tareas', 'action' => 'configurarParametrosTarea']);
	
	}
	
	public function testAlumnoPrimerAccesoEstablecerConexion(){
	
		$this->__crearProfesor(1, "Luis", "Izquierdo", "ck1", "s1", "li@ubu.es");
		$_REQUEST['oauth_consumer_key'] = "ck1";
		$_REQUEST['roles'] = "Learner";
	
		$this->post('/conexiones/establecerConexion');
		$this->assertResponseSuccess();
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
	
}