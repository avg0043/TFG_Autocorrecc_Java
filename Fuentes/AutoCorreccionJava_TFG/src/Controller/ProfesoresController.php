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
		$this->set('intentos', $intentos_controller->obtenerIntentos());
		
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