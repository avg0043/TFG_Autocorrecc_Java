<?php

namespace App\Controller;

class IntentosController extends AppController{

	/**
	 * Función que se encarga de manejar los datos introducidos
	 * en el formulario, el cual pertenece a su vista.
	 * 
	 * @param string $tipo_usuario	puede ser profesor o alumno.
	 */
	public function subida($tipo_usuario = null){
		
		session_start();
		
		$this->set('tipo', $tipo_usuario);
		
		echo "HOLAAA";
		
		if($tipo_usuario == 'alumno'){
		
			// Número máximo de intentos para subir prácticas
			$tareas_controller = new TareasController;
			$numero_maximo_intentos = $tareas_controller->obtenerIntentosMaximo($_SESSION['lti_idTarea']);
			
			$query = $this->Intentos->find('all')
									->where(['tarea_id' => $_SESSION['lti_idTarea'], 'alumno_id' => $_SESSION['lti_userId']])
									->toArray();									
			$total_intentos_realizados = count($query);
			
			// Fecha límite para la entrega de las prácticas
			$fecha_limite = $tareas_controller->obtenerFechaLimite($_SESSION['lti_idTarea']);
			$fecha_limite = (new \DateTime($fecha_limite))->format('Y-m-d');
			$fecha_actual = (new \DateTime(date("Y-m-d H:i:s")))->format('Y-m-d');
			
			$this->set('num_maximo_intentos', $numero_maximo_intentos);
			$this->set('fecha_limite', $fecha_limite);
			$this->set('num_intentos_realizados', $total_intentos_realizados);
			
		}
		
		if ($this->request->is('post')) {
			
			$extension = pathinfo($_FILES['ficheroAsubir']['name'], PATHINFO_EXTENSION);
	
			if($extension != "java"){
				
				$this->Flash->error(__('El fichero debe tener extensión .java!'));	
				
			}else{

				if($tipo_usuario == 'alumno' and $fecha_actual > $fecha_limite){
					
					$this->Flash->error(__('La fecha tope de la entrega ha finalizado'));
					
				}elseif($tipo_usuario == 'alumno' and $total_intentos_realizados == $numero_maximo_intentos){
					
					$this->Flash->error(__('No puedes subir más veces la práctica'));
					
				}else{	// CREACIÓN DE LA ESTRUCTURA DE CARPETAS
					
					if($tipo_usuario == 'alumno'){
						
						// Cración estructura de carpetas
						$intento_realizado = $total_intentos_realizados + 1;					
						$directorio_destino = "../" . $_SESSION["lti_idCurso"] . "/" . $_SESSION["lti_idTarea"] . "/"
									  . $_SESSION["lti_rol"] . "/" . $_SESSION["lti_userId"] . "/" . $intento_realizado . "/";									  
						mkdir($directorio_destino, 0777, true);
						
						// COPIAR ESTRUCTURA MAVEN A la carpeta ID ALUMNO
						$ruta_dir_origen = "..\\" . $_SESSION["lti_idCurso"] . "\\" . $_SESSION["lti_idTarea"] . "\\"
									  . "Instructor" . "\\" . "4";						
						
						$ruta_dir_destino = "..\\" . $_SESSION["lti_idCurso"] . "\\" . $_SESSION["lti_idTarea"] . "\\"
									  . $_SESSION["lti_rol"] . "\\" . $_SESSION["lti_userId"];
					
						exec('xcopy ' . $ruta_dir_origen . ' ' . $ruta_dir_destino . ' /s /e 2>&1');
						
						// GUARDAR FICHERO en su carpeta del intento
						$path_fichero = $directorio_destino . $_FILES["ficheroAsubir"]["name"];
						move_uploaded_file($_FILES["ficheroAsubir"]["tmp_name"], $path_fichero);
						
						// COPIAR FICHERO A LA ESTRUCTURA MAVEN
						$nuevo_directorio_origen = "..\\" . $_SESSION["lti_idCurso"] . "\\" . $_SESSION["lti_idTarea"] . "\\"
									  . $_SESSION["lti_rol"] . "\\" . $_SESSION["lti_userId"] . "\\" . $intento_realizado . "\\";
						$path_fichero = $nuevo_directorio_origen . $_FILES["ficheroAsubir"]["name"];
						
						$nuevo_directorio_destino = "..\\" . $_SESSION["lti_idCurso"] . "\\" . $_SESSION["lti_idTarea"] . "\\"
									  . $_SESSION["lti_rol"] . "\\" . $_SESSION["lti_userId"] . "\\HOLI\\src\\main\\java\\ubu";
							  
						exec('xcopy ' . $path_fichero . ' ' . $nuevo_directorio_destino . ' 2>&1');
						
						// COMPROBAR SALIDA DEL TEST
						$dest = "../" . $_SESSION["lti_idCurso"] . "/" . $_SESSION["lti_idTarea"] . "/"
								. $_SESSION["lti_rol"] . "/" . $_SESSION["lti_userId"] . "/" . "HOLI";
						
						exec('cd ' . $dest . ' && mvn test', $salida);
						$salida_string = implode(' ', $salida);
						echo $salida_string;
						
						$test_pasado = false;
						
						if(strpos($salida_string, 'BUILD SUCCESS')){
							$this->Flash->error(__('La práctica ha pasado los test'));
							$test_pasado = true;
						}elseif(strpos($salida_string, 'BUILD FAILURE')){ // BUILD FAILURE
							$this->Flash->error(__('La práctica NO ha pasado los test'));
							//echo "La práctica NO ha pasado los test"; 
						}else{
							$this->Flash->error(__('error desconocido'));
							//echo "error desconocido";
						}
						
						// BORRAR PRACTICA UNA VEZ PASADA EL TEST
						$borrar = "../" . $_SESSION["lti_idCurso"] . "/" . $_SESSION["lti_idTarea"] . "/"
									  . $_SESSION["lti_rol"] . "/" . $_SESSION["lti_userId"] . "/HOLI/src/main/java/ubu/" . $_FILES["ficheroAsubir"]["name"];			
						unlink($borrar);
						
			
					}else{
						
						echo "epa";
						
						$directorio_destino = "../" . $_SESSION["lti_idCurso"] . "/" . $_SESSION["lti_idTarea"] . "/"
									  . $_SESSION["lti_rol"] . "/" . $_SESSION["lti_userId"] . "/";
						
						if(!is_dir($directorio_destino)){
							
							mkdir($directorio_destino, 0777, true);
							
							exec('SET PATH=%JAVA_HOME%\bin;%PATH% 2>&1');
							exec('cd ' . $directorio_destino . ' && mvn archetype:generate -DarchetypeArtifactId=maven-archetype-quickstart -DinteractiveMode=false -DgroupId=ubu -DartifactId=HOLI 2>&1');
											
						}
						
					}
					
					// Se guarda el fichero subido
					//$path_fichero = $directorio_destino . $_FILES["ficheroAsubir"]["name"];
					//move_uploaded_file($_FILES["ficheroAsubir"]["tmp_name"], $path_fichero);
					//echo $path_fichero;
					
					// Y SE GUARDA EN LA ESTRUCTURA MAVEN (AÑADIDO)
					if($tipo_usuario == 'profesor'){
							
						$path_fichero_maven = $directorio_destino . 'HOLI/src/test/java/ubu/' . $_FILES["ficheroAsubir"]["name"];
						move_uploaded_file($_FILES["ficheroAsubir"]["tmp_name"], $path_fichero_maven);
						
						// Se guarda el test en base de datos
						$tests_controller = new TestsController;
						$nombre_test = $_FILES['ficheroAsubir']['name'];
						$tests_controller->guardarTest($_SESSION['lti_idTarea'], $nombre_test);
						
					}
					

					if($tipo_usuario == 'alumno'){
						
						// Añadimos el intento realizado de subida de práctica del alumno
						$nuevo_intento = $this->Intentos->newEntity();
						$nuevo_intento->tarea_id = $_SESSION['lti_idTarea'];
						$nuevo_intento->alumno_id = $_SESSION['lti_userId'];
						$nuevo_intento->test = $test_pasado;
						$nuevo_intento->fecha = new \DateTime(date("Y-m-d H:i:s"));
						
						// Hacer un if(!..?? )
						$this->Intentos->save($nuevo_intento);
						
						$this->Flash->success(__('Práctica subida. Realizado intento número: ' . $intento_realizado));
						return $this->redirect(['action' => 'subida', 'alumno']);
						
					}else{
						
						$this->Flash->success(__('Test subido!'));
						
					}
												
				}
	
			}
		}
	}
	
}

?>