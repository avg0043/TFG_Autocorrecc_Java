<?php

namespace App\Controller;

class TestsController extends AppController{
	
	private $id_profesor;
	
	public function subida(){
		
		session_start();
		
		if ($this->request->is('post')) {	
			$extension = pathinfo($_FILES['ficheroAsubir']['name'], PATHINFO_EXTENSION);
			
			if($extension != "java"){				
				$this->Flash->error(__('El fichero debe tener extensión .java!'));				
			}
			else{				
				$tareas_controller = new TareasController();
				$this->id_profesor = $tareas_controller->obtenerTarea($_SESSION['lti_idTarea'])[0]->profesor_id;	
				$this->__realizarAccionesProfesor();									
			}
		}
	}
	
	private function __realizarAccionesProfesor(){
		
		$ruta = "../../" . $_SESSION["lti_idCurso"] . "/" . $_SESSION["lti_idTarea"] . "/"
							. $_SESSION["lti_rol"] . "/";
	
		$directorio_destino = $ruta . $this->id_profesor . "/";
	
		// Creación de la estructura de carpetas y del arquetipo de MAVEN
		if(!is_dir($directorio_destino)){
			mkdir($directorio_destino, 0777, true);
			exec('SET PATH=%JAVA_HOME%\bin;%PATH% 2>&1');
			exec('cd ' . $directorio_destino . ' && mvn archetype:generate -DarchetypeArtifactId=maven-archetype-quickstart -DinteractiveMode=false -DgroupId=ubu -DartifactId=arquetipo 2>&1');
		}
	
		// Guardar test subido en el arquetipo de MAVEN
		$path_fichero_maven = $directorio_destino . 'arquetipo/src/test/java/ubu/' . $_FILES["ficheroAsubir"]["name"];
		move_uploaded_file($_FILES["ficheroAsubir"]["tmp_name"], $path_fichero_maven);
	
		// Guardar el test subido en base de datos
		$this->guardarTest($_SESSION['lti_idTarea'], $_FILES['ficheroAsubir']['name']);
	
		$this->Flash->success(__('Test subido!'));
		
	}

	public function guardarTest($id_tarea, $nombre_test){
		
		$nuevo_test = $this->Tests->newEntity();
		$nuevo_test->tarea_id = $id_tarea;
		$nuevo_test->nombre = $nombre_test;
		
		// fecha actual
		date_default_timezone_set("Europe/Madrid");
		$nuevo_test->fecha_subida = new \DateTime(date("Y-m-d H:i:s"));		
		
		if (!$this->Tests->save($nuevo_test)) {
			$this->Flash->error(__('No ha sido posible registrar el test'));
		}
		
	}
	
}

?>