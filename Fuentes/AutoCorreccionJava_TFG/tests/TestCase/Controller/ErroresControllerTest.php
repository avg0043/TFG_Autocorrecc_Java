<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use App\Controller\ErroresController;
use Cake\ORM\TableRegistry;

class ErroresControllerTest extends IntegrationTestCase{
	
	public function testGuardarError(){
		
		$errores_controller = new ErroresController();
		$datos = [
				'intento_id' => 20,
				'nombre_clase' => 'Pipas.java',
				'nombre_test' => 'testPipas',
				'tipo_error' => 'criminal',
				'tipo' => 'pipas.framework',
		];
		
		$errores_controller->guardarError($datos['intento_id'], $datos['nombre_clase'], $datos['nombre_test'], 
										  $datos['tipo_error'], $datos['tipo'], "nada");
		$errores = TableRegistry::get('Errores');
		$query = $errores->find()->where(['intento_id' => $datos['intento_id'], 'nombre_clase' => $datos['nombre_clase']]);
		$this->assertEquals(1, $query->count());
		
	}

}