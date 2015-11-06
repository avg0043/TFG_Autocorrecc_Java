<?php

namespace App\Controller;

class IntentosController extends AppController{

	public function subida($tipo_usuario = null){
		
		session_start();
		
		$this->set('tipo', $tipo_usuario);
		
		if ($this->request->is('post')) {
			
			$extension = pathinfo($_FILES['ficheroAsubir']['name'], PATHINFO_EXTENSION);
	
			if($extension != "java"){
				
				$this->Flash->error(__('El fichero debe tener extensión .java!'));	
				
			}else{
				
				if($tipo_usuario == 'alumno'){
					
					// Obtención del nº de intentos máximo que pueden subir los alumnos la práctica
					$tareas_controller = new TareasController;
					$numero_maximo_intentos = $tareas_controller->obtenerIntentosPorId($_SESSION['lti_idTituloActividad']);
					
					$query = $this->Intentos->find('all')
											->where(['tarea_id' => $_SESSION['lti_idTituloActividad'], 'alumno_id' => $_SESSION['lti_userId']])
											->toArray();									
					$total_intentos_realizados = count($query);
					
					// Obtención de la fecha tope de entrega que tienen los alumnos para enviar prácticas
					$fecha_tope = $tareas_controller->obtenerFechaTopePorId($_SESSION['lti_idTituloActividad']);
					$fecha_tope = (new \DateTime($fecha_tope))->format('Y-m-d');
					$fecha_actual = (new \DateTime(date("Y-m-d H:i:s")))->format('Y-m-d');
					
				}

				if($tipo_usuario == 'alumno' and $fecha_actual > $fecha_tope){
					
					$this->Flash->error(__('La fecha tope de la entrega ha finalizado'));
					
				}elseif($tipo_usuario == 'alumno' and $total_intentos_realizados == $numero_maximo_intentos){
					
					$this->Flash->error(__('No puedes subir más veces la práctica'));
					
				}else{
					
					if($tipo_usuario == 'alumno'){
										
						$intento_realizado = $total_intentos_realizados + 1;					
						$directorio_destino = "../" . $_SESSION["lti_idCurso"] . "/" . $_SESSION["lti_idTituloActividad"] . "/"
									  . $_SESSION["lti_rol"] . "/" . $_SESSION["lti_userId"] . "/" . $intento_realizado . "/";									  
						mkdir($directorio_destino, 0777, true);
			
					}else{
						
						$directorio_destino = "../" . $_SESSION["lti_idCurso"] . "/" . $_SESSION["lti_idTituloActividad"] . "/"
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
						$nuevo_intento->tarea_id = $_SESSION['lti_idTituloActividad'];
						$nuevo_intento->alumno_id = $_SESSION['lti_userId'];
						$this->Intentos->save($nuevo_intento);
						
						$this->Flash->success(__('Práctica subida. Realizado intento número: ' . $intento_realizado));
						
					}else{
						
						$this->Flash->success(__('Test subido!'));
						
					}
												
				}
	
			}
		}
	}
	
}

?>