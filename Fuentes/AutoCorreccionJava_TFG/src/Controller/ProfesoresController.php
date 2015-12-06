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
		$this->comprobarSesion();
		
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
			$ultimo_intento = $intentos_controller->obtenerUltimoIntentoPorIdAlumno($alumno->id);
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
		$this->comprobarSesion();
		
		$alumnos_controller = new AlumnosController();
		$intentos_controller = new IntentosController();
		$this->set('alumnos', $alumnos_controller->obtenerAlumnos());
		$this->set('intentos', $intentos_controller->obtenerIntentosPorIdTarea($_SESSION["lti_idTarea"]));
		
	}
	
	public function generarGraficas(){
		
		include('/../../vendor/libchart/libchart/classes/libchart.php');
		session_start();
		
		$this->__generarGraficaAlumnosTest();
		$this->__generarGraficaAlumnosIntentos();
		$this->__generarGraficaAlumnosViolacionesCometidas();
		
	}
	
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
			$chart = new \PieChart(600, 350);
			$dataSet = new \XYDataSet();
			$dataSet->addPoint(new \Point("Alumnos que pasan los test", $alumnos_pasan_test));
			$dataSet->addPoint(new \Point("Alumnos que no pasan los test", $alumnos_no_pasan_test));
			$chart->setDataSet($dataSet);
			$chart->setTitle("Porcentaje de alumnos que pasan y no pasan los test");
			$chart->render("img/".$_SESSION["lti_idTarea"]."-prof-alumnos_test.png");
		}
		$this->set("alumnos_registrados", $alumnos_registrados);
		
	}
	
	private function __generarGraficaAlumnosIntentos(){
		
		$alumnos_controller = new AlumnosController();
		$intentos_controller = new IntentosController();
		$alumnos = $alumnos_controller->obtenerAlumnos();
		$intentos_pasa_test = false;
		$intentos_no_pasa_test = false;
		$num_alumnos_pasan_test = 0;
		$num_alumnos_no_pasan_test = 0;
		$total_intentos_pasan_test = 0;
		$total_intentos_no_pasan_test = 0;
		$chart_pasa_test = new \VerticalBarChart(600, 350);
		$dataSet_pasa_test = new \XYDataSet();
		$chart_no_pasa_test = new \VerticalBarChart(600, 350);
		$dataSet_no_pasa_test = new \XYDataSet();
		
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
						$dataSet_pasa_test->addPoint(new \Point($alumno->apellidos, $num_intentos_realizados));
						$num_alumnos_pasan_test++;
						$total_intentos_pasan_test += $num_intentos_realizados;
						break;
					}
				}
				if(!$test_pasados){		// Test no pasados
					$intentos_no_pasa_test = true;
					$dataSet_no_pasa_test->addPoint(new \Point($alumno->apellidos, $num_intentos_realizados));
					$num_alumnos_no_pasan_test++;
					$total_intentos_no_pasan_test += $num_intentos_realizados;
				}
			}
		}
		
		if($intentos_pasa_test){
			$dataSet_pasa_test->addPoint(new \Point("Media", $total_intentos_pasan_test/$num_alumnos_pasan_test));
			$chart_pasa_test->setDataSet($dataSet_pasa_test);
			$chart_pasa_test->setTitle("Número de intentos mínimo realizados para pasar los test");
			$chart_pasa_test->render("img/".$_SESSION["lti_idTarea"]."-prof-intentos_pasaTest.png");		
		}
		if($intentos_no_pasa_test){
			$dataSet_no_pasa_test->addPoint(new \Point("Media", $total_intentos_no_pasan_test/$num_alumnos_no_pasan_test));
			$chart_no_pasa_test->setDataSet($dataSet_no_pasa_test);
			$chart_no_pasa_test->setTitle("Número de intentos realizados sin conseguir pasar los test");
			$chart_no_pasa_test->render("img/".$_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest.png");
		}
		
	}
	
	private function __generarGraficaAlumnosViolacionesCometidas(){
		
		$alumnos_controller = new AlumnosController();
		$intentos_controller = new IntentosController();
		$violaciones_controller = new ViolacionesController();
		$alumnos = $alumnos_controller->obtenerAlumnos();
		$intentos_realizados = false;
		$chart = new \VerticalBarChart(600, 350);
		$dataSet = new \XYDataSet();
		
		foreach($alumnos as $alumno){
			$intentos_alumno = $intentos_controller->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $alumno->id);
			if(!$intentos_alumno->isEmpty()){
				$intentos_realizados = true;
				$total_violaciones = 0;
				foreach($intentos_alumno as $intento){
					$violaciones = $violaciones_controller->obtenerViolacionesPorIdIntento($intento->id);
					$total_violaciones += count($violaciones);	
				}
				$dataSet->addPoint(new \Point($alumno->apellidos, $total_violaciones));
			}
		}
		
		if($intentos_realizados){
			$chart->setDataSet($dataSet);
			$chart->setTitle("Número de violaciones de código cometidas");
			$chart->render("img/".$_SESSION["lti_idTarea"]."-prof-violaciones_alumnos.png");
		}
		
	}
	
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