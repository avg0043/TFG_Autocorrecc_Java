<?php

namespace App\Controller;

class ProfesoresController extends AppController{
	
	/**
	 * Función que registra al profesor en el servicio web.
	 * Al registrarse obtendrá los parámetros LTI necesarios
	 * para poder configurar la tarea de tipo herramienta externa
	 * desde Moodle.
	 */
	public function registrarProfesor(){
		
		$nuevo_profesor = $this->Profesores->newEntity();
		
		if ($this->request->is('post')) {				
			$consumer_key = $this->__crearConsumerKey();
			$consumer_key_encriptada = $this->__encriptarCadena($consumer_key);
			$secret_encriptada = $this->__encriptarCadena($this->request->data['contraseña']);
			$nuevo_profesor->consumer_key = $consumer_key_encriptada;
			$nuevo_profesor->secret = $secret_encriptada;		
			$nuevo_profesor = $this->Profesores->patchEntity($nuevo_profesor, $this->request->data);
			
			if ($this->Profesores->save($nuevo_profesor)) {	
				return $this->redirect(['action' => 'mostrarParametrosLti', $this->request->data['correo']]);
			}
			$this->Flash->error(__('No ha sido posible registrar al profesor.'));		
		}
		$this->set('nuevo_profesor', $nuevo_profesor);
		
	}
	
	/**
	 * Función que encripta y devuelve la cadena pasada por parámetro.
	 *
	 * @param string $cadena	cadena a encriptar.
	 */
	private function __encriptarCadena($cadena){
	
		$key='clave_codificacion';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
		$encriptada = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $cadena, MCRYPT_MODE_CBC, md5(md5($key))));
		return $encriptada; //Devuelve el string encriptado
	
	}
	
	/*
	 private function __desencriptarCadena($cadena){
	
	 $key='clave_codificacion';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
	 $desencriptada = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($cadena), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	 return $desencriptada;  //Devuelve el string desencriptado
	
	 }
	 */
	
	/**
	 * Función que crea un consumer_key aleatoriamente, que se le va a entregar al
	 * profesor al registrarse.
	 */
	private function __crearConsumerKey(){
	
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$pass = array();
		$alphaLength = strlen($alphabet) - 1;
	
		for ($i = 0; $i < 7; $i++){
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass);
	}
	
	/**
	 * Función que guarda en una variable los parámetros LTI del profesor
	 * para que puedan ser mostrados desde su vista asociada.
	 *
	 * @param string $correo	correo del profesor
	 */
	public function mostrarParametrosLti($correo){
	
		$this->set('parametros', $this->Profesores->find('all')->where(['correo' => $correo]));
	
	}
	
	/**
	 * Función que guarda en una variable los datos del profesor actual para que
	 * puedan ser mostrados desde su vista asociada.
	 */
	public function mostrarDatosProfesor(){
	
		session_start();
		$this->comprobarSesion();
		
		$this->set('profesor', $this->Profesores->find('all')->where(['correo' => $_SESSION['lti_correo']]));
	
	}
	
	public function mostrarPanel(){
	
		session_start();
		$this->comprobarSesion();
		
	}
	
	public function generarReportePlagiosPracticas(){
		
		session_start();	
		if(!isset($_SESSION["lti_userId"])){
			return $this->redirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoLocal']);
		}
		//$this->__comprobarSesion();
		
		$alumnos_controller = new AlumnosController();
		$intentos_controller = new IntentosController();
		$tareas_controller = new TareasController();
		$paquete = $tareas_controller->obtenerTareaPorId($_SESSION["lti_idTarea"])[0]->paquete;
		$paquete_ruta = str_replace('.', '\\', $paquete);
		$alumnos = $alumnos_controller->obtenerAlumnos();
		$reporte_jplag_generado = false;
		$numero_practicas_subidas = 0;
		$ruta_carpeta_tarea = "../../".$_SESSION["lti_idCurso"]."/".$_SESSION["lti_idTarea"]."/";
		
		if(is_dir("../../plagios/")){
			exec('cd ' . "../../" . ' && rmdir plagios /s /q');	// borrar plagios
		}
		mkdir("../../plagios/practicas", 0777, true);
		
		$alumnos_con_practicas = [];
		foreach ($alumnos as $alumno):
			$ultimo_intento = $intentos_controller->obtenerUltimoIntentoPorIdTareaAlumno($_SESSION["lti_idTarea"], $alumno->id);
			if(!empty($ultimo_intento)){
				array_push($alumnos_con_practicas, $alumno->nombre." ".$alumno->apellidos);
				$numero_practicas_subidas++;
				mkdir("../../plagios/practicas/".utf8_decode($alumno->nombre.$alumno->apellidos), 0777, true);
				exec('xcopy ' . str_replace('/', '\\', $ruta_carpeta_tarea)."Learner\\"
						      . $ultimo_intento['alumno_id']."\\". $ultimo_intento['numero_intento']."\\".$paquete_ruta . ' '
							  . "..\\..\\plagios\\practicas\\".utf8_decode($alumno->nombre.$alumno->apellidos)."\\" . ' /s /e');
			}	
		endforeach;
		
		if($numero_practicas_subidas >= 2){
			$reporte_jplag_generado = true;
			exec('cd ../../plagios && java -jar ../AutoCorreccionJava_TFG/vendor/jplag-2.11.8-SNAPSHOT-jar-with-dependencies.jar -l java17 -r reporte/ -s practicas/');
			$this->Flash->success(__('El reporte de plagios ha sido generado'));
		}else{
			$this->Flash->error(__('No ha sido posible generar el reporte de los plagios'));
		}
		
		$this->set('alumnos_con_practicas', $alumnos_con_practicas);
		$this->set('numero_practicas_subidas', $numero_practicas_subidas);
		$this->set('reporte_generado', $reporte_jplag_generado);
		
	}
	
	public function descargarPracticasAlumnos(){
		
		session_start();
		if(!isset($_SESSION["lti_userId"])){
			return $this->redirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoLocal']);
		}
		//$this->comprobarSesion();
		
		$alumnos_controller = new AlumnosController();
		$intentos_controller = new IntentosController();
		$this->set('alumnos', $alumnos_controller->obtenerAlumnos());
		$this->set('intentos', $intentos_controller->obtenerIntentosPorIdTarea($_SESSION["lti_idTarea"]));
		
	}
	
	public function generarGraficas(){
		
		include('/../../vendor/libchart/libchart/classes/libchart.php');
		
		session_start();
		if(!isset($_SESSION["lti_userId"])){
			return $this->redirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoLocal']);
		}
		//$this->comprobarSesion();
		
		$alumnos_controler = new AlumnosController();
		$intentos_controller = new IntentosController();
		$graficas_controller = new GraficasController();
		$alumnos = $alumnos_controler->obtenerAlumnos();
		$alumnos_intentos = array();
		foreach ($alumnos as $alumno){
			$intentos = $intentos_controller->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $alumno->id);
			if(!$intentos->isEmpty()){
				$alumnos_intentos[$alumno->id] = $alumno->nombre." ".$alumno->apellidos;
			}
		}
		$this->set("alumnos_intentos", $alumnos_intentos);
		
		$_SESSION["grafica_medias_globales"] = false;
		$_SESSION["grafica_promedio_errores_violaciones"] = false;
		$_SESSION["grafica_media_errores"] = false;
		$_SESSION["grafica_alumnos_violaciones"] = false;
		$_SESSION["grafica_alumnos_intentos"] = false;
		$_SESSION["grafica_alumnos_test"] = false;
		$_SESSION["dropdown"] = false;
		
		if ($this->request->is('post')) {
			if($this->request->data["MediasGlobales"]){
				$_SESSION["grafica_medias_globales"] = true;
				$graficas_controller->generarGraficaMedias();
				//$this->__calcularMediaIntentos();
				//$this->__calcularMediaViolaciones();
				//$this->__generarGraficaMedias();
			}
			if($this->request->data["MediaViolacionesErrores"]){
				$_SESSION["grafica_promedio_errores_violaciones"] = true;
				$graficas_controller->generarGraficaLineaPromedioErroresUnitariosViolaciones();
				//$this->__generarGraficaLineaPromedioErroresUnitariosViolaciones();
			}
			if($this->request->data["MediaErrores"]){
				$_SESSION["grafica_media_errores"] = true;
				$graficas_controller->generarGraficaMediaErrores();
				//$this->__generarGraficaMediaErrores();
			}
			if($this->request->data["AlumnosViolaciones"]){
				$_SESSION["grafica_alumnos_violaciones"] = true;
				$graficas_controller->generarGraficaVerticalAlumnosViolacionesCometidas();
				//$this->__generarGraficaVerticalAlumnosViolacionesCometidas();			
			}
			if($this->request->data["AlumnosIntentos"]){
				$_SESSION["grafica_alumnos_intentos"] = true;
				$graficas_controller->generarGraficaVerticalAlumnosIntentos();
				//$this->__generarGraficaVerticalAlumnosIntentos();
			}
			if($this->request->data["AlumnosTest"]){
				$_SESSION["grafica_alumnos_test"] = true;
				$graficas_controller->generarGraficaAlumnosTest();
				//$this->__generarGraficaAlumnosTest();
			}
			if($this->request->data["Todas"]){
				$_SESSION["grafica_medias_globales"] = true;
				$_SESSION["grafica_promedio_errores_violaciones"] = true;
				$_SESSION["grafica_media_errores"] = true;
				$_SESSION["grafica_alumnos_violaciones"] = true;
				$_SESSION["grafica_alumnos_intentos"] = true;
				$_SESSION["grafica_alumnos_test"] = true;
				$graficas_controller->generarGraficaMedias();
				$graficas_controller->generarGraficaLineaPromedioErroresUnitariosViolaciones();
				$graficas_controller->generarGraficaVerticalAlumnosViolacionesCometidas();
				$graficas_controller->generarGraficaVerticalAlumnosIntentos();
				$graficas_controller->generarGraficaAlumnosTest();
				$graficas_controller->generarGraficaMediaErrores();
				/*
				$this->__calcularMediaIntentos();
				$this->__calcularMediaViolaciones();
				$this->__generarGraficaMedias();
				$this->__generarGraficaLineaPromedioErroresUnitariosViolaciones();
				$this->__generarGraficaVerticalAlumnosViolacionesCometidas();
				$this->__generarGraficaVerticalAlumnosIntentos();
				$this->__generarGraficaAlumnosTest();
				$this->__generarGraficaMediaErrores();
				*/
			}
			if($this->request->data["field"]){
				$_SESSION["dropdown"] = true;
				$id_alumno = $this->request->data["field"];
				$this->set("id_alumno", $id_alumno);
			}
			
			if(!$_SESSION["grafica_medias_globales"] && !$_SESSION["grafica_promedio_errores_violaciones"] &&
				!$_SESSION["grafica_alumnos_violaciones"] && !$_SESSION["grafica_alumnos_intentos"] &&
				!$_SESSION["grafica_alumnos_test"] && !$_SESSION["dropdown"] && !$_SESSION["grafica_media_errores"]){
					$this->Flash->error(__('Debes de seleccionar una de las opciones'));
			}
		}
		
	}
	
	private function __generarGraficaLineaPromedioErroresUnitariosViolaciones(){
		
		$alumnos_controller = new AlumnosController();
		$intentos_controller = new IntentosController();
		$violaciones_controller = new ViolacionesController();
		$errores_controller = new ErroresController();
		
		$chart = new \LineChart(800, 350);
		$serie_violaciones = new \XYDataSet();
		$serie_errores = new \XYDataSet();
		
		$num_alumnos_por_intento = array();
		$alumnos = $alumnos_controller->obtenerAlumnos();
		$intento_realizado = false;
		
		foreach ($alumnos as $alumno){
			$intentos = $intentos_controller->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $alumno->id);
			foreach ($intentos as $intento){
				$intento_realizado = true;
				$clave = "Intento ".$intento->numero_intento;
				if(array_key_exists($clave, $num_alumnos_por_intento)){
					$num_alumnos_por_intento[$clave] += 1;
				}
				else {
					$num_alumnos_por_intento[$clave] = 1;
				}
				
				// Violaciones
				$num_violaciones = count($violaciones_controller->obtenerViolacionesPorIdIntento($intento->id));
				$point_violacion = $serie_violaciones->getPointWithX($clave);
				if($point_violacion != null){
					$point_violacion->setY(($point_violacion->getY() + $num_violaciones) / $num_alumnos_por_intento[$clave]);
				}else{
					$serie_violaciones->addPoint(new \Point($clave, $num_violaciones));
				}
				
				// Errores
				$num_errores = count($errores_controller->obtenerErroresPorIdIntento($intento->id));
				$point_error = $serie_errores->getPointWithX($clave);
				if($point_error != null){
					$point_error->setY(($point_error->getY() + $num_errores) / $num_alumnos_por_intento[$clave]);
				}else{
					$serie_errores->addPoint(new \Point($clave, $num_errores));
				}
			}
		}
		
		if($intento_realizado){
			$dataSet = new \XYSeriesDataSet();
			$dataSet->addSerie("Violaciones de código", $serie_violaciones);
			$dataSet->addSerie("Errores unitarios", $serie_errores);
			$chart->setDataSet($dataSet);
			$chart->setTitle("Promedio de la clase de Violaciones-Errores por intento realizado");
			$chart->render("img/".$_SESSION["lti_idTarea"]."-prof-promedioViolacionesErrores.png");
		}
		
	}
	
	/*
	private function __generarGraficaMediaErrores(){
		
		$alumnos_controller = new AlumnosController();
		$intentos_controller = new IntentosController();
		$errores_controller = new ErroresController();
		
		$chart = new \VerticalBarChart(800, 350);
		$serie_errores_unitarios = new \XYDataSet();
		$serie_errores_excepcion = new \XYDataSet();
		
		$num_alumnos_por_intento = array();
		$alumnos = $alumnos_controller->obtenerAlumnos();
		$intento_realizado = false;
		
		foreach ($alumnos as $alumno){
			$intentos = $intentos_controller->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $alumno->id);
			foreach ($intentos as $intento){
				$intento_realizado = true;
				$clave = "Intento ".$intento->numero_intento;
				if(array_key_exists($clave, $num_alumnos_por_intento)){
					$num_alumnos_por_intento[$clave] += 1;
				}
				else {
					$num_alumnos_por_intento[$clave] = 1;
				}
		
				// Errores
				$num_errores_unitarios = 0;
				$num_errores_excepcion = 0;
				$errores = $errores_controller->obtenerErroresPorIdIntento($intento->id);
				foreach ($errores as $error){
					if($error->tipo_error == "failure"){
						$num_errores_unitarios++;
					}else{
						$num_errores_excepcion++;
					}
				}
				
				$point_error_unitario = $serie_errores_unitarios->getPointWithX($clave);
				if($point_error_unitario != null){
					$point_error_unitario->setY(($point_error_unitario->getY() + $num_errores_unitarios) / $num_alumnos_por_intento[$clave]);
				}else{
					$serie_errores_unitarios->addPoint(new \Point($clave, $num_errores_unitarios));
				}
				
				$point_error_excepcion = $serie_errores_excepcion->getPointWithX($clave);
				if($point_error_excepcion != null){
					$point_error_excepcion->setY(($point_error_excepcion->getY() + $num_errores_excepcion) / $num_alumnos_por_intento[$clave]);
				}else{
					$serie_errores_excepcion->addPoint(new \Point($clave, $num_errores_excepcion));
				}
				
			}
		}
		
		if($intento_realizado){
			$dataSet = new \XYSeriesDataSet();
			$dataSet->addSerie("Unitarios", $serie_errores_unitarios);
			$dataSet->addSerie("Excepciones", $serie_errores_excepcion);
			$chart->setDataSet($dataSet);
			$chart->getPlot()->setGraphCaptionRatio(0.75);
			$chart->setTitle("Promedio de errores cometidos por intento");
			$chart->render("img/".$_SESSION["lti_idTarea"]."-prof-promedioErroresUnitariosExcepciones.png");
		}
		
	}
	*/
	
	/*
	private function __generarGraficaAlumnosTest(){
		
		$alumnos_controller = new AlumnosController();
		$intentos_controller = new IntentosController();
		$alumnos = $alumnos_controller->obtenerAlumnos();
		$alumnos_registrados = false;
		$alumnos_pasan_test = 0;
		$alumnos_no_pasan_test = 0;
		
		foreach($alumnos as $alumno){
			$alumnos_registrados = true;
			$intentos_pasan_test = $intentos_controller->obtenerIntentosTestPasados($_SESSION["lti_idTarea"], $alumno->id);
			if($intentos_pasan_test->isEmpty()){
				$alumnos_no_pasan_test++;
			}
			else{
				$alumnos_pasan_test++;
			}
		}
		
		if($alumnos_registrados){	// hay alumnos
			$chart = new \PieChart(800, 350);
			$dataSet = new \XYDataSet();
			$dataSet->addPoint(new \Point("Alumnos que pasan los test", $alumnos_pasan_test));
			$dataSet->addPoint(new \Point("Alumnos que no pasan los test", $alumnos_no_pasan_test));
			$chart->setDataSet($dataSet);
			$chart->setTitle("Porcentaje de alumnos que pasan y no pasan los test");
			$chart->render("img/".$_SESSION["lti_idTarea"]."-prof-test.png");
		}
		$this->set("alumnos_registrados", $alumnos_registrados);
		
	}
	*/
	
	private function __calcularMediaIntentos(){
		
		$alumnos_controller = new AlumnosController();
		$intentos_controller = new IntentosController();
		$alumnos = $alumnos_controller->obtenerAlumnos();
		
		$_SESSION["media_intentos_pasa_test"] = false;
		$_SESSION["media_intentos_no_pasa_test"] = false;
		$intentos_pasa_test = false;
		$intentos_no_pasa_test = false;
		$num_alumnos_pasan_test = 0;
		$num_alumnos_no_pasan_test = 0;
		$total_intentos_pasan_test = 0;
		$total_intentos_no_pasan_test = 0;
		
		/*
		$chart_pasa_test = new \HorizontalBarChart(600, 350);
		$dataSet_pasa_test = new \XYDataSet();
		$chart_no_pasa_test = new \HorizontalBarChart(600, 350);
		$dataSet_no_pasa_test = new \XYDataSet();
		*/
		
		if(file_exists("img/".$_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest.png")){
			unlink("img/".$_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest.png");
		}
		
		foreach($alumnos as $alumno){
			$intentos_alumno = $intentos_controller->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $alumno->id);
			if(!$intentos_alumno->isEmpty()){
				$num_intentos_realizados = 0;
				$test_pasados = false;
				foreach($intentos_alumno as $intento){
					$num_intentos_realizados++;
					if($intento->resultado == 1){	// Test pasados
						$intentos_pasa_test = true;
						$test_pasados = true;
						//$dataSet_pasa_test->addPoint(new \Point($alumno->apellidos, $num_intentos_realizados));
						$num_alumnos_pasan_test++;
						$total_intentos_pasan_test += $num_intentos_realizados;
						break;
					}
				}
				if(!$test_pasados){		// Test no pasados
					$intentos_no_pasa_test = true;
					//$dataSet_no_pasa_test->addPoint(new \Point($alumno->apellidos, $num_intentos_realizados));
					$num_alumnos_no_pasan_test++;
					$total_intentos_no_pasan_test += $num_intentos_realizados;
				}
			}
		}
		
		if($intentos_pasa_test){
			$_SESSION["media_intentos_pasa_test"] = round($total_intentos_pasan_test/$num_alumnos_pasan_test, 2);
			/*
			$dataSet_pasa_test->addPoint(new \Point("Media", $_SESSION["media_intentos_pasa_test"]));
			$chart_pasa_test->setDataSet($dataSet_pasa_test);
			$chart_pasa_test->getPlot()->setGraphPadding(new \Padding(5, 30, 20, 140));
			$chart_pasa_test->setTitle("Número de intentos mínimo realizados para pasar los test");
			*/
			//$chart_pasa_test->render("img/".$_SESSION["lti_idTarea"]."-prof-intentos_pasaTest.png");		
		}
		if($intentos_no_pasa_test){
			$_SESSION["media_intentos_no_pasa_test"] = round($total_intentos_no_pasan_test/$num_alumnos_no_pasan_test, 2);
			/*
			$dataSet_no_pasa_test->addPoint(new \Point("Media", $_SESSION["media_intentos_no_pasa_test"]));
			$chart_no_pasa_test->setDataSet($dataSet_no_pasa_test);
			$chart_no_pasa_test->getPlot()->setGraphPadding(new \Padding(5, 30, 20, 140));
			$chart_no_pasa_test->setTitle("Número de intentos realizados sin conseguir pasar los test");
			*/
			//$chart_no_pasa_test->render("img/".$_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest.png");
		}
		
	}
	
	/*
	private function __generarGraficaVerticalAlumnosIntentos(){
	
		$alumnos_controller = new AlumnosController();
		$intentos_controller = new IntentosController();
		$alumnos = $alumnos_controller->obtenerAlumnos();
	
		$intentos_pasa_test = false;
		$intentos_no_pasa_test = false;
	
		$chart_pasa_test = new \VerticalBarChart(800, 350);
		$dataSet_pasa_test = new \XYDataSet();
		$this->__añadirIntervalosIntentosEjeX($dataSet_pasa_test);
		
		$chart_no_pasa_test = new \VerticalBarChart(800, 350);
		$dataSet_no_pasa_test = new \XYDataSet();
		$this->__añadirIntervalosIntentosEjeX($dataSet_no_pasa_test);
	
		if(file_exists("img/".$_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest.png")){
			unlink("img/".$_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest.png");
		}
	
		foreach($alumnos as $alumno){
			$intentos_alumno = $intentos_controller->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $alumno->id);
			if(!$intentos_alumno->isEmpty()){
				$num_intentos_realizados = 0;
				$test_pasados = false;
				foreach($intentos_alumno as $intento){
					$num_intentos_realizados++;
					if($intento->resultado == 1){	// Test pasados
						$intentos_pasa_test = true;
						$test_pasados = true;
						/////////////////////////////////////////
						$intervalo = $this->__obtenerIntervaloIntento($num_intentos_realizados);
						$point = $dataSet_pasa_test->getPointWithX($intervalo);
						$point->setY($point->getY() + 1);
						/////////////////////////////////////////
						break;
					}
				}
				if(!$test_pasados){		// Test no pasados
					$intentos_no_pasa_test = true;
					$intervalo = $this->__obtenerIntervaloIntento($num_intentos_realizados);
					$point = $dataSet_no_pasa_test->getPointWithX($intervalo);
					$point->setY($point->getY() + 1);
				}
			}
		}
	
		if($intentos_pasa_test){
			$chart_pasa_test->setDataSet($dataSet_pasa_test);
			$chart_pasa_test->setTitle("Número de alumnos que han realizado X intentos para pasar los test");
			$chart_pasa_test->render("img/".$_SESSION["lti_idTarea"]."-prof-intentos_pasaTest_intervalos.png");
		}
		if($intentos_no_pasa_test){
			$chart_no_pasa_test->setDataSet($dataSet_no_pasa_test);
			$chart_no_pasa_test->setTitle("Número de alumnos que han realizado X intentos sin conseguir pasar los test");
			$chart_no_pasa_test->render("img/".$_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest_intervalos.png");
		}
	
	}
	*/
	
	private function __calcularMediaViolaciones(){
	
		$alumnos_controller = new AlumnosController();
		$intentos_controller = new IntentosController();
		$violaciones_controller = new ViolacionesController();
		$alumnos = $alumnos_controller->obtenerAlumnos();
		
		$_SESSION["media_violaciones"] = false;
		$intentos_realizados = false;
		$total_violaciones = 0;
		$num_alumnos = 0;
		
		/*
		$chart = new \HorizontalBarChart(600, 350);
		$dataSet = new \XYDataSet();
		*/
	
		foreach($alumnos as $alumno){
			$intentos_alumno = $intentos_controller->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $alumno->id);
			if(!$intentos_alumno->isEmpty()){
				$intentos_realizados = true;
				$total_violaciones_alumno = 0;
				$num_alumnos += 1;
				foreach($intentos_alumno as $intento){
					$violaciones = $violaciones_controller->obtenerViolacionesPorIdIntento($intento->id);
					$total_violaciones_alumno += count($violaciones);
				}
				//$dataSet->addPoint(new \Point($alumno->apellidos, $total_violaciones_alumno));
				$total_violaciones += $total_violaciones_alumno;
			}
		}
	
		if($intentos_realizados){
			$_SESSION["media_violaciones"] = round($total_violaciones/$num_alumnos, 2);
			/*
			$dataSet->addPoint(new \Point("Media", $_SESSION["media_violaciones"]));
			$chart->setDataSet($dataSet);
			$chart->getPlot()->setGraphPadding(new \Padding(5, 30, 20, 140));
			$chart->setTitle("Número de violaciones de código cometidas");
			*/
			//$chart->render("img/".$_SESSION["lti_idTarea"]."-prof-violaciones.png");
		}
	
	}
	
	private function __generarGraficaVerticalAlumnosViolacionesCometidas(){
		
		$alumnos_controller = new AlumnosController();
		$intentos_controller = new IntentosController();
		$violaciones_controller = new ViolacionesController();
		$alumnos = $alumnos_controller->obtenerAlumnos();
		$intentos_realizados = false;
		
		$chart = new \VerticalBarChart(800, 350);
		$dataSet = new \XYDataSet();
		$this->__añadirIntervalosViolacionesEjeX($dataSet);
		
		foreach($alumnos as $alumno){
			$intentos_alumno = $intentos_controller->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $alumno->id);
			if(!$intentos_alumno->isEmpty()){
				$intentos_realizados = true;
				$total_violaciones = 0;
				foreach($intentos_alumno as $intento){
					$violaciones = $violaciones_controller->obtenerViolacionesPorIdIntento($intento->id);
					$total_violaciones += count($violaciones);	
				}
				$intervalo = $this->__obtenerIntervaloViolacion($total_violaciones);
				$point = $dataSet->getPointWithX($intervalo);
				$point->setY($point->getY() + 1);
			}
		}
		
		if($intentos_realizados){
			$chart->setDataSet($dataSet);
			$chart->setTitle("Número de alumnos que cometen X violaciones");
			$chart->render("img/".$_SESSION["lti_idTarea"]."-prof-violaciones_intervalos.png");
		}
		
	}
	
	/*
	private function __generarGraficaMedias(){
		
		$chart = new \HorizontalBarChart(800, 350);
		$dataSet = new \XYDataSet();
		
		if($_SESSION["media_intentos_no_pasa_test"] != false)
			$dataSet->addPoint(new \Point("Intentos sin pasar los test", $_SESSION["media_intentos_no_pasa_test"]));
		if($_SESSION["media_intentos_pasa_test"] != false)
			$dataSet->addPoint(new \Point("Intentos para pasar los test", $_SESSION["media_intentos_pasa_test"]));
		if($_SESSION["media_violaciones"] != false)
			$dataSet->addPoint(new \Point("Violaciones de código", $_SESSION["media_violaciones"]));
		
		if($_SESSION["media_intentos_no_pasa_test"] != false || $_SESSION["media_intentos_pasa_test"] != false ||
																$_SESSION["media_violaciones"] != false){
			$chart->setDataSet($dataSet);
			$chart->getPlot()->setGraphPadding(new \Padding(5, 30, 20, 140));
			$chart->setTitle("MEDIAS");
			$chart->render("img/".$_SESSION["lti_idTarea"]."-prof-medias.png");
		}
		
	}
	*/
	
	/*
	private function __añadirIntervalosViolacionesEjeX($dataSet){
		
		$dataSet->addPoint(new \Point("[0]", 0));
		$dataSet->addPoint(new \Point("[1,10]", 0));
		$dataSet->addPoint(new \Point("[11,20]", 0));
		$dataSet->addPoint(new \Point("[21,30]", 0));
		$dataSet->addPoint(new \Point("[31,40]", 0));
		$dataSet->addPoint(new \Point("[41,50]", 0));
		$dataSet->addPoint(new \Point("[51,60]", 0));
		$dataSet->addPoint(new \Point("[61,70]", 0));
		$dataSet->addPoint(new \Point("[71,80]", 0));
		$dataSet->addPoint(new \Point("[81,+]", 0));
		
	}
	
	private function __añadirIntervalosIntentosEjeX($dataSet){
	
		$dataSet->addPoint(new \Point("[0]", 0));
		$dataSet->addPoint(new \Point("[1,3]", 0));
		$dataSet->addPoint(new \Point("[4,6]", 0));
		$dataSet->addPoint(new \Point("[7,9]", 0));
		$dataSet->addPoint(new \Point("[10,12]", 0));
		$dataSet->addPoint(new \Point("[13,15]", 0));
		$dataSet->addPoint(new \Point("[16,18]", 0));
		$dataSet->addPoint(new \Point("[19,21]", 0));
		$dataSet->addPoint(new \Point("[22,24]", 0));
		$dataSet->addPoint(new \Point("[25,+]", 0));
	
	}
	
	private function __obtenerIntervaloViolacion($valor){
		
		switch($valor){
			case 0:
				return "[0]";
				break;
			case $valor >= 1 && $valor <= 10:
				return "[1,10]";
				break;
			case $valor >= 11 && $valor <= 20:
				return "[11,20]";
				break;
			case $valor >= 21 && $valor <= 30:
				return "[21,30]";
				break;
			case $valor >= 31 && $valor <= 40:
				return "[31,40]";
				break;
			case $valor >= 41 && $valor <= 50:
				return "[41,50]";
				break;
			case $valor >= 51 && $valor <= 60:
				return "[51,60]";
				break;
			case $valor >= 61 && $valor <= 70:
				return "[61,70]";
				break;
			case $valor >= 71 && $valor <= 80:
				return "[71,80]";
				break;
			case $valor >= 81:
				return "[81,+]";
				break;
		}
		
	}
	
	private function __obtenerIntervaloIntento($valor){
		
		switch($valor){
			case 0:
				return "[0]";
				break;
			case $valor >= 1 && $valor <= 3:
				return "[1,3]";
				break;
			case $valor >= 4 && $valor <= 6:
				return "[4,6]";
				break;
			case $valor >= 7 && $valor <= 9:
				return "[7,9]";
				break;
			case $valor >= 10 && $valor <= 12:
				return "[10,12]";
				break;
			case $valor >= 13 && $valor <= 15:
				return "[13,15]";
				break;
			case $valor >= 16 && $valor <= 18:
				return "[16,18]";
				break;
			case $valor >= 19 && $valor <= 21:
				return "[19,21]";
				break;
			case $valor >= 22 && $valor <= 24:
				return "[22,24]";
				break;
			case $valor >= 25:
				return "[25,+]";
				break;
		}
		
	}
	*/
	
	/*
	public function seleccionarGraficas(){
		session_start();
		$_SESSION["violaciones"] = false;
		if ($this->request->is('post')) {
			if($this->request->data["Violaciones"]){
				$_SESSION["violaciones"] = true;
			}
			if($this->request->data["Medias"]){
			}
		}
	}
	*/
	
	public function obtenerProfesorPorKeyCorreo($consumer_key, $correo){
		
		return $this->Profesores->find('all')
					            ->where(['consumer_key' => $consumer_key, 'correo' => $correo])
								->toArray();
		
	}
	
	public function obtenerProfesorPorKey($consumer_key){
		
		return $this->Profesores->find('all')
								->where(['consumer_key' => $consumer_key])
								->toArray();
		
	}
	
	public function obtenerProfesorPorCorreo($correo){
		
		return $this->Profesores->find('all')
								->where(['correo' => $correo])
								->toArray();
		
	}
	
}

?>