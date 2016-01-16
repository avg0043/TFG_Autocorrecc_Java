<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;

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
		
		//$paquete = $this->obtenerTareaPorId($_SESSION["lti_idTarea"])[0]->paquete;
		$tareas_tabla = TableRegistry::get("Tareas");
		$query = $tareas_tabla->find('all')
							  ->where(['id' => $_SESSION["lti_idTarea"]])
							  ->toArray();
		$paquete = $query[0]->paquete;
		
		$paquete_ruta = str_replace('.', '\\', $paquete);
		//$alumnos = $this->obtenerAlumnos();
		$alumnos_tabla = TableRegistry::get("Alumnos");
		$alumnos = $alumnos_tabla->find('all');
		$reporte_jplag_generado = false;
		$numero_practicas_subidas = 0;
		$ruta_carpeta_tarea = "../../".$_SESSION["lti_idCurso"]."/".$_SESSION["lti_idTarea"]."/";
		
		if(is_dir("../../plagios/")){
			exec('cd ' . "../../" . ' && rmdir plagios /s /q');	// borrar plagios
		}
		mkdir("../../plagios/practicas", 0777, true);
		
		$intentos_tabla = TableRegistry::get("Intentos");
		$alumnos_con_practicas = [];
		foreach ($alumnos as $alumno):
			//$intentos_alumno = $this->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $alumno->id);
			$intentos_alumno = $intentos_tabla->find('all')
    										  ->where(['tarea_id' => $_SESSION["lti_idTarea"], 'alumno_id' => $alumno->id]);
		
			if(!$intentos_alumno->isEmpty()){
				//$ultimo_intento = $this->obtenerUltimoIntentoPorIdTareaAlumno($_SESSION["lti_idTarea"], $alumno->id);
				//$intentos_tabla = TableRegistry::get("Intentos");
				$ultimo_intento = $intentos_tabla->find('all')
										    	 ->where(['tarea_id' => $_SESSION["lti_idTarea"], 'alumno_id' => $alumno->id])
										    	 ->last()
										    	 ->toArray();
				
				if(!empty($ultimo_intento)){
					array_push($alumnos_con_practicas, $alumno->nombre." ".$alumno->apellidos);
					$numero_practicas_subidas++;
					mkdir("../../plagios/practicas/".utf8_decode($alumno->nombre.$alumno->apellidos), 0777, true);
					exec('xcopy ' . str_replace('/', '\\', $ruta_carpeta_tarea)."Learner\\"
							      . $ultimo_intento['alumno_id']."\\". $ultimo_intento['numero_intento']."\\".$paquete_ruta . ' '
								  . "..\\..\\plagios\\practicas\\".utf8_decode($alumno->nombre.$alumno->apellidos)."\\" . ' /s /e');
				}
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
		
		$alumnos_tabla = TableRegistry::get("Alumnos");
		$intentos_tabla = TableRegistry::get("Intentos");
		
		$this->set('intentos_todos', $intentos_tabla->find('all'));
		//$this->set('alumnos', $this->obtenerAlumnos());
		$this->set('alumnos', $alumnos_tabla->find('all'));
		//$this->set('intentos', $this->obtenerIntentosPorIdTarea($_SESSION["lti_idTarea"]));
		$this->set('intentos', $intentos_tabla->find('all')->where(['tarea_id' => $_SESSION["lti_idTarea"]]));
		
	}
	
	public function generarGraficas(){
		
		include('/../../vendor/libchart/libchart/classes/libchart.php');
		
		session_start();
		if(!isset($_SESSION["lti_userId"])){
			return $this->redirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoLocal']);
		}
		//$this->comprobarSesion();
		
		$graficas_controller = new GraficasController();
		//$alumnos = $this->obtenerAlumnos();
		$alumnos_tabla = TableRegistry::get("Alumnos");
		$alumnos = $alumnos_tabla->find('all');
		$alumnos_intentos = array();
		foreach ($alumnos as $alumno){
			//$intentos = $this->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $alumno->id);
			$intentos_tabla = TableRegistry::get("Intentos");
			$intentos = $intentos_tabla->find('all')
    								   ->where(['tarea_id' => $_SESSION["lti_idTarea"], 'alumno_id' => $alumno->id]);
			
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
			}
			if($this->request->data["MediaViolacionesErrores"]){
				$_SESSION["grafica_promedio_errores_violaciones"] = true;
				$graficas_controller->generarGraficaLineaPromedioErroresUnitariosViolaciones();
			}
			if($this->request->data["MediaErrores"]){
				$_SESSION["grafica_media_errores"] = true;
				$graficas_controller->generarGraficaMediaErrores();
			}
			if($this->request->data["AlumnosViolaciones"]){
				$_SESSION["grafica_alumnos_violaciones"] = true;
				$graficas_controller->generarGraficaVerticalAlumnosViolacionesCometidas();
			}
			if($this->request->data["AlumnosIntentos"]){
				$_SESSION["grafica_alumnos_intentos"] = true;
				$graficas_controller->generarGraficaVerticalAlumnosIntentos();
			}
			if($this->request->data["AlumnosTest"]){
				$_SESSION["grafica_alumnos_test"] = true;
				$graficas_controller->generarGraficaAlumnosTest();
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
	
	public function compruebaExistenciaReportes(){
	
		session_start();
		$this->autoRender = false;
		$id_alumno = $_POST["id"];
		$numero_intento = $_POST["num_intento"];
		$ruta = "../../".$_SESSION['lti_idCurso']."/".$_SESSION['lti_idTarea']."/Learner".
				"/".$id_alumno."/".$numero_intento."/site/";
		$reportes = array("pmd" => false, "findbugs" => false, "errores" => false);
	
		if(file_exists($ruta."pmd.html")){
			$reportes["pmd"] = true;
		}
		if(file_exists($ruta."findbugs.html")){
			$reportes["findbugs"] = true;
		}
		if(file_exists($ruta."surefire-report.html")){
			$reportes["errores"] = true;
		}
	
		echo json_encode($reportes);
	
	}
	
}

?>