<?php

namespace App\Controller;
use Cake\ORM\TableRegistry;

class IntentosController extends AppController{

	public function subida($tipo_usuario = null){
		
		session_start();
		
		$this->set('tipo', $tipo_usuario);
		
		if ($this->request->is('post')) {
			
			$extension = pathinfo($_FILES["ficheroAsubir"]["name"], PATHINFO_EXTENSION);
			
			if($extension != "java"){
				$this->Flash->error(__('El fichero debe tener extensión .java!'));			
			}else{
				
				if($tipo_usuario == 'alumno'){
					
					// Obtención del número de intentos máximo posibles para subir la práctica (HACERLO SOLO PARA EL ALUMNO,
					// COMPROBAR LA VARIABLE TIPO_USUARIO)
					$tareasCon = new TareasController;
					$numero_maximo_intentos = $tareasCon->obtenerIntentosPorId(18);
					
					$query = $this->Intentos->find('all')
											->where(['tarea_id' => $_SESSION['lti_idTituloActividad'], 'alumno_id' => $_SESSION['lti_userId']])
											->toArray();
											
					$total_intentos_realizados = count($query);
					
				}

							
				if($tipo_usuario == 'alumno' and $total_intentos_realizados == $numero_maximo_intentos){
					
					$this->Flash->error(__('No puedes subir más veces la práctica!!'));
					
				}else{
				
					/*
					$directorio_destino = "../" . $_SESSION["lti_tituloCurso"] . "/" . $_SESSION["lti_tituloActividad"] . "/"
								  . $_SESSION["lti_rol"] . "/" . $_SESSION["lti_userId"] . "/" . date("Y-m-d H,i,s") . "/";
					*/
					
					if($tipo_usuario == 'alumno'){
										
						$intento_realizado = $total_intentos_realizados + 1;
						
						$directorio_destino = "../" . $_SESSION["lti_tituloCurso"] . "/" . $_SESSION["lti_tituloActividad"] . "/"
									  . $_SESSION["lti_rol"] . "/" . $_SESSION["lti_userId"] . "/" . $intento_realizado . "/";
									  
						mkdir($directorio_destino, 0777, true);
			
					}else{
						
						$directorio_destino = "../" . $_SESSION["lti_tituloCurso"] . "/" . $_SESSION["lti_tituloActividad"] . "/"
									  . $_SESSION["lti_rol"] . "/" . $_SESSION["lti_userId"] . "/";
						
						if(!is_dir($directorio_destino)){
							mkdir($directorio_destino, 0777, true);
						}
						
					}
			
					// Creación de la/s carpeta/s
					//mkdir($directorio_destino, 0777, true);
					
					// Se guarda el fichero
					$path_fichero = $directorio_destino . $_FILES["ficheroAsubir"]["name"];
					move_uploaded_file($_FILES["ficheroAsubir"]["tmp_name"], $path_fichero);

					if($tipo_usuario == 'alumno'){
						
						// Añadimos el intento realizado a la base de datos
						$tabla_intentos = TableRegistry::get('Intentos');
						$nuevo_intento = $tabla_intentos->newEntity();

						$nuevo_intento->tarea_id = $_SESSION['lti_idTituloActividad'];
						$nuevo_intento->alumno_id = $_SESSION['lti_userId'];

						$tabla_intentos->save($nuevo_intento);
						
						$this->Flash->success(__('Práctica subida. Realizado intento número: ' . $intento_realizado));
						
					}else{
						$this->Flash->success(__('Test subido!'));
					}
												
				}
	
			}
			//return $this->redirect(['action' => 'subida']);
		}
	}
	
}

?>