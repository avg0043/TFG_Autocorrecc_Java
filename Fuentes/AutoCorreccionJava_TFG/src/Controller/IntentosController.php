<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;
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
	//public function subirPractica($intento_realizado = null){
	public function subirPractica(){
	
		session_start();
		$this->comprobarSesion();	
		//$this->set("intento", $intento_realizado);		
		$this->__comprobarTestSubido();
		
		//$intentos_alumno = $this->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $_SESSION["lti_userId"]);
		$intentos_alumno = $this->Intentos->find('all')
    									  ->where(['tarea_id' => $_SESSION["lti_idTarea"], 'alumno_id' => $_SESSION["lti_userId"]]);
		
		//$num_ultimo_intento = 0;
    	$num_ultimo_intento = null;
		if(!$intentos_alumno->isEmpty()){
			//$ultimo_intento = $this->obtenerUltimoIntentoPorIdTareaAlumno($_SESSION["lti_idTarea"], $_SESSION["lti_userId"]);
			$ultimo_intento = $this->Intentos->find('all')
										     ->where(['tarea_id' => $_SESSION["lti_idTarea"], 'alumno_id' => $_SESSION["lti_userId"]])
										     ->last()
										     ->toArray();
			
			if(!empty($ultimo_intento)){
				$num_ultimo_intento = $ultimo_intento["numero_intento"];
			}
		}
		$this->set("num_ultimo_intento", $num_ultimo_intento);
		
		// Se pasa a la vista el enunciado de la tarea
		$tareas_tabla = TableRegistry::get("Tareas");
		$query = $tareas_tabla->find('all')
							  ->where(['id' => $_SESSION['lti_idTarea']])
							  ->toArray();
		$this->set("enunciado", $query[0]->enunciado);
		
		if ($this->request->is('post')) {	
			$extension = pathinfo($_FILES['ficheroAsubir']['name'], PATHINFO_EXTENSION);
			
			if($extension != "zip"){				
				$this->Flash->error(__('El fichero debe tener extensión .zip'));				
			}
			else{
				if($this->fecha_actual > $this->fecha_limite){				
					$this->Flash->error(__('La fecha límite de la entrega ha finalizado'));			
				}
				elseif($this->total_intentos_realizados == $this->numero_maximo_intentos){				
					$this->Flash->error(__('No puedes subir más veces la práctica'));				
				}
				else{									
					$this->ruta_carpeta_id = "../../" . $_SESSION["lti_idCurso"] . "/" . $_SESSION["lti_idTarea"] . "/"
										. $_SESSION["lti_rol"] . "/" . $_SESSION["lti_userId"] . "/";				
					//$this->id_profesor = $this->obtenerTareaPorId($_SESSION['lti_idTarea'])[0]->profesor_id;
					$tareas_tabla = TableRegistry::get("Tareas");
					$query = $tareas_tabla->find('all')
									      ->where(['id' => $_SESSION['lti_idTarea']])
									      ->toArray();
					$this->id_profesor = $query[0]->profesor_id;
										
					$this->__copiarArquetipoMaven();
					//return $this->redirect(['action' => 'subirPractica', $this->intento_realizado]);
					return $this->redirect(['action' => 'subirPractica']);
				}
			}
		}
	}
	
	private function __comprobarTestSubido(){

		//$query = $this->obtenerTestPorIdTarea($_SESSION['lti_idTarea']);
		$tests_tabla = TableRegistry::get("Tests");
		$query = $tests_tabla->find('all')
					    	 ->where(['tarea_id' => $_SESSION['lti_idTarea']])
					    	 ->toArray();
		
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
			
		//$this->paquete = $this->obtenerTareaPorId($_SESSION['lti_idTarea'])[0]->paquete;
		$tareas_tabla = TableRegistry::get("Tareas");
		$query_tarea = $tareas_tabla->find('all')
							  		->where(['id' => $_SESSION['lti_idTarea']])
							  		->toArray();
		$this->paquete = $query_tarea[0]->paquete;
		
		//$this->numero_maximo_intentos = $this->obtenerTareaPorId($_SESSION['lti_idTarea'])[0]->num_max_intentos;
		$this->numero_maximo_intentos = $query_tarea[0]->num_max_intentos;
		
		$query = $this->Intentos->find('all')
								->where(['tarea_id' => $_SESSION['lti_idTarea'], 'alumno_id' => $_SESSION['lti_userId']])
								->toArray();
		$this->total_intentos_realizados = count($query);		
		//$this->fecha_limite = $this->obtenerTareaPorId($_SESSION['lti_idTarea'])[0]->fecha_limite;
		$this->fecha_limite = $query_tarea[0]->fecha_limite;
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
		exec('xcopy ' . $ruta_dir_origen . ' ' . str_replace('/', '\\', $this->ruta_carpeta_id) . ' /s /e /Y');
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
			
			// Generar gráficas
			$graficas_controller = new GraficasController();
			$graficas_controller->generarGraficasViolacionesErroresAlumno();
			$graficas_controller->generarGraficaPrioridadesViolacionesIntentoRealizadoAlumno($this->id_intento);
		}
		else{
			$this->Flash->error(__('La práctica tiene errores de compilación'));
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
		//
		$comentarios = $this->request->data['comentarios'];
		if($comentarios != null){
			$nuevo_intento->comentarios = $comentarios;
		}
		//
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
		
		//--- SIEMPRE SE GENERA EL FINDBUGS.html, AUNQUE NO HAYA FALLOS
		$this->__guardarDatosXML();
	
	}
	
	private function __guardarDatosXML(){
	
		$ficherosXml_controller = new FicherosXmlController();
		$this->id_intento = $this->__obtenerIntentoPorTareaAlumnoNumIntento($_SESSION["lti_idTarea"],
																		    $_SESSION["lti_userId"],
																		    $this->intento_realizado)[0]->id;
																	  
		if($_SESSION["pmd_generado"]){
			$ficherosXml_controller->guardarDatosXmlPluginPmd($this->ruta_carpeta_id, $this->id_intento, 
															  						  $this->intento_realizado);
		}
	
		if($_SESSION["findbugs_generado"]){
			$_SESSION["findbugs_generado"] = false;
			$ficherosXml_controller->guardarDatosXmlPluginFindbugs($this->ruta_carpeta_id, $this->id_intento, 
																						   $this->intento_realizado);
		}
		
		//if(empty($this->obtenerViolacionPorIntentoTipo($this->id_intento, "IL_INFINITE_LOOP"))){
		$violaciones_tabla = TableRegistry::get("Violaciones");
		$query = $violaciones_tabla->find('all')
							       ->where(['intento_id' => $this->id_intento, 'tipo' => "IL_INFINITE_LOOP"])
							       ->toArray();
		if(empty($query)){
			$this->__ejecutarTests();
		}
		else{	// Violación de bucle infinito
			$this->Flash->error(__('Hay bucles infinitos. Test no ejecutado.'));
			unlink("../../".$_SESSION['lti_idCurso']."/".$_SESSION['lti_idTarea']."/".$_SESSION['lti_rol'].
					"/".$_SESSION['lti_userId']."/".$this->intento_realizado."/site/surefire-report.html");
		}
		
		$this->__actualizarResultadoIntento($this->id_intento, $this->test_pasado);
		
	}
	
	private function __ejecutarTests(){
		
		exec('cd ' . $this->ruta_carpeta_id . "/arquetipo" . ' && mvn test', $salida);
		$salida_string = implode(' ', $salida);

		if(strpos($salida_string, 'BUILD SUCCESS')){
			$this->Flash->success(__('La práctica ha pasado los test'));
			$this->test_pasado = true;
			unlink("../../".$_SESSION['lti_idCurso']."/".$_SESSION['lti_idTarea']."/".$_SESSION['lti_rol'].
					"/".$_SESSION['lti_userId']."/".$this->intento_realizado."/site/surefire-report.html");
		}
		elseif(strpos($salida_string, 'BUILD FAILURE')){
			$_SESSION["errores_unitarios"] = true;
			$this->Flash->error(__('La práctica no ha pasado los test'));
			$ficherosXml_controller = new FicherosXmlController();
			$ficherosXml_controller->guardarDatosXmlErrores($this->ruta_carpeta_id, $this->id_intento, 
																							 $this->intento_realizado);
		}
		else{
			$this->Flash->error(__('error desconocido'));
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
	
}

?>