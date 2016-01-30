<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;

/**
 * Controlador encargado de los intentos de subida de práctica
 * realizados por los alumnos.
 * 
 * @author Álvaro Vázquez Gómez
 *
 */
class IntentosController extends AppController{

	/**
	 * Fecha actual en la que se ha realizado la subida de práctica.
	 * @var DateTime
	 */
	private $fecha_actual;
	
	/**
	 * Fecha límite que tienen los alumnos para subir sus prácticas.
	 * @var DateTime
	 */
	private $fecha_limite;
	
	/**
	 * Número total de intentos se subida de prácticas realizados por el alumno.
	 * @var int
	 */
	private $total_intentos_realizados;
	
	/**
	 * Número máximo de intentos de subida de prácticas que puede realizar el
	 * alumno.
	 * @var int
	 */
	private $numero_maximo_intentos;
	
	/**
	 * Indica si la práctica subida por el alumno ha pasado los test o no.
	 * @var boolean
	 */
	private $test_pasado = false;
	
	/**
	 * Id del profesor que ha creado la tarea.
	 * @var id
	 */
	private $id_profesor;
	
	/**
	 * Ruta a la carpeta id del alumno.
	 * @var string
	 */
	private $ruta_carpeta_id;
	
	/**
	 * Número del intento de subida de práctica realizado por el alumno.
	 * @var int
	 */
	private $intento_realizado;
	
	/**
	 * Paquete al que pertenece la práctica.
	 * @var string
	 */
	private $paquete;
	
	/**
	 * Nombre del fichero zip en el que ha subido la práctica.
	 * @var string
	 */
	private $nombre_practica_zip;
	
	/**
	 * Id del intento de la subida de práctica realizada.
	 * @var int
	 */
	private $id_intento;
	
	
	/**
	 * Función asociada a una vista, encargada de realizar todas las
	 * operaciones de análisis y guardado de la práctica subida por
	 * el alumno en el correspondiente formulario.
	 * 
	 */
	public function subirPractica(){
	
		$finalizado = false;
		$this->set("finalizado", $finalizado);	
		
		if(!isset($_SESSION["lti_userId"])){
			return $this->redirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoLocal']);
		}	
		$this->comprobarRolAlumno();	
	
		$this->__comprobarTestSubido();	
		$intentos_alumno = $this->Intentos->find('all')
    									  ->where(['tarea_id' => $_SESSION["lti_idTarea"], 'alumno_id' => $_SESSION["lti_userId"]]);
    	$num_ultimo_intento = null;
		if(!$intentos_alumno->isEmpty()){	// Se obtiene el número del último intento realizado y se le pasa a la vista
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
					$tareas_tabla = TableRegistry::get("Tareas");
					$query = $tareas_tabla->find('all')
									      ->where(['id' => $_SESSION['lti_idTarea']])
									      ->toArray();
					$this->id_profesor = $query[0]->profesor_id;							
					$this->__copiarArquetipoMaven();
					$finalizado = true;
					$this->set("finalizado", $finalizado);
					return $this->redirect($this->here);
				}
			}
		}
	}
	
	/**
	 * Función privada encargada de comprobar si el profesor
	 * ha subido un test.
	 * 
	 */
	private function __comprobarTestSubido(){

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
	
	/**
	 * Función privada encargada de pasar los datos a la vista 
	 * en la que van a mostrarse.
	 * 
	 */
	private function __establecerDatosVista(){
			
		$tareas_tabla = TableRegistry::get("Tareas");
		$query_tarea = $tareas_tabla->find('all')
							  		->where(['id' => $_SESSION['lti_idTarea']])
							  		->toArray();
		$this->paquete = $query_tarea[0]->paquete;		
		$this->numero_maximo_intentos = $query_tarea[0]->num_max_intentos;
		
		$query = $this->Intentos->find('all')
								->where(['tarea_id' => $_SESSION['lti_idTarea'], 'alumno_id' => $_SESSION['lti_userId']])
								->toArray();
		$this->total_intentos_realizados = count($query);		
		$this->fecha_limite = $query_tarea[0]->fecha_limite;
		$this->fecha_limite = (new \DateTime($this->fecha_limite))->format('Y-m-d');
		$this->fecha_actual = (new \DateTime(date("Y-m-d H:i:s")))->format('Y-m-d');
		
		$this->set('paquete', $this->paquete);
		$this->set('num_maximo_intentos', $this->numero_maximo_intentos);
		$this->set('fecha_limite', $this->fecha_limite);
		$this->set('num_intentos_realizados', $this->total_intentos_realizados);
		
	}
	
	/**
	 * Función privada encargada de copiar el arquetipo maven
	 * creado en la carpeta del profesor a la carpeta del alumno.
	 * 
	 */
	private function __copiarArquetipoMaven(){
		
		if(!is_dir($this->ruta_carpeta_id)){
			mkdir($this->ruta_carpeta_id . "/", 0777, true);  // estructuras de carpetas del alumno
		}
		
		// Copia del arquetipo maven a la carpeta id alumno
		$ruta_dir_origen = "..\\..\\" . $_SESSION["lti_idCurso"] . "\\" . $_SESSION["lti_idTarea"] . "\\"
							. "Instructor" . "\\" . $this->id_profesor;
		exec('xcopy ' . $ruta_dir_origen . ' ' . str_replace('/', '\\', $this->ruta_carpeta_id) . ' /s /e /Y');
		// Borrar estructura main>java del arquetipo
		exec('cd ' . $this->ruta_carpeta_id . "/arquetipo/src/main" . ' && rmdir java /s /q && md java');
		$this->__extraerPractica();
		
	}
	
	/**
	 * Función privada encargada de extraer la práctica subida
	 * por el alumno.
	 * 
	 */
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
	
	/**
	 * Función privada encargada de comprobar que la práctica 
	 * subida por el alumno pertenece al paquete correcto.
	 * 
	 */
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
	
	/**
	 * Función privada encargada de compilar la práctica.
	 * 
	 */
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
	
	/**
	 * Función privada encargada de guardar en base de datos
	 * el intento de subida de práctica realizado por el alumno.
	 * 
	 */
	private function __guardarIntento(){
	
		$nuevo_intento = $this->Intentos->newEntity();
		$nuevo_intento->tarea_id = $_SESSION['lti_idTarea'];
		$nuevo_intento->alumno_id = $_SESSION['lti_userId'];
		$nuevo_intento->nombre = $this->nombre_practica_zip;
		$nuevo_intento->numero_intento = $this->intento_realizado;
		$nuevo_intento->ruta = $this->ruta_carpeta_id . $this->intento_realizado . "/";
		date_default_timezone_set("Europe/Madrid");
		$nuevo_intento->fecha_intento = new \DateTime(date("Y-m-d H:i:s"));	// fecha actual
		$comentarios = $this->request->data['comentarios'];
		if($comentarios != null){
			$nuevo_intento->comentarios = $comentarios;
		}
		$this->Intentos->save($nuevo_intento);
		
		$this->__generarReportes();
	
	}
	
	/**
	 * Función privada encargada de generar los reportes de
	 * los plugins.
	 * 
	 */
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
		
		$this->__guardarDatosXML();
	
	}
	
	/**
	 * Función privada encargada de llamar a los métodos
	 * de guardado de datos xml de los plugins.
	 * 
	 */
	private function __guardarDatosXML(){
	
		$ficherosXml_controller = new FicherosXmlController();
		$this->id_intento = $this->__obtenerIntentoPorTareaAlumnoNumIntento($_SESSION["lti_idTarea"],
																		    $_SESSION["lti_userId"],
																		    $this->intento_realizado)[0]->id;
																	  
		if($_SESSION["pmd_generado"]){
			$ficherosXml_controller->guardarDatosXmlPluginPmd($this->ruta_carpeta_id, $this->id_intento, 
															  						  $this->intento_realizado);
		}	
		if($_SESSION["findbugs_generado"]){	// el findbugs.html siempre se genera aunque no haya fallos
			$_SESSION["findbugs_generado"] = false;
			$ficherosXml_controller->guardarDatosXmlPluginFindbugs($this->ruta_carpeta_id, $this->id_intento, 
																						   $this->intento_realizado);
		}
		
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
	
	/**
	 * Función privada encargada de ejecutar los test.
	 * 
	 */
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
	
	/**
	 * Función privada encargada de obtener el intento correspondiente.
	 * 
	 * @param int $id_tarea	id de la tarea.
	 * @param int $id_alumno	id del alumno.
	 * @param int $num_intento	número del intento realizado.
	 */
	private function __obtenerIntentoPorTareaAlumnoNumIntento($id_tarea, $id_alumno, $num_intento){
	
		return $this->Intentos->find('all')
							  ->where(['tarea_id' => $id_tarea, 'alumno_id' => $id_alumno, 'numero_intento' => $num_intento])
							  ->toArray();
	
	}
	
	/**
	 * Función privada encargada de actualizar el resultado (test pasado o no)
	 * del intento de subida de práctica realizado.
	 * 
	 * @param int $id_intento	id del intento de subida de práctica realizado.
	 * @param boolean $resultado	indica si ha pasado los test o no.
	 */
	private function __actualizarResultadoIntento($id_intento, $resultado){
	
		$this->Intentos->query()
					   ->update()
					   ->set(['resultado' => $resultado])
					   ->where(['id' => $id_intento])
					   ->execute();
	
		$this->Flash->success(__('Práctica subida. Realizado intento número: {0}', $this->intento_realizado));
	
	}
	
}

?>