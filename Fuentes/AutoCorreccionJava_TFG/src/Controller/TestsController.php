<?php

namespace App\Controller;

class TestsController extends AppController{
	
	private $id_profesor;
	private $ruta_carpeta_id;
	private $paquete_ruta;
	
	public function subirTest(){
		
		session_start();
		$this->comprobarSesion();
		
		if ($this->request->is('post')) {	
			$extension = pathinfo($_FILES['ficheroAsubir']['name'], PATHINFO_EXTENSION);
			
			if($extension != "zip"){				
				$this->Flash->error(__('El fichero debe tener extensión .zip!'));				
			}
			else{		
				$tareas_controller = new TareasController();
				$this->id_profesor = $tareas_controller->obtenerTareaPorId($_SESSION['lti_idTarea'])[0]->profesor_id;		
				$this->ruta_carpeta_id = "../../" . $_SESSION["lti_idCurso"] . "/" . $_SESSION["lti_idTarea"] . "/"
										. $_SESSION["lti_rol"] . "/" . $this->id_profesor . "/";					
				$this->__crearArquetipoMaven();					
				$this->Flash->success(__('Test subido correctamente'));
			}
		}
	}
	
	private function __crearArquetipoMaven(){
		
		// Obtención del nombre del paquete de la tarea
		$tareas_controller = new TareasController();
		$paquete = $tareas_controller->obtenerTareaPorId($_SESSION['lti_idTarea'])[0]->paquete;
		$this->paquete_ruta = str_replace('.', '/', $paquete);
		
		if(!is_dir($this->ruta_carpeta_id)){
			mkdir($this->ruta_carpeta_id, 0777, true);
			exec('SET PATH=%JAVA_HOME%\bin;%PATH% 2>&1');
			exec('cd ' . $this->ruta_carpeta_id . ' && mvn archetype:generate -DarchetypeArtifactId=maven-archetype-quickstart -DinteractiveMode=false -DgroupId='.$paquete.' -DartifactId=arquetipo 2>&1');
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
			
			//	 ---- MODIFICACIÓN -----
			//$this->__editarPOM();
			$ficherosXml_controller = new FicherosXmlController();
			$ficherosXml_controller->editarPomArquetipoMaven($this->ruta_carpeta_id);
			//	--------- 
		}
		
		unlink('./' . $_FILES["ficheroAsubir"]["name"]);
		$this->guardarTest($_SESSION['lti_idTarea'], $_FILES['ficheroAsubir']['name']);
		
	}
	
	/*
	private function __editarPOM(){
		
		$pom_xml = simplexml_load_file($this->ruta_carpeta_id . 'arquetipo/pom.xml');
		
		// Codificación
		$properties = $pom_xml->addChild('properties');
		$properties->addChild("project.build.sourceEncoding", "UTF-8");
		
		// Plugins
		$reporting = $pom_xml->addChild('reporting');
		$plugins = $reporting->addChild("plugins");
		
		// Plugin Surfire
		$plugin_surfire = $plugins->addChild("plugin");
		$plugin_surfire->addChild("groupId", "org.apache.maven.plugins");
		$plugin_surfire->addChild("artifactId", "maven-surefire-report-plugin");
		
		// Plugin JavaNCSS
		$plugin_javancss = $plugins->addChild("plugin");
		$plugin_javancss->addChild("groupId", "org.codehaus.mojo");
		$plugin_javancss->addChild("artifactId", "javancss-maven-plugin");
		
		// Plugin JDepend
		$plugin_jdepend = $plugins->addChild("plugin");
		$plugin_jdepend->addChild("groupId", "org.codehaus.mojo");
		$plugin_jdepend->addChild("artifactId", "jdepend-maven-plugin");
		
		// Plugin PMD
		$plugin_pmd = $plugins->addChild("plugin");
		$plugin_pmd->addChild("groupId", "org.apache.maven.plugins");
		$plugin_pmd->addChild("artifactId", "maven-pmd-plugin");
		
		// Plugin FindBugs
		$plugin_findbugs = $plugins->addChild("plugin");
		$plugin_findbugs->addChild("groupId", "org.codehaus.mojo");
		$plugin_findbugs->addChild("artifactId", "findbugs-maven-plugin");
		
		$pom_xml->asXml($this->ruta_carpeta_id . 'arquetipo/pom.xml');	
		
	}
	*/

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
	
	public function obtenerTestPorIdTarea($id_tarea){
	
		return $this->Tests->find('all')
						   ->where(['tarea_id' => $id_tarea])
						   ->toArray();
		
	}
	
}

?>