<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use App\Controller\ExcepcionesController;

class ExcepcionesControllerTest extends IntegrationTestCase{
	
	public function testMostrarErrorAccesoLocal(){
	
		$excepciones_controller = new ExcepcionesController();
		$excepciones_controller->mostrarErrorAccesoLocal();
		$this->get('/excepciones/mostrarErrorAccesoLocal');
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
	
}