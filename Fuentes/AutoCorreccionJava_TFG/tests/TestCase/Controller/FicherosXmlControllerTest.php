<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use App\Controller\FicherosXmlController;

class FicherosXmlControllerTest extends IntegrationTestCase{
	
	public function testGuardarDatosXmlPluginPmd(){
		$ficherosXml_controller = new FicherosXmlController();
		//$this->assertEquals(1, $ficherosXml_controller->probando());
		//$ficherosXml_controller->guardarDatosXmlPluginPmd("../ficheros_test/", 1, 1);
	}
	
}