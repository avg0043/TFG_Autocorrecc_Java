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
		
		//$intentos_alumno = $this->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $_SESSION["lti_userId"]);
		$intentos_alumno = $this->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $_SESSION["lti_userId"]);
		$num_ultimo_intento = 0;
		if(!$intentos_alumno->isEmpty()){
			//$ultimo_intento = $this->obtenerUltimoIntentoPorIdTareaAlumno($_SESSION["lti_idTarea"], $_SESSION["lti_userId"]);
			$ultimo_intento = $this->obtenerUltimoIntentoPorIdTareaAlumno($_SESSION["lti_idTarea"], $_SESSION["lti_userId"]);
			if(!empty($ultimo_intento)){
				$num_ultimo_intento = $ultimo_intento["numero_intento"];
			}
		}
		$this->set("num_ultimo_intento", $num_ultimo_intento);
		
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
					//$tareas_controller = new TareasController();
					//$this->id_profesor = $tareas_controller->obtenerTareaPorId($_SESSION['lti_idTarea'])[0]->profesor_id;									
					$this->id_profesor = $this->obtenerTareaPorId($_SESSION['lti_idTarea'])[0]->profesor_id;
					$this->__copiarArquetipoMaven();
					return $this->redirect(['action' => 'subirPractica', $this->intento_realizado]);
				}
			}
		}
	}
	
	private function __comprobarTestSubido(){
		
		//$tests_controller = new TestsController();
		//$query = $tests_controller->obtenerTestPorIdTarea($_SESSION['lti_idTarea']);
		$query = $this->obtenerTestPorIdTarea($_SESSION['lti_idTarea']);
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
		
		//$tareas_controller = new TareasController();
		//$this->paquete = $tareas_controller->obtenerTareaPorId($_SESSION['lti_idTarea'])[0]->paquete;	
		$this->paquete = $this->obtenerTareaPorId($_SESSION['lti_idTarea'])[0]->paquete;
		//$this->numero_maximo_intentos = $tareas_controller->obtenerTareaPorId($_SESSION['lti_idTarea'])[0]->num_max_intentos;			
		$this->numero_maximo_intentos = $this->obtenerTareaPorId($_SESSION['lti_idTarea'])[0]->num_max_intentos;
		$query = $this->Intentos->find('all')
								->where(['tarea_id' => $_SESSION['lti_idTarea'], 'alumno_id' => $_SESSION['lti_userId']])
								->toArray();
		$this->total_intentos_realizados = count($query);		
		//$this->fecha_limite = $tareas_controller->obtenerTareaPorId($_SESSION['lti_idTarea'])[0]->fecha_limite;
		$this->fecha_limite = $this->obtenerTareaPorId($_SESSION['lti_idTarea'])[0]->fecha_limite;
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
			//$graficas_controller->generarGraficaPrioridadesViolacionesAlumno();
			$graficas_controller->generarGraficaPrioridadesViolacionesIntentoRealizadoAlumno($this->id_intento);
			
			/*
			$this->__generarGraficasViolacionesErrores();
			$this->__generarGraficaPrioridadesViolaciones();
			$this->__generarGraficaPrioridadesViolacionesIntentoRealizado();
			*/
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
		
		//$violaciones_controller = new ViolacionesController();
		
		//if(empty($violaciones_controller->obtenerViolacionPorIntentoTipo($this->id_intento, "IL_INFINITE_LOOP"))){
		if(empty($this->obtenerViolacionPorIntentoTipo($this->id_intento, "IL_INFINITE_LOOP"))){
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
			$ficherosXml_controller->guardarDatosXmlErroresUnitarios($this->ruta_carpeta_id, $this->id_intento, 
																							 $this->intento_realizado);
		}
		else{
			$this->Flash->error(__('error desconocido'));
		}
							
	}
	
	/*
	private function __generarGraficaPrioridadesViolaciones(){
		
		include('/../../vendor/libchart/libchart/classes/libchart.php');
		
		$violaciones_existen = false;
		
		// INNER JOIN
		$query = $this->Intentos->find('all')
								->contain(['Violaciones'])
								->where(['alumno_id' => $_SESSION["lti_userId"]]);
		
		$chart = new \PieChart(600, 350);
		$dataSet = new \XYDataSet();
		
		foreach ($query as $intento){
			if(count($intento->violaciones) > 0){
				$violaciones_existen = true;
				foreach ($intento->violaciones as $violacion){
					$prioridad = $violacion->prioridad;
					$point = $dataSet->getPointWithX("Prioridad ".$prioridad);
					if($point == null){
						$dataSet->addPoint(new \Point("Prioridad ".$prioridad, 1));
					}else{
						$point->setY($point->getY() + 1);
					}
				}
			}
		}
		
		if($violaciones_existen){
			$chart->setDataSet($dataSet);
			$chart->setTitle("Porcentaje de las prioridades de las violaciones de código cometidas");
			$chart->render("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones.png");
		}
								
	}
	*/
	
	/*
	private function __generarGraficaPrioridadesViolacionesIntentoRealizado(){
		
		if(file_exists("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones_ultimoIntento.png")){
			unlink("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones_ultimoIntento.png");
		}
		$violaciones_controller = new ViolacionesController();
		$violaciones = $violaciones_controller->obtenerViolacionesPorIdIntento($this->id_intento);
		
		if(!empty($violaciones)){
			$chart = new \PieChart(600, 350);
			$dataSet = new \XYDataSet();
			$chart_barras = new \VerticalBarChart(600, 350);
			$dataSet_barras = new \XYDataSet();
			
			foreach ($violaciones as $violacion){
				$prioridad = $violacion->prioridad;
				$point = $dataSet->getPointWithX("Prioridad ".$prioridad);
				$point_barras = $dataSet_barras->getPointWithX("Prioridad ".$prioridad);
				if($point == null){
					$dataSet->addPoint(new \Point("Prioridad ".$prioridad, 1));
					$dataSet_barras->addPoint(new \Point("Prioridad ".$prioridad, 1));
				}else{
					$point->setY($point->getY() + 1);
					$point_barras->setY($point->getY() + 1);
				}
			}
			
			$chart->setDataSet($dataSet);
			$chart->setTitle("Porcentaje de las prioridades de las violaciones de código cometidas");
			$chart->render("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones_ultimoIntento.png");	
		
			$chart_barras->setDataSet($dataSet_barras);
			$chart_barras->setTitle("Número de violaciones cometidas por prioridad");
			$chart_barras->render("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones_ultimoIntento_barras.png");
		}
		
	}
	*/
	
	/*
	private function __generarGraficasViolacionesErrores(){
		
		include('/../../vendor/libchart/libchart/classes/libchart.php');
		
		$query_violaciones = $this->Intentos->find('all')
											->contain(['Violaciones'])
											->where(['alumno_id' => $_SESSION["lti_userId"]]);
		$query_errores = $this->Intentos->find('all')
										->contain(['Errores'])
										->where(['alumno_id' => $_SESSION["lti_userId"]]);
		
		// Línea
		$chart_linea = new \LineChart(600, 350);
		$serie1 = new \XYDataSet();
		$serie2 = new \XYDataSet();
		
		// Barras Violaciones
		$chart_violaciones = new \VerticalBarChart(600, 350);
		$total_intentos_violaciones = 0;
		$total_violaciones = 0;
		$dataSet_violaciones = new \XYDataSet();
		
		// Barras Errores
		$chart_errores = new \VerticalBarChart(700, 350);
		$serie_errores_unitarios = new \XYDataSet();
		$serie_errores_excepciones = new \XYDataSet();
		$total_intentos_errores = 0;
		$total_errores_unitarios = 0;
		$total_errores_excepcion = 0;
		
		// Línea Violaciones
		foreach ($query_violaciones as $intento) {
			$numero_violaciones = count($intento->violaciones);
			$serie1->addPoint(new \Point("Intento: ".$intento->numero_intento, $numero_violaciones));
			$dataSet_violaciones->addPoint(new \Point("Intento: ".$intento->numero_intento, $numero_violaciones));
			$total_intentos_violaciones++;
			$total_violaciones += $numero_violaciones;
		}
		
		// Línea Errores
		foreach ($query_errores as $intento) {
			$total_intentos_errores++;
			$num_errores_unitarios = 0;
			$num_errores_excepcion = 0;
			foreach ($intento->errores as $error){
				if($error->tipo_error == "failure"){
					$num_errores_unitarios++;
					$total_errores_unitarios += $num_errores_unitarios;
				}else{
					$num_errores_excepcion++;
					$total_errores_excepcion += $num_errores_excepcion;
				}
			}
			$serie_errores_unitarios->addPoint(new \Point("Intento: ".$intento->numero_intento, $num_errores_unitarios));
			$serie_errores_excepciones->addPoint(new \Point("Intento: ".$intento->numero_intento, $num_errores_excepcion));
			$numero_errores = count($intento->errores);
			$serie2->addPoint(new \Point("Intento: ".$intento->numero_intento, $numero_errores));
		}
		
		// Línea
		$dataSet = new \XYSeriesDataSet();
		$dataSet->addSerie("Violaciones de código", $serie1);
		$dataSet->addSerie("Errores unitarios", $serie2);
		$chart_linea->setDataSet($dataSet);
		$chart_linea->setTitle("Errores unitarios- Violaciones de código");
		$chart_linea->render("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-linea.png");
		
		// Barras Violaciones
		$dataSet_violaciones->addPoint(new \Point("Media", round($total_violaciones/$total_intentos_violaciones, 2)));
		$chart_violaciones->setDataSet($dataSet_violaciones);
		$chart_violaciones->setTitle("Número de violaciones de código cometidas");
		$chart_violaciones->render("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-violaciones.png");
		
		// Barras Errores
		$serie_errores_unitarios->addPoint(new \Point("Media", round($total_errores_unitarios/$total_intentos_errores, 2)));
		$serie_errores_excepciones->addPoint(new \Point("Media", round($total_errores_excepcion/$total_intentos_errores, 2)));
		$dataSet_errores = new \XYSeriesDataSet();
		$dataSet_errores->addSerie("Errores Unitarios", $serie_errores_unitarios);
		$dataSet_errores->addSerie("Excepciones", $serie_errores_excepciones);
		$chart_errores->setDataSet($dataSet_errores);
		$chart_errores->getPlot()->setGraphCaptionRatio(0.65);
		$chart_errores->setTitle("Errores cometidos");
		$chart_errores->render("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-errores_unitarios.png");
		
	}
	*/
	
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
	
	/*
	public function obtenerUltimoIntentoPorIdTareaAlumno($id_tarea, $id_alumno){
		
		return $this->Intentos->find('all')
							  ->where(['tarea_id' => $id_tarea, 'alumno_id' => $id_alumno])
							  ->last()
							  ->toArray();
		
	}
	
	public function obtenerIntentosPorIdTarea($id_tarea){
	
		return $this->Intentos->find('all')
							  ->where(['tarea_id' => $id_tarea]);
	
	}
	
	public function obtenerIntentosTestPasados($id_tarea, $id_alumno){
		
		return $this->Intentos->find('all')
							  ->where(['tarea_id' => $id_tarea, 'alumno_id' => $id_alumno, 'resultado' => 1]);
		
	}
	
	public function obtenerIntentosPorIdTareaAlumno($id_tarea, $id_alumno){
		
		return $this->Intentos->find('all')
							  ->where(['tarea_id' => $id_tarea, 'alumno_id' => $id_alumno]);
		
	}
	
	public function obtenerIntentosConViolaciones(){
		
		return $this->Intentos->find('all')
							  ->contain(['Violaciones'])
							  ->where(['alumno_id' => $_SESSION["lti_userId"]]);
		
	}
	
	public function obtenerIntentosConErrores(){
	
		return $this->Intentos->find('all')
							  ->contain(['Errores'])
							  ->where(['alumno_id' => $_SESSION["lti_userId"]]);
	
	}
	*/
	
}

?>