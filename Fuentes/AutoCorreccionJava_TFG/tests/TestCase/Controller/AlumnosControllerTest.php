<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;

class AlumnosControllerTest extends IntegrationTestCase{
	
	private $alumnos_tabla;
	
	public function setUp(){
		
		error_reporting(0);
		@session_start();
		$this->alumnos_tabla = TableRegistry::get("Alumnos");
		
	}
	
	public function tearDown(){
		
		$this->alumnos_tabla->deleteAll(['1 = 1']);
		
	}
	
	public function testRegistrarAlumno(){
		
		$datos = [
				'id' => 3,
				'curso_id' => 9,
				'nombre' => 'Luis',
				'apellidos' => 'Izquierdo',
				'correo' => 'li@ubu.es'
		];
		$_SESSION['lti_userId'] = $datos["id"];
		$_SESSION['lti_idCurso'] = $datos["curso_id"];
		$_SESSION['lti_nombre'] = $datos["nombre"];
		$_SESSION['lti_apellidos'] = $datos["apellidos"];
		$_SESSION['lti_correo'] = $datos["correo"];
	
		$this->post('/alumnos/registrarAlumno');
		$this->assertResponseSuccess();
		$this->assertRedirect(['controller' => 'Intentos', 'action' => 'subirPractica']);
		
		$query = $this->alumnos_tabla->find("all")->where(["id" => $datos["id"]])->toArray();
		foreach ($query as $alumno){
			$this->assertEquals($datos["id"], $alumno->id);
			$this->assertEquals($datos["curso_id"], $alumno->curso_id);
			$this->assertEquals($datos["nombre"], $alumno->nombre);
			$this->assertEquals($datos["apellidos"], $alumno->apellidos);
			$this->assertEquals($datos["correo"], $alumno->correo);
		}
	
	}
	
}