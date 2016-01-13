<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

class AppControllerTest extends IntegrationTestCase{
	
	public function testComprobarSesion(){
		
		$this->post('app/comprobarSesion');
		$this->assertResponseSuccess();
		$this->assertRedirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoLocal']);
		
	}
	
}