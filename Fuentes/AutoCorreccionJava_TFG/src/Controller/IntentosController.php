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
					
				}else{
					
					if($tipo_usuario == 'alumno'){
										
						$intento_realizado = $total_intentos_realizados + 1;					
						$directorio_destino = "../" . $_SESSION["lti_idCurso"] . "/" . $_SESSION["lti_idTarea"] . "/"
									  . $_SESSION["lti_rol"] . "/" . $_SESSION["lti_userId"] . "/" . $intento_realizado . "/";									  
						mkdir($directorio_destino, 0777, true);
			
					}else{
						
						$directorio_destino = "../" . $_SESSION["lti_idCurso"] . "/" . $_SESSION["lti_idTarea"] . "/"
									  . $_SESSION["lti_rol"] . "/" . $_SESSION["lti_userId"] . "/";
						
						if(!is_dir($directorio_destino)){
							
							mkdir($directorio_destino, 0777, true);
							
						}
						
					}
					
					// Se guarda el fichero subido
					$path_fichero = $directorio_destino . $_FILES["ficheroAsubir"]["name"];
					move_uploaded_file($_FILES["ficheroAsubir"]["tmp_name"], $path_fichero);

					if($tipo_usuario == 'alumno'){
						
						// Añadimos el intento realizado de subida de práctica del alumno
						$nuevo_intento = $this->Intentos->newEntity();
						$nuevo_intento->tarea_id = $_SESSION['lti_idTarea'];
						$nuevo_intento->alumno_id = $_SESSION['lti_userId'];
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