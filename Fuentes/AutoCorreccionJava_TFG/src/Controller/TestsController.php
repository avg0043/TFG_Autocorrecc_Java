<?php

namespace App\Controller;

class TestsController extends AppController{
	
	private $id_profesor;
	private $ruta_carpeta_id;
	
	public function subida(){
		
		session_start();
		
		if ($this->request->is('post')) {	
			$extension = pathinfo($_FILES['ficheroAsubir']['name'], PATHINFO_EXTENSION);
			
			if($extension != "zip"){				
				$this->Flash->error(__('El fichero debe tener extensión .zip!'));				
			}
			else{		
				$tareas_controller = new TareasController();
				$this->id_profesor = $tareas_controller->obtenerTarea($_SESSION['lti_idTarea'])[0]->profesor_id;	
				
				$this->ruta_carpeta_id = "../../" . $_SESSION["lti_idCurso"] . "/" . $_SESSION["lti_idTarea"] . "/"
						. $_SESSION["lti_rol"] . "/" . $this->id_profesor . "/";
				
				$this->__realizarAccionesProfesor();
				$this->Flash->success(__('Test subido!'));
			}
		}
	}
	
	private function __realizarAccionesProfesor(){
			
		// Obtención del nombre del paquete
		$tareas_controller = new TareasController();
		$paquete = $tareas_controller->obtenerTarea($_SESSION['lti_idTarea'])[0]->paquete;
		$paquete_ruta = str_replace('.', '/', $paquete);
		
		// Creación de la estructura de carpetas y del arquetipo de MAVEN
		if(!is_dir($this->ruta_carpeta_id)){
			mkdir($this->ruta_carpeta_id, 0777, true);
			exec('SET PATH=%JAVA_HOME%\bin;%PATH% 2>&1');
			exec('cd ' . $this->ruta_carpeta_id . ' && mvn archetype:generate -DarchetypeArtifactId=maven-archetype-quickstart -DinteractiveMode=false -DgroupId='.$paquete.' -DartifactId=arquetipo 2>&1');
		}
	
		// Extraer tests dentro de la correspondiente carpeta del arquetipo
		$zip = new \ZipArchive;
		move_uploaded_file($_FILES["ficheroAsubir"]["tmp_name"], './' . $_FILES["ficheroAsubir"]["name"]);
		if ($zip->open($_FILES["ficheroAsubir"]["name"]) === TRUE) {
			$zip->extractTo($this->ruta_carpeta_id . 'arquetipo/src/test/java/'.$paquete_ruta.'/');
			$zip->close();
		}
		unlink('./' . $_FILES["ficheroAsubir"]["name"]);
		
		$this->guardarTest($_SESSION['lti_idTarea'], $_FILES['ficheroAsubir']['name']);	
		
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