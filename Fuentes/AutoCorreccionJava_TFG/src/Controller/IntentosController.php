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
	
	/**
	 * Función que se encarga de manejar los datos introducidos
	 * en el formulario, el cual pertenece a su vista.
	 * 
	 * @param string $tipo_usuario	puede ser profesor o alumno.
	 */
	public function subida(){
		
		session_start();
			
		$this->__establecerDatosVista();			
		
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
					$this->id_profesor = $tareas_controller->obtenerTarea($_SESSION['lti_idTarea'])[0]->profesor_id;
										
					$this->__guardarPracticaAlumno();
					return $this->redirect(['action' => 'subida']);
				}
			}
		}
	}
	
	private function __establecerDatosVista(){
		
		$tareas_controller = new TareasController;
		$this->numero_maximo_intentos = $tareas_controller->obtenerTarea($_SESSION['lti_idTarea'])[0]->num_max_intentos;
			
		$query = $this->Intentos->find('all')
				->where(['tarea_id' => $_SESSION['lti_idTarea'], 'alumno_id' => $_SESSION['lti_userId']])
				->toArray();
		$this->total_intentos_realizados = count($query);
			
		$this->fecha_limite = $tareas_controller->obtenerTarea($_SESSION['lti_idTarea'])[0]->fecha_limite;
		$this->fecha_limite = (new \DateTime($this->fecha_limite))->format('Y-m-d');
		$this->fecha_actual = (new \DateTime(date("Y-m-d H:i:s")))->format('Y-m-d');
			
		$this->set('num_maximo_intentos', $this->numero_maximo_intentos);
		$this->set('fecha_limite', $this->fecha_limite);
		$this->set('num_intentos_realizados', $this->total_intentos_realizados);
		
	}
	
	private function __guardarPracticaAlumno(){
			
		//	Creación de la estructura de carpetas del alumno (si no lo está)
		if(!is_dir($this->ruta_carpeta_id)){
			mkdir($this->ruta_carpeta_id . "/", 0777, true);
		}
				
		// Copiar el arquetipo maven a la carpeta id alumno
		$ruta_dir_origen = "..\\..\\" . $_SESSION["lti_idCurso"] . "\\" . $_SESSION["lti_idTarea"] . "\\"
							. "Instructor" . "\\" . $this->id_profesor;
		exec('xcopy ' . $ruta_dir_origen . ' ' . str_replace('/', '\\', $this->ruta_carpeta_id) . ' /s /e');
		
		// Extraer zip subido en la correspondiente carpeta del arquetipo
		$zip = new \ZipArchive;
		move_uploaded_file($_FILES["ficheroAsubir"]["tmp_name"], './' . $_FILES["ficheroAsubir"]["name"]);	
		if ($zip->open($_FILES["ficheroAsubir"]["name"]) === TRUE) {
			$zip->extractTo($this->ruta_carpeta_id . 'arquetipo/src/main/java/');
			$zip->close();
		}
		unlink('./' . $_FILES["ficheroAsubir"]["name"]);
		
		$this->__comprobarCompilacion();
		
	}
	
	private function __comprobarCompilacion(){
		
		exec('cd ' . $this->ruta_carpeta_id . "/arquetipo" . ' && mvn compile', $salida);
		$salida_string = implode(' ', $salida);
		
		if(strpos($salida_string, 'BUILD SUCCESS')){	// Compilación correcta
			// Creación carpeta intento
			$this->intento_realizado = $this->total_intentos_realizados + 1;
			mkdir($this->ruta_carpeta_id . $this->intento_realizado . "/", 0777, true);
			
			$this->__ejecutarTests();
		}
		else{
			// Borrar estructura main>java del arquetipo
			exec('cd ' . $this->ruta_carpeta_id . "/arquetipo/src/main" . ' && rmdir java /s /q && md java');
			$this->Flash->error(__('La práctica tiene errores de compilación!'));
		}
		
	}
	
	private function __ejecutarTests(){
		
		// Ejecución de los test
		exec('cd ' . $this->ruta_carpeta_id . "/arquetipo" . ' && mvn test', $salida);
		$salida_string = implode(' ', $salida);

		// Comprobar salida generada
		if(strpos($salida_string, 'BUILD SUCCESS')){
			$this->Flash->error(__('La práctica ha pasado los test'));
			$this->test_pasado = true;
		}
		elseif(strpos($salida_string, 'BUILD FAILURE')){
			$this->Flash->error(__('La práctica NO ha pasado los test'));
		}
		else{
			$this->Flash->error(__('error desconocido'));
		}
		
		// Borrar estructura main>java del arquetipo
		exec('cd ' . $this->ruta_carpeta_id . "/arquetipo/src/main" . ' && rmdir java /s /q && md java');
		
		$this->__guardarIntento();
		
	}
	
	private function __guardarIntento(){
		
		// Añadir intento realizado a la base de datos
		$nuevo_intento = $this->Intentos->newEntity();
		$nuevo_intento->tarea_id = $_SESSION['lti_idTarea'];
		$nuevo_intento->alumno_id = $_SESSION['lti_userId'];
		$nuevo_intento->resultado = $this->test_pasado;		

		date_default_timezone_set("Europe/Madrid");
		$nuevo_intento->fecha_intento = new \DateTime(date("Y-m-d H:i:s"));
		
		// Hacer un if(!..?? )
		$this->Intentos->save($nuevo_intento);
		$this->Flash->success(__('Práctica subida. Realizado intento número: ' . $this->intento_realizado));
		
	}
	
}

?>