<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use App\Controller\ExcepcionesController;

class ExcepcionesControllerTest extends IntegrationTestCase{
	
	public function testMostrarErrorAccesoLocal(){
	
		$this->post('/excepciones/mostrarErrorAccesoLocal');
		$this->assertResponseOk();
		$this->assertNoRedirect();
	
	}
	
	public function testMostrarErrorConsumerKey(){
	
		$excepciones_controller = new ExcepcionesController();
		$excepciones_controller->mostrarErrorConsumerKey("ck1");
		$this->get('/excepciones/mostrarErrorConsumerKey/ck1');
		$this->assertResponseOk();
		$this->assertNoRedirect();
	
	}
	
	public function testMostrarErrorAccesoIncorrectoAlumno(){
	
		$this->post('/excepciones/mostrarErrorAccesoIncorrectoAlumno');
		$this->assertResponseOk();
		$this->assertNoRedirect();
	
	}
	
	public function testMostrarErrorAccesoIncorrectoProfesor(){
	
		$this->post('/excepciones/mostrarErrorAccesoIncorrectoProfesor');
		$this->assertResponseOk();
		$this->assertNoRedirect();
	
	}
	
}