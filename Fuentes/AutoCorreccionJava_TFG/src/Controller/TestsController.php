<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;
class TestsController extends AppController{
	
	private $id_profesor;
	private $ruta_carpeta_id;
	private $paquete_ruta;
	
	public function subirTest(){
		
		session_start();
		$this->comprobarSesion();
		$this->comprobarRolProfesor();
		
		if ($this->request->is('post')) {	
			$extension = pathinfo($_FILES['ficheroAsubir']['name'], PATHINFO_EXTENSION);
			$enunciado = $this->request->data['enunciado'];
			$tareas_tabla = TableRegistry::get("Tareas");
			
			if($enunciado == null && $extension == null){
				$this->Flash->error(__('Debes de rellenar el enunciado o subir un test'));		
			}
			elseif($enunciado != null && $extension == null){
				$tareas_tabla->query()
							 ->update()
							 ->set(['enunciado' => $enunciado])
							 ->where(['id' => $_SESSION['lti_idTarea']])
							 ->execute();
				$this->Flash->success(__('Enunciado guardado correctamente'));
			}
			elseif($extension != "zip"){
				$this->Flash->error(__('El fichero debe tener extensión .zip'));
			}
			else{		
				//$tareas_tabla = TableRegistry::get("Tareas");
				// Guardar enunciado en la BD Tareas
				//$enunciado = $this->request->data['enunciado'];
				if($enunciado != null){
					$tareas_tabla->query()
								 ->update()
								 ->set(['enunciado' => $enunciado])
								 ->where(['id' => $_SESSION['lti_idTarea']])
								 ->execute();
				}
				//
				
				$query = $tareas_tabla->find('all')
									  ->where(['id' => $_SESSION['lti_idTarea']])
									  ->toArray();
								      
				$this->id_profesor = $query[0]->profesor_id;
				$this->ruta_carpeta_id = "../../" . $_SESSION["lti_idCurso"] . "/" . $_SESSION["lti_idTarea"] . "/"
										. $_SESSION["lti_rol"] . "/" . $this->id_profesor . "/";					
				$this->__crearArquetipoMaven();					
				$this->Flash->success(__('Test subido correctamente'));
				//return $this->redirect(['action' => 'subirTest']);
			}
			return $this->redirect(['action' => 'subirTest']);
		}
	}
	
	private function __crearArquetipoMaven(){
		
		// Obtención del nombre del paquete de la tarea
		//$paquete = $this->obtenerTareaPorId($_SESSION['lti_idTarea'])[0]->paquete;
		$tareas_tabla = TableRegistry::get("Tareas");
		$query = $tareas_tabla->find('all')
							  ->where(['id' => $_SESSION['lti_idTarea']])
							  ->toArray();
		$paquete = $query[0]->paquete;
		
		$this->paquete_ruta = str_replace('.', '/', $paquete);
		
		if(!is_dir($this->ruta_carpeta_id)){
			mkdir($this->ruta_carpeta_id, 0777, true);
			exec('SET PATH=%JAVA_HOME%\bin;%PATH% 2>&1');
			exec('cd ' . $this->ruta_carpeta_id . ' && mvn archetype:generate -DarchetypeArtifactId=maven-archetype-quickstart -DinteractiveMode=false -DgroupId='.$paquete.' -DartifactId=arquetipo 2>&1');
			// Borrar App.java y AppTest.java
			unlink($this->ruta_carpeta_id . 'arquetipo/src/main/java/' . $this->paquete_ruta . '/App.java');
			unlink($this->ruta_carpeta_id . 'arquetipo/src/test/java/' . $this->paquete_ruta . '/AppTest.java');
		}
		
		$this->__extraerTest();
		
	}
	
	private function __extraerTest(){
		
		// Extraer tests dentro de la correspondiente carpeta del arquetipo
		$zip = new \ZipArchive();
		move_uploaded_file($_FILES["ficheroAsubir"]["tmp_name"], './' . $_FILES["ficheroAsubir"]["name"]);
		
		if ($zip->open($_FILES["ficheroAsubir"]["name"]) === TRUE) {
			$zip->extractTo($this->ruta_carpeta_id . 'arquetipo/src/test/java/'.$this->paquete_ruta.'/');
			$zip->close();
			
			// El pom.xml sólo se edita la primera vez que se sube un test
			//$test_query = $this->obtenerTestPorIdTarea($_SESSION["lti_idTarea"]);
			$test_query = $this->Tests->find('all')
								      ->where(['tarea_id' => $_SESSION["lti_idTarea"]])
								      ->toArray();
			
			if(empty($test_query)){
				$ficherosXml_controller = new FicherosXmlController();
				$ficherosXml_controller->editarPomArquetipoMaven($this->ruta_carpeta_id);
			}
		}
		
		unlink('./' . $_FILES["ficheroAsubir"]["name"]);
		$this->guardarTest($_SESSION['lti_idTarea'], $_FILES['ficheroAsubir']['name']);
		
	}

	public function guardarTest($id_tarea, $nombre_test){
		
		$nuevo_test = $this->Tests->newEntity();
		$nuevo_test->tarea_id = $id_tarea;
		$nuevo_test->nombre = $nombre_test;	
		date_default_timezone_set("Europe/Madrid");
		$nuevo_test->fecha_subida = new \DateTime(date("Y-m-d H:i:s"));	// fecha actual
		
		if (!$this->Tests->save($nuevo_test)) {
			$this->Flash->error(__('No ha sido posible registrar el test'));
		}
		
	}
	
}

?>