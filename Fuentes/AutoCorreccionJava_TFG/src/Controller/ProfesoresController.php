<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;

/**
 * Contrlador encargado de los profesores.
 * 
 * @author Álvaro Vázquez Gómez
 *
 */
class ProfesoresController extends AppController{
	
	/**
	 * Función asociada a una vista, que se encarga de
	 * registrar al profesor en base de datos tras rellenar
	 * los campos del formulario de registro.
	 * 
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
	 * Función privada que encripta y devuelve la cadena pasada por parámetro.
	 *
	 * @param string $cadena	cadena a encriptar.
	 * 
	 */
	private function __encriptarCadena($cadena){
	
		$key='clave_codificacion';
		$encriptada = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $cadena, MCRYPT_MODE_CBC, md5(md5($key))));
		return $encriptada; //Devuelve el string encriptado
	
	}
	
	/**
	 * Función privada que crea un consumer_key aleatoriamente, 
	 * que se le va a entregar al profesor al registrarse.
	 * 
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
	 * Función asociada a una vista, encargada de pasarle a la vista
	 * los parámetros LTI del profesor para que posteriormente pueda
	 * crear una tarea que enlace con la aplicación desde Moodle.
	 * 
	 * @param string $correo	correo del profesor registrado.
	 */
	public function mostrarParametrosLti($correo){
	
		$this->set('parametros', $this->Profesores->find('all')->where(['correo' => $correo]));
	
	}
	
	/**
	 * Función asociada a una vista encargada mostrar
	 * el panel principal del profesor.
	 * 
	 */
	public function mostrarPanel(){
	
		$this->comprobarSesion();
		$this->comprobarRolProfesor();
		
	}
	
	/**
	 * Función asociada a una vista, que se encarga de pasarle
	 * a la vista los datos necesarios de los intentos y alumnos.
	 * 
	 */
	public function descargarPracticasAlumnos(){
		
		if(!isset($_SESSION["lti_userId"])){
			return $this->redirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoLocal']);
		}
		$this->comprobarRolProfesor();
		
		$alumnos_tabla = TableRegistry::get("Alumnos");
		$intentos_tabla = TableRegistry::get("Intentos");
		
		$this->set('intentos_todos', $intentos_tabla->find('all'));
		$this->set('alumnos', $alumnos_tabla->find('all'));
		$this->set('intentos', $intentos_tabla->find('all')->where(['tarea_id' => $_SESSION["lti_idTarea"]]));
		
	}
	
	/**
	 * Función asociada a una vista, que se encarga de llamar
	 * a los métodos de generación de gráficas en función de las 
	 * gráficas que hayan sido seleccionadas en el formulario de 
	 * la vista.
	 * 
	 */
	public function generarGraficas(){
		
		include('/../../vendor/libchart/libchart/classes/libchart.php');
		
		if(!isset($_SESSION["lti_userId"])){
			return $this->redirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoLocal']);
		}
		$this->comprobarRolProfesor();
		
		$graficas_controller = new GraficasController();
		$alumnos_tabla = TableRegistry::get("Alumnos");
		$alumnos = $alumnos_tabla->find('all');
		$alumnos_intentos = array();
		foreach ($alumnos as $alumno){
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
	
	/**
	 * Función encargada de comprobar la existencia de los reportes
	 * de una práctica. Recibe como parámetros POST el id del alumno
	 * y el número del intento de subida de práctica realizado.
	 * Genera un array de boolean en el que se indican los reportes que
	 * han sido generados, y por último se devuelve dicho array mediante
	 * JSON.
	 * 
	 */
	public function compruebaExistenciaReportes(){
	
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
	
	/**
	 * Función asociada a una vista, que se encarga de
	 * generar el reporte de plagios entre los alumnos que han
	 * sido seleccionados desde el formulario de la vista.
	 * 
	 */
	public function generarReportePlagiosPracticas(){
		
		if(!isset($_SESSION["lti_userId"])){
			return $this->redirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoLocal']);
		}
		$this->comprobarRolProfesor();
		
		$alumnos_tabla = TableRegistry::get("Alumnos");
		$alumnos = $alumnos_tabla->find('all');
		$alumnos_intentos = array();
		$reporte_generado = false;
		
		foreach ($alumnos as $alumno){	// Alumnos que han realizado intentos
			$intentos_tabla = TableRegistry::get("Intentos");
			$intentos = $intentos_tabla->find('all')
									   ->where(['tarea_id' => $_SESSION["lti_idTarea"], 
									   			'alumno_id' => $alumno->id]);
				
			if(!$intentos->isEmpty()){
				$alumnos_intentos[$alumno->id] = $alumno->nombre." ".$alumno->apellidos;
			}
		}
		
		$this->set("alumnos", $alumnos_intentos);
		$this->set("reporte_generado", $reporte_generado);
		
		if($this->request->is('post')){	
			if(!in_array(1, $this->request->data)){	// Ninguna opción seleccionada
				$this->Flash->error(__('Debes seleccionar al menos 2 alumnos o TODOS CON TODOS'));
				return $this->redirect(['action' => 'generarReportePlagiosPracticas']);
			}
			
			$num_alumnos_seleccionados = 0;
			$alumnos_seleccionados = array();
			$id_alumno = key($this->request->data);
			foreach ($this->request->data as $alumno):	// Obtención del id de los alumnos seleccionados
				if($alumno){
					$num_alumnos_seleccionados++;
					array_push($alumnos_seleccionados, $id_alumno);
				}
				$id_alumno = key($this->request->data);
				next($this->request->data);
			endforeach;
			
			// Selección incorrecta
			if(!$this->request->data["TodosConTodos"] && $num_alumnos_seleccionados == 1){
				$this->Flash->error(__('Debes de seleccionar al menos 2 alumnos'));
				return $this->redirect(['action' => 'generarReportePlagiosPracticas']);
			}
			
			// Creación carpeta "plagios" en la que van a almacenarse las prácticas
			if(is_dir("../../plagios/")){
				exec('cd ' . "../../" . ' && rmdir plagios /s /q');
			}
			mkdir("../../plagios/practicas", 0777, true);
			
			$intentos_tabla = TableRegistry::get("Intentos");
			$tareas_tabla = TableRegistry::get("Tareas");
			$query = $tareas_tabla->find('all')
								  ->where(['id' => $_SESSION["lti_idTarea"]])
								  ->toArray();
			$paquete = $query[0]->paquete;
			$paquete_ruta = str_replace('.', '\\', $paquete);
			$ruta_carpeta_tarea = "../../".$_SESSION["lti_idCurso"]."/".$_SESSION["lti_idTarea"]."/";
					
			// Copia de la última práctica de los alumnos a la carpeta "plagios" creada
			foreach ($alumnos as $alumno):
				if($this->request->data["TodosConTodos"] || in_array($alumno->id, $alumnos_seleccionados)){
					$ultimo_intento = $intentos_tabla->find('all')
													 ->where(['tarea_id' => $_SESSION["lti_idTarea"], 
													 	      'alumno_id' => $alumno->id])
													 ->last()
													 ->toArray();
					
					mkdir("../../plagios/practicas/".utf8_decode($alumno->nombre.str_replace(' ', '', $alumno->apellidos)), 0777, true);
					exec('xcopy ' . str_replace('/', '\\', $ruta_carpeta_tarea)."Learner\\"
							. $ultimo_intento['alumno_id']."\\". $ultimo_intento['numero_intento']."\\".$paquete_ruta . ' '
							. "..\\..\\plagios\\practicas\\".utf8_decode($alumno->nombre.str_replace(' ', '', $alumno->apellidos))."\\" . ' /s /e');
				}
			endforeach;
			
			// Generación del reporte de plagios (Llamada al plugin JPlag)
			exec('cd ../../plagios && java -Dfile.encoding=UTF-8 -jar ../AutoCorreccionJava_TFG/vendor/jplag-2.11.8-SNAPSHOT-jar-with-dependencies.jar -l java17 -r reporte/ -s practicas/');
			$this->Flash->success(__('El reporte de plagios ha sido generado'));
			$reporte_generado = true;
			$this->set("reporte_generado", $reporte_generado);
			if($this->request->data["TodosConTodos"]){
				$num_alumnos_seleccionados = count($alumnos->toArray());
			}
			$this->set("numero_practicas_subidas", $num_alumnos_seleccionados);
		}
		
	}
	
}

?>