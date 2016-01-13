<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;
use App\Controller\ProfesoresController;

class ProfesoresControllerTest extends IntegrationTestCase{
	
	private $profesores_tabla;
	
	public function setUp(){
	
		error_reporting(0);
		@session_start();
		$this->profesores_tabla = TableRegistry::get("Profesores");
	
	}
	
	public function tearDown(){
	
		$this->profesores_tabla->deleteAll(['1 = 1']);
	
	}
	
	public function testRegistrarProfesor(){
		
		$data = [
				'nombre' => "Luis",
				'apellidos' => "Izquierdo",
				'contraseña' => 'password1234',
				'correo' => 'li@ubu.es'
		];
		
		$this->post('/profesores/registrarProfesor', $data);
		$this->assertResponseSuccess();
		$this->assertRedirect(['controller' => 'Profesores', 'action' => 'mostrarParametrosLti', $data["correo"]]);
		
	}
	
	public function testMostrarParametrosLti(){
		
		$profesores_controller = new ProfesoresController();
		$profesores_controller->mostrarParametrosLti("correo@ubu.es");
		$this->get('/profesores/mostrarParametrosLti/correo@ubu.es');
		$this->assertResponseOk();
		$this->assertNoRedirect();
		
	}
	
	public function testErrorMostrarPanel(){
		
		$this->post('/profesores/mostrarPanel');
		$this->assertResponseSuccess();
		$this->assertRedirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoLocal']);
		
	}
	
	public function testErrorGenerarReportePlagiosPracticas(){
		
		$this->post('/profesores/generarReportePlagiosPracticas');
		$this->assertResponseSuccess();
		$this->assertRedirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoLocal']);
		
	}

	public function testErrorDescargarPracticasAlumnos(){
		
		$this->post('/profesores/descargarPracticasAlumnos');
		$this->assertResponseSuccess();
		$this->assertRedirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoLocal']);
		
	}
	
	public function testDescargarPracticasAlumnos(){
	
		$_SESSION["lti_userId"] = 3;
		$_SESSION["lti_idTarea"] = 18;
		$this->post('/profesores/descargarPracticasAlumnos');
		$this->assertResponseOk();
		$this->assertNoRedirect();
	
	}
	
	public function testErrorGenerarGraficas(){
		
		$this->post('/profesores/generarGraficas');
		$this->assertResponseSuccess();
		$this->assertRedirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoLocal']);
		
	}
	
}