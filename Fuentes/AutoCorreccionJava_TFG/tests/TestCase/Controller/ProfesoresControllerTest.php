<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use App\Controller\ProfesoresController;
use Cake\ORM\TableRegistry;

class ProfesoresControllerTest extends IntegrationTestCase{
	
	private $profesores_tabla;
	private $profesores_controller;
	private $datos;
	
	public function setUp(){
		
		$this->profesores_controller = new ProfesoresController();
		$this->profesores_tabla = TableRegistry::get("Profesores");
		$this->datos = [
				'nombre' => 'Luis',
				'apellidos' => 'Izquierdo',
				'consumer_key' => 'ck1',
				'secret' => 's1',
				'correo' => 'li@ubu.es'
		];
		$this->__crearProfesor($this->datos["nombre"], $this->datos["apellidos"], $this->datos["consumer_key"], 
							   $this->datos["secret"], $this->datos["correo"]);
		
	}
	
	public function tearDown(){
		
		$this->profesores_tabla->deleteAll(['1 = 1']);
		
	}
	
	public function testObtenerProfesorPorKeyCorreo(){
		
		$query = $this->profesores_controller->obtenerProfesorPorKeyCorreo($this->datos["consumer_key"], 
																		   $this->datos["correo"]);
		$this->__comprobarDatosCorrectos($query);
		
	}
	
	public function testObtenerProfesorPorKey(){
		
		$query = $this->profesores_controller->obtenerProfesorPorKey($this->datos["consumer_key"]);
		$this->__comprobarDatosCorrectos($query);
		
	}
	
	public function testObtenerProfesorPorCorreo(){
		
		$query = $this->profesores_controller->obtenerProfesorPorCorreo($this->datos["correo"]);
		$this->__comprobarDatosCorrectos($query);
		
	}
	
	private function __comprobarDatosCorrectos($query){
		
		foreach ($query as $profesor){
			$this->assertEquals($this->datos["nombre"], $profesor->nombre);
			$this->assertEquals($this->datos["apellidos"], $profesor->apellidos);
			$this->assertEquals($this->datos["consumer_key"], $profesor->consumer_key);
			$this->assertEquals($this->datos["secret"], $profesor->secret);
			$this->assertEquals($this->datos["correo"], $profesor->correo);
		}
		
	}
	
	private function __crearProfesor($nombre, $apellidos, $consumer_key, $secret, $correo){
	
		$nuevo_profesor = $this->profesores_tabla->newEntity();
	
		$nuevo_profesor->nombre = "Luis";
		$nuevo_profesor->apellidos = "Izquierdo";
		$nuevo_profesor->consumer_key = "ck1";
		$nuevo_profesor->secret = "s1";
		$nuevo_profesor->correo = "li@ubu.es";
	
		$this->profesores_tabla->save($nuevo_profesor);
	
	}
	
}