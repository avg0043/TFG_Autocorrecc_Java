<?php

namespace App\Controller;

class PruebasController extends AppController{
	
	/*
	public function recibeValor(){
		
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
	*/
	
	public function doble(){
		return 2;
	}
	
	public function redireccionando(){
		return $this->redirect(['controller' => 'Profesores', 'action' => 'mostrarPanel']);
	}
	
}
