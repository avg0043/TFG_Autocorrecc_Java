<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

class IntentosControllerTest extends IntegrationTestCase{
	
	public function setUp(){
		
		error_reporting(0);
		@session_start();
	
	}
	
	public function testComprobarSesion(){
	
		$this->post('intentos/subirPractica');
		$this->assertResponseSuccess();
		$this->assertRedirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoLocal']);
	
	}
	
	public function testComprobarRolAlumno(){
		
		$_SESSION["lti_userId"] = 1;
		$_SESSION["lti_rol"] = "Instructor";
		$this->post('intentos/subirPractica');
		$this->assertResponseSuccess();
		$this->assertRedirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoIncorrectoProfesor']);
		
	}
	
	public function testComprobarTestSubido(){
	
		$_SESSION["lti_userId"] = 1;
		$_SESSION["lti_rol"] = "Learner";
		$_SESSION['lti_idTarea'] = 18;
		$this->post('intentos/subirPractica');
		$this->assertResponseOk();
		$this->assertNoRedirect();
	
	}
	
}