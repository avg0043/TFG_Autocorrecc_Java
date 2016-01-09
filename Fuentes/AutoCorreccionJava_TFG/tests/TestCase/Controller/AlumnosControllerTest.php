<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use App\Controller\AlumnosController;
use Cake\ORM\TableRegistry;

class AlumnosControllerTest extends IntegrationTestCase{
	
	private $alumnos_tabla;
	private $alumnos_controller;
	private $datos_alumno1;

	public function setUp(){
		
		$this->alumnos_controller = new AlumnosController();
		$this->alumnos_tabla = TableRegistry::get("Alumnos");
		$this->datos_alumno1 = [
				'id' => 3,
				'curso_id' => 9,
				'nombre' => 'Ivan',
				'apellidos' => 'Izquierdo',
				'correo' => 'ii@alu.ubu.es'
		];
		
	}
	
	public function tearDown(){
		
		$this->alumnos_tabla->deleteAll(['1 = 1']);
		
	}
	
	public function testObtenerAlumnos(){
		
		$datos_alumno2 = [
				'id' => 5,
				'curso_id' => 9,
				'nombre' => 'Luis',
				'apellidos' => 'Vázquez',
				'correo' => 'lv@alu.ubu.es'
		];
		$this->__crearAlumno($this->datos_alumno1["id"], $this->datos_alumno1["curso_id"], $this->datos_alumno1["nombre"],
							 $this->datos_alumno1["apellidos"], $this->datos_alumno1["correo"]);
		$this->__crearAlumno($datos_alumno2["id"], $datos_alumno2["curso_id"], $datos_alumno2["nombre"],
							 $datos_alumno2["apellidos"], $datos_alumno2["correo"]);
		$query = $this->alumnos_controller->obtenerAlumnos();
		
		$numero_alumno = 1;
		$this->assertEquals(2, $query->count());
		foreach ($query as $alumno){
			if($numero_alumno == 1){
				$this->assertEquals($this->datos_alumno1["id"], $alumno->id);
				$this->assertEquals($this->datos_alumno1["curso_id"], $alumno->curso_id);
				$this->assertEquals($this->datos_alumno1["nombre"], $alumno->nombre);
				$this->assertEquals($this->datos_alumno1["apellidos"], $alumno->apellidos);
				$this->assertEquals($this->datos_alumno1["correo"], $alumno->correo);
			}
			else{
				$this->assertEquals($datos_alumno2["id"], $alumno->id);
				$this->assertEquals($datos_alumno2["curso_id"], $alumno->curso_id);
				$this->assertEquals($datos_alumno2["nombre"], $alumno->nombre);
				$this->assertEquals($datos_alumno2["apellidos"], $alumno->apellidos);
				$this->assertEquals($datos_alumno2["correo"], $alumno->correo);
			}
			$numero_alumno++;
		}
		
		
	}
	
	public function testObtenerAlumnoPorId(){
		
		$this->__crearAlumno($this->datos_alumno1["id"], $this->datos_alumno1["curso_id"], $this->datos_alumno1["nombre"], 
							 $this->datos_alumno1["apellidos"], $this->datos_alumno1["correo"]);
		$query = $this->alumnos_controller->obtenerAlumnoPorId($this->datos_alumno1["id"]);
		
		foreach ($query as $alumno){
			$this->assertEquals($this->datos_alumno1["id"], $alumno->id);
			$this->assertEquals($this->datos_alumno1["curso_id"], $alumno->curso_id);
			$this->assertEquals($this->datos_alumno1["nombre"], $alumno->nombre);
			$this->assertEquals($this->datos_alumno1["apellidos"], $alumno->apellidos);
			$this->assertEquals($this->datos_alumno1["correo"], $alumno->correo);	
		}
		
	}
	
	private function __crearAlumno($id, $curso_id, $nombre, $apellidos, $correo){
	
		$nuevo_alumno = $this->alumnos_tabla->newEntity();
	
		$nuevo_alumno->id = $id;
		$nuevo_alumno->curso_id = $curso_id;
		$nuevo_alumno->nombre = $nombre;
		$nuevo_alumno->apellidos = $apellidos;
		$nuevo_alumno->correo = $correo;
	
		$this->alumnos_tabla->save($nuevo_alumno);
	
	}
	
}