<?php

namespace App\Controller;

class IntentosController extends AppController{

	private $fecha_actual;
	private $fecha_limite;
	private $total_intentos_realizados;
	private $numero_maximo_intentos;
	private $test_pasado = false;
	private $id_profesor;
	private $ruta_comun;  
	
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
			
			if($extension != "java"){				
				$this->Flash->error(__('El fichero debe tener extensión .java!'));				
			}
			else{
				if($this->fecha_actual > $this->fecha_limite){				
					$this->Flash->error(__('La fecha tope de la entrega ha finalizado'));			
				}
				elseif($this->total_intentos_realizados == $this->numero_maximo_intentos){				
					$this->Flash->error(__('No puedes subir más veces la práctica'));				
				}
				else{				
					$this->ruta_comun = "../../" . $_SESSION["lti_idCurso"] . "/" . $_SESSION["lti_idTarea"] . "/"
										. $_SESSION["lti_rol"] . "/";
					
					$tareas_controller = new TareasController();
					$this->id_profesor = $tareas_controller->obtenerTarea($_SESSION['lti_idTarea'])[0]->profesor_id;
							
					$this->__realizarAccionesAlumno();																	
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
	
	private function __realizarAccionesAlumno(){
		
		$this->__guardarPracticaAlumno();
		$this->__ejecutarTests();
		$this->__guardarIntento();
		
		//$this->Flash->success(__('Práctica subida. Realizado intento número: ' . $intento_realizado));
		// Actualizar vista
		return $this->redirect(['action' => 'subida']);
		
	}
	
	private function __guardarPracticaAlumno(){
		
		$ruta_carpeta_id = "..\\..\\" . $_SESSION["lti_idCurso"] . "\\" . $_SESSION["lti_idTarea"] . "\\"
							. $_SESSION["lti_rol"] . "\\" . $_SESSION["lti_userId"] . "\\";
		
		//	Creación de la estructura de carpetas del alumno
		$intento_realizado = $this->total_intentos_realizados + 1;
		$directorio_destino = $this->ruta_comun . $_SESSION["lti_userId"] . "/" . $intento_realizado . "/";
		mkdir($directorio_destino, 0777, true);
		
		// Guardar práctica en la carpeta del intento
		$path_fichero = $directorio_destino . $_FILES["ficheroAsubir"]["name"];
		move_uploaded_file($_FILES["ficheroAsubir"]["tmp_name"], $path_fichero);
		
		// Copiar el arquetipo maven a la carpeta id alumno
		$ruta_dir_origen = "..\\..\\" . $_SESSION["lti_idCurso"] . "\\" . $_SESSION["lti_idTarea"] . "\\"
				. "Instructor" . "\\" . $this->id_profesor;
		$ruta_dir_destino = $ruta_carpeta_id;
		exec('xcopy ' . $ruta_dir_origen . ' ' . $ruta_dir_destino . ' /s /e 2>&1');
		
		// Copiar práctica al arquetipo maven
		$nuevo_directorio_origen = $ruta_carpeta_id . $intento_realizado . "\\";
		$path_fichero = $nuevo_directorio_origen . $_FILES["ficheroAsubir"]["name"];
		$nuevo_directorio_destino = $ruta_carpeta_id . "arquetipo\\src\\main\\java\\ubu\\";						
		exec('xcopy ' . $path_fichero . ' ' . $nuevo_directorio_destino . ' 2>&1');
		
	}
	
	private function __ejecutarTests(){
		
		$dest = $this->ruta_comun . $_SESSION["lti_userId"] . "/" . "arquetipo";
		
		// Ejecución de los test
		exec('cd ' . $dest . ' && mvn test', $salida);
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

		// Borrar práctica del arquetipo
		$borrar = $this->ruta_comun . $_SESSION["lti_userId"] . "/arquetipo/src/main/java/ubu/" . $_FILES["ficheroAsubir"]["name"];
		unlink($borrar);
		
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
		
	}
	
}

?>