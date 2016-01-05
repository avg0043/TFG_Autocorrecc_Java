<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use App\Controller\PruebasController;

class PruebasControllerTest extends IntegrationTestCase{
	
	public function testDoble(){
		
		$pruebas_controller = new PruebasController();
		
		$this->assertEquals(2, $pruebas_controller->doble());
		$intentos = new IntentosController();
		
	}

}