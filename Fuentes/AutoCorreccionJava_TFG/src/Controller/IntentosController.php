<?php

namespace App\Controller;

class IntentosController extends AppController{

	private $fecha_actual;
	private $fecha_limite;
	private $total_intentos_realizados;
	private $numero_maximo_intentos;
	private $test_pasado = false;
	private $id_profesor;
	private $ruta_carpeta_id;
	private $intento_realizado;
	private $paquete;
	private $nombre_practica_zip;
	private $id_intento;
	
	/**
	 * Función que se encarga de manejar los datos introducidos
	 * en el formulario, el cual pertenece a su vista.
	 * 
	 * @param string $tipo_usuario	puede ser profesor o alumno.
	 */
	public function subirPractica($intento_realizado = null){
		
		session_start();
		$this->comprobarSesion();
		
		$this->set("intento", $intento_realizado);		
		$this->__comprobarTestSubido();
			
		if ($this->request->is('post')) {	
			$extension = pathinfo($_FILES['ficheroAsubir']['name'], PATHINFO_EXTENSION);
			
			if($extension != "zip"){				
				$this->Flash->error(__('El fichero debe tener extensión .zip!'));				
			}
			else{
				if($this->fecha_actual > $this->fecha_limite){				
					$this->Flash->error(__('La fecha tope de la entrega ha finalizado'));			
				}
				elseif($this->total_intentos_realizados == $this->numero_maximo_intentos){				
					$this->Flash->error(__('No puedes subir más veces la práctica'));				
				}
				else{									
					$this->ruta_carpeta_id = "../../" . $_SESSION["lti_idCurso"] . "/" . $_SESSION["lti_idTarea"] . "/"
										. $_SESSION["lti_rol"] . "/" . $_SESSION["lti_userId"] . "/";				
					$tareas_controller = new TareasController();
					$this->id_profesor = $tareas_controller->obtenerTareaPorId($_SESSION['lti_idTarea'])[0]->profesor_id;									
					$this->__copiarArquetipoMaven();
					return $this->redirect(['action' => 'subirPractica', $this->intento_realizado]);
				}
			}
		}
	}
	
	private function __comprobarTestSubido(){
		
		$tests_controller = new TestsController();
		$query = $tests_controller->obtenerTestPorIdTarea($_SESSION['lti_idTarea']);
		$test_subido = true;
		
		if(empty($query)){
			$test_subido = false;
		}
		else{
			$this->__establecerDatosVista();
		}
		
		$this->set('test_subido', $test_subido);
		
	}
	
	private function __establecerDatosVista(){
		
		$tareas_controller = new TareasController();
		$this->paquete = $tareas_controller->obtenerTareaPorId($_SESSION['lti_idTarea'])[0]->paquete;	
		$this->numero_maximo_intentos = $tareas_controller->obtenerTareaPorId($_SESSION['lti_idTarea'])[0]->num_max_intentos;			
		$query = $this->Intentos->find('all')
								->where(['tarea_id' => $_SESSION['lti_idTarea'], 'alumno_id' => $_SESSION['lti_userId']])
								->toArray();
		$this->total_intentos_realizados = count($query);		
		$this->fecha_limite = $tareas_controller->obtenerTareaPorId($_SESSION['lti_idTarea'])[0]->fecha_limite;
		$this->fecha_limite = (new \DateTime($this->fecha_limite))->format('Y-m-d');
		$this->fecha_actual = (new \DateTime(date("Y-m-d H:i:s")))->format('Y-m-d');
		
		$this->set('paquete', $this->paquete);
		$this->set('num_maximo_intentos', $this->numero_maximo_intentos);
		$this->set('fecha_limite', $this->fecha_limite);
		$this->set('num_intentos_realizados', $this->total_intentos_realizados);
		
	}
	
	private function __copiarArquetipoMaven(){
		
		if(!is_dir($this->ruta_carpeta_id)){
			mkdir($this->ruta_carpeta_id . "/", 0777, true);  // estructuras de carpetas del alumno
		}
		
		// Copiar el arquetipo maven a la carpeta id alumno
		$ruta_dir_origen = "..\\..\\" . $_SESSION["lti_idCurso"] . "\\" . $_SESSION["lti_idTarea"] . "\\"
							. "Instructor" . "\\" . $this->id_profesor;
		exec('xcopy ' . $ruta_dir_origen . ' ' . str_replace('/', '\\', $this->ruta_carpeta_id) . ' /s /e');
		// Borrar estructura main>java del arquetipo
		exec('cd ' . $this->ruta_carpeta_id . "/arquetipo/src/main" . ' && rmdir java /s /q && md java');
		$this->__extraerPractica();
		
	}
	
	private function __extraerPractica(){
	
		$zip = new \ZipArchive();
		$this->nombre_practica_zip = $_FILES["ficheroAsubir"]["name"];
		move_uploaded_file($_FILES["ficheroAsubir"]["tmp_name"], './' . $this->nombre_practica_zip);
		
		if($zip->open($this->nombre_practica_zip) === TRUE){
			$zip->extractTo($this->ruta_carpeta_id . 'arquetipo/src/main/java/');
			$zip->close();
		}	
		
		$this->__comprobarEstructuraCarpetasPractica();
		
	}
	
	private function __comprobarEstructuraCarpetasPractica(){
		
		$paquete_ruta = str_replace('.', '/', $this->paquete);
		
		if(is_dir($this->ruta_carpeta_id . "arquetipo/src/main/java/" . $paquete_ruta)){
			$this->__compilarPractica();
		}
		else{
			// Borrar estructura main>java del arquetipo
			exec('cd ' . $this->ruta_carpeta_id . "/arquetipo/src/main" . ' && rmdir java /s /q && md java');
			unlink('./' . $this->nombre_practica_zip);
			$this->Flash->error(__("El zip no contiene la estructura de carpetas correcta"));
		}
		
	}
	
	private function __compilarPractica(){
		
		exec('cd ' . $this->ruta_carpeta_id . "/arquetipo" . ' && mvn compile', $salida);
		$salida_string = implode(' ', $salida);
		
		if(strpos($salida_string, 'BUILD SUCCESS')){	// Compilación correcta
			// Creación carpeta intento
			$this->intento_realizado = $this->total_intentos_realizados + 1;
			mkdir($this->ruta_carpeta_id . $this->intento_realizado . "/", 0777, true);
			// Copiar practica subida a la carpeta del intento
			exec('xcopy ' . str_replace('/', '\\', $this->ruta_carpeta_id)."\\arquetipo\\src\\main\\java" . ' ' .
							str_replace('/', '\\', $this->ruta_carpeta_id)."\\".$this->intento_realizado . ' /s /e');
			// Copiar zip practica a la carpeta del intento
			exec('xcopy ' . $this->nombre_practica_zip . ' ' . str_replace('/', '\\', $this->ruta_carpeta_id)."\\".$this->intento_realizado);		
			$this->__guardarIntento();
		}
		else{
			$this->Flash->error(__('La práctica tiene errores de compilación!'));
		}
		
		unlink('./' . $this->nombre_practica_zip);
		exec('cd ' . $this->ruta_carpeta_id . "/arquetipo/src/main" . ' && rmdir java /s /q && md java'); // borrar main/java
		exec('cd ' . $this->ruta_carpeta_id . "/arquetipo" . ' && rmdir target /s /q');	// borrar target
		
	}
	
	private function __guardarIntento(){
	
		$nuevo_intento = $this->Intentos->newEntity();
		$nuevo_intento->tarea_id = $_SESSION['lti_idTarea'];
		$nuevo_intento->alumno_id = $_SESSION['lti_userId'];
		$nuevo_intento->nombre = $this->nombre_practica_zip;
		$nuevo_intento->numero_intento = $this->intento_realizado;
		$nuevo_intento->ruta = $this->ruta_carpeta_id . $this->intento_realizado . "/";
		date_default_timezone_set("Europe/Madrid");
		$nuevo_intento->fecha_intento = new \DateTime(date("Y-m-d H:i:s"));	// fecha actual
		$this->Intentos->save($nuevo_intento);
		
		$this->__generarReportes();
	
	}
	
	private function __generarReportes(){
	
		$_SESSION["pmd_generado"] = false;
		$_SESSION["findbugs_generado"] = false;
		$_SESSION["errores_unitarios"] = false;
	
		exec('cd ' . $this->ruta_carpeta_id . "/arquetipo" . ' && mvn jxr:jxr site');
	
		if(file_exists($this->ruta_carpeta_id."/arquetipo/target/site/pmd.html")){
			$_SESSION["pmd_generado"] = true;
		}
		if(file_exists($this->ruta_carpeta_id."/arquetipo/target/site/findbugs.html")){
			$_SESSION["findbugs_generado"] = true;
		}
	
		// Copiar target generado a la carpeta del intento
		exec('xcopy ' . str_replace('/', '\\', $this->ruta_carpeta_id)."\\arquetipo\\target" . ' ' .
				str_replace('/', '\\', $this->ruta_carpeta_id)."\\".$this->intento_realizado . ' /s /e');
		
		if($_SESSION["pmd_generado"] || $_SESSION["findbugs_generado"]){
			$this->__guardarDatosXML();
		}
		else{
			$this->__ejecutarTests();
		}
	
	}
	
	private function __guardarDatosXML(){
	
		$violaciones_controller = new ViolacionesController();
		$this->id_intento = $this->__obtenerIntentoPorTareaAlumnoNumIntento($_SESSION["lti_idTarea"],
																	  $_SESSION["lti_userId"],
																	  $this->intento_realizado)[0]->id;
	
		if($_SESSION["pmd_generado"]){
			$xml_pmd = simplexml_load_file($this->ruta_carpeta_id. $this->intento_realizado . "/pmd.xml");
	
			foreach($xml_pmd->children() as $files){
				foreach($files->children() as $violations){
					$violaciones_controller->guardarViolacion($this->id_intento, $violations["class"].".java",
							$violations["rule"], $violations, $violations["priority"],
							$violations["beginline"], $violations["endline"]);
				}
			}
		}
	
		if($_SESSION["findbugs_generado"]){
			$_SESSION["findbugs_generado"] = false;
			$xml_findbugs = simplexml_load_file($this->ruta_carpeta_id. $this->intento_realizado . "/findbugsXml.xml");
				
			foreach($xml_findbugs->children()->BugInstance as $bug_instances){
				$_SESSION["findbugs_generado"] = true;
				if($bug_instances->SourceLine["start"] != null && $bug_instances->SourceLine["end"] != null){
					$violaciones_controller->guardarViolacion($this->id_intento, $bug_instances->Class->SourceLine["sourcefile"],
							$bug_instances["type"], $bug_instances->LongMessage, $bug_instances["priority"],
							$bug_instances->SourceLine["start"], $bug_instances->SourceLine["end"]);
				}
				else{
					$violaciones_controller->guardarViolacion($this->id_intento, $bug_instances->Class->SourceLine["sourcefile"],
							$bug_instances["type"], $bug_instances->LongMessage, $bug_instances["priority"]);
				}
			}
		}
			
		if(empty($violaciones_controller->obtenerViolacionPorIntentoTipo($this->id_intento, "IL_INFINITE_LOOP"))){
			$this->__ejecutarTests();
		}
		else{	// Violación de bucle infinito
			$this->Flash->error(__('Hay bucles infinitos. Test no ejecutado.'));
		}
		
		$this->__actualizarResultadoIntento($this->id_intento, $this->test_pasado);
		
	}
	
	private function __ejecutarTests(){
		
		exec('cd ' . $this->ruta_carpeta_id . "/arquetipo" . ' && mvn test', $salida);
		$salida_string = implode(' ', $salida);

		if(strpos($salida_string, 'BUILD SUCCESS')){
			$this->Flash->success(__('La práctica ha pasado los test'));
			$this->test_pasado = true;
		}
		elseif(strpos($salida_string, 'BUILD FAILURE')){
			$_SESSION["errores_unitarios"] = true;
			$this->Flash->error(__('La práctica no ha pasado los test'));
			$this->__guardarDatosErrorXML();
		}
		else{
			$this->Flash->error(__('error desconocido'));
		}
							
	}
	
	private function __guardarDatosErrorXML(){
		
		$errores_controller = new ErroresController();
		$ficheros_xml = glob($this->ruta_carpeta_id . $this->intento_realizado . "/surefire-reports/*xml");
		
		foreach($ficheros_xml as $fichero) {
			$xml = simplexml_load_file($fichero);
			$fallos = (int) $xml["failures"];
			
			if($fallos > 0){	// test que falla
				foreach($xml->children()->testcase as $test_case){
					$errores_controller->guardarError($this->id_intento, $test_case["classname"], $test_case["name"], 
													  $test_case->failure["type"], $test_case->failure);
				}
			}
		}
		
	}
	
	private function __obtenerIntentoPorTareaAlumnoNumIntento($id_tarea, $id_alumno, $num_intento){
		
		return $this->Intentos->find('all')
							  ->where(['tarea_id' => $id_tarea, 'alumno_id' => $id_alumno, 'numero_intento' => $num_intento])
							  ->toArray();
	
	}
		
	private function __actualizarResultadoIntento($id_intento, $resultado){
		
		$this->Intentos->query()
					   ->update()
					   ->set(['resultado' => $resultado])
					   ->where(['id' => $id_intento])
					   ->execute();
		
		$this->Flash->success(__('Práctica subida. Realizado intento número: ' . $this->intento_realizado));
		
	}
	
	public function obtenerUltimoIntentoPorIdAlumno($id_alumno){
		
		return $this->Intentos->find('all')
							  ->where(['alumno_id' => $id_alumno])
							  ->last()
							  ->toArray();
		
	}
	
	public function obtenerIntentosPorIdAlumno($id_alumno){
		
		return $this->Intentos->find('all')
							  ->where(['alumno_id' => $id_alumno]);
		
	}
	
	public function obtenerIntentosPorIdTarea($id_tarea){
	
		return $this->Intentos->find('all')
							  ->where(['tarea_id' => $id_tarea]);
	
	}
	
}

?>