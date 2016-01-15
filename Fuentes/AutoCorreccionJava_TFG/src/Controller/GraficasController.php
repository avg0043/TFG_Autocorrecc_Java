<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;
include('/../../vendor/libchart/libchart/classes/libchart.php');

class GraficasController extends AppController{
	
	public function mostrar(){
		
	}
	
	public function generarGraficasViolacionesErroresAlumno(){
	
		//$query_violaciones = $this->obtenerIntentosConViolaciones();
		$intentos_tabla = TableRegistry::get("Intentos");
		$query_violaciones = $intentos_tabla->find('all')
									    	->contain(['Violaciones'])
									    	//->where(['alumno_id' => $_SESSION["lti_userId"]]);
									    	->where(['alumno_id' => $_SESSION["lti_userId"], 'tarea_id' => $_SESSION["lti_idTarea"]]);
		
		//$query_errores = $this->obtenerIntentosConErrores();
		$query_errores = $intentos_tabla->find('all')
								    	->contain(['Errores'])
								    	//->where(['alumno_id' => $_SESSION["lti_userId"]]);
										->where(['alumno_id' => $_SESSION["lti_userId"], 'tarea_id' => $_SESSION["lti_idTarea"]]);
		
		// Línea
		$chart_linea = new \LineChart(800, 350);
		$serie1 = new \XYDataSet();
		$serie2 = new \XYDataSet();
	
		// Barras Violaciones
		$chart_violaciones = new \VerticalBarChart(800, 350);
		$total_intentos_violaciones = 0;
		$total_violaciones = 0;
		$dataSet_violaciones = new \XYDataSet();
	
		// Barras Errores
		$chart_errores = new \VerticalBarChart(800, 350);
		$serie_errores_unitarios = new \XYDataSet();
		$serie_errores_excepciones = new \XYDataSet();
		$total_intentos_errores = 0;
		$total_errores_unitarios = 0;
		$total_errores_excepcion = 0;
	
		// Línea y barras Violaciones
		foreach ($query_violaciones as $intento) {
			$numero_violaciones = count($intento->violaciones);
			$serie1->addPoint(new \Point("Intento: ".$intento->numero_intento, $numero_violaciones));
			$dataSet_violaciones->addPoint(new \Point("Intento: ".$intento->numero_intento, $numero_violaciones));
			$total_intentos_violaciones++;
			$total_violaciones += $numero_violaciones;
		}
	
		// Línea Errores
		foreach ($query_errores as $intento) {
			$total_intentos_errores++;
			$num_errores_unitarios = 0;
			$num_errores_excepcion = 0;
			foreach ($intento->errores as $error){
				if($error->tipo_error == "failure"){
					$num_errores_unitarios++;
					$total_errores_unitarios += $num_errores_unitarios;
				}else{
					$num_errores_excepcion++;
					$total_errores_excepcion += $num_errores_excepcion;
				}
			}
			$serie_errores_unitarios->addPoint(new \Point("Intento: ".$intento->numero_intento, $num_errores_unitarios));
			$serie_errores_excepciones->addPoint(new \Point("Intento: ".$intento->numero_intento, $num_errores_excepcion));
			$numero_errores = count($intento->errores);
			$serie2->addPoint(new \Point("Intento: ".$intento->numero_intento, $numero_errores));
		}
	
		// Línea violaciones y errores
		if($total_intentos_violaciones > 1 && $total_intentos_errores > 1){
			$dataSet = new \XYSeriesDataSet();
			$dataSet->addSerie("Violaciones de código", $serie1);
			$dataSet->addSerie("Errores", $serie2);
			$chart_linea->setDataSet($dataSet);
			$chart_linea->setTitle("Violaciones de código - Errores");
			$chart_linea->render("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-linea.png");
		}
	
		// Barras Violaciones
		$dataSet_violaciones->addPoint(new \Point("Media", round($total_violaciones/$total_intentos_violaciones, 2)));
		$chart_violaciones->setDataSet($dataSet_violaciones);
		$chart_violaciones->setTitle("Número de violaciones de código cometidas");
		$chart_violaciones->render("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-violaciones.png");
	
		// Barras Errores
		$serie_errores_unitarios->addPoint(new \Point("Media", round($total_errores_unitarios/$total_intentos_errores, 2)));
		$serie_errores_excepciones->addPoint(new \Point("Media", round($total_errores_excepcion/$total_intentos_errores, 2)));
		$dataSet_errores = new \XYSeriesDataSet();
		$dataSet_errores->addSerie("Errores Unitarios", $serie_errores_unitarios);
		$dataSet_errores->addSerie("Excepciones", $serie_errores_excepciones);
		$chart_errores->setDataSet($dataSet_errores);
		$chart_errores->getPlot()->setGraphCaptionRatio(0.65);
		$chart_errores->setTitle("Errores cometidos");
		$chart_errores->render("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-errores_unitarios.png");
	
	}
	
	public function generarGraficaPrioridadesViolacionesIntentoRealizadoAlumno($id_intento){
	
		if(file_exists("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones_ultimoIntento.png")){
			unlink("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones_ultimoIntento.png");
		}
		if(file_exists("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones_ultimoIntento_barras.png")){
			unlink("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones_ultimoIntento_barras.png");
		}

		//$violaciones = $this->obtenerViolacionesPorIdIntento($id_intento);
		$violaciones_tabla = TableRegistry::get("Violaciones");
		$violaciones = $violaciones_tabla->find('all')
									     ->where(['intento_id' => $id_intento])
									     ->toArray();
	
		if(!empty($violaciones)){
			$chart = new \PieChart(800, 350);
			$dataSet = new \XYDataSet();
			$chart_barras = new \VerticalBarChart(800, 350);
			$dataSet_barras = new \XYDataSet();
				
			foreach ($violaciones as $violacion){
				$prioridad = $violacion->prioridad;
				$point = $dataSet->getPointWithX("Prioridad ".$prioridad);
				$point_barras = $dataSet_barras->getPointWithX("Prioridad ".$prioridad);
				if($point == null){
					$dataSet->addPoint(new \Point("Prioridad ".$prioridad, 1));
					$dataSet_barras->addPoint(new \Point("Prioridad ".$prioridad, 1));
				}else{
					$point->setY($point->getY() + 1);
					$point_barras->setY($point_barras->getY() + 1);
				}
			}
				
			$chart->setDataSet($dataSet);
			$chart->setTitle("Porcentaje de las prioridades de las violaciones de código cometidas");
			$chart->render("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones_ultimoIntento.png");
	
			$chart_barras->setDataSet($dataSet_barras);
			$chart_barras->setTitle("Número de violaciones cometidas por prioridad");
			$chart_barras->render("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones_ultimoIntento_barras.png");
		}
	
	}
	
	public function generarGraficaLineaPromedioErroresUnitariosViolaciones(){
	
		$chart = new \LineChart(800, 350);
		$serie_violaciones = new \XYDataSet();
		$serie_errores = new \XYDataSet();
	
		$num_alumnos_por_intento = array();
		//$alumnos = $this->obtenerAlumnos();
		$alumnos_tabla = TableRegistry::get("Alumnos");
		$alumnos = $alumnos_tabla->find('all');
		$intento_realizado = false;
	
		foreach ($alumnos as $alumno){
			//$intentos = $this->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $alumno->id);
			$intentos_tabla = TableRegistry::get("Intentos");
			$intentos = $intentos_tabla->find('all')
    								   ->where(['tarea_id' => $_SESSION["lti_idTarea"], 'alumno_id' => $alumno->id]);
			
			foreach ($intentos as $intento){
				$intento_realizado = true;
				$clave = "Intento ".$intento->numero_intento;
				if(array_key_exists($clave, $num_alumnos_por_intento)){
					$num_alumnos_por_intento[$clave] += 1;
				}
				else {
					$num_alumnos_por_intento[$clave] = 1;
				}
	
				// Violaciones
				//$num_violaciones = count($this->obtenerViolacionesPorIdIntento($intento->id));
				$violaciones_tabla = TableRegistry::get("Violaciones");
				$query = $violaciones_tabla->find('all')
									       ->where(['intento_id' => $intento->id])
									       ->toArray();
				$num_violaciones = count($query);
				
				$point_violacion = $serie_violaciones->getPointWithX($clave);
				if($point_violacion != null){
					$point_violacion->setY(($point_violacion->getY() + $num_violaciones) / $num_alumnos_por_intento[$clave]);
				}else{
					$serie_violaciones->addPoint(new \Point($clave, $num_violaciones));
				}
	
				// Errores
				//$num_errores = count($this->obtenerErroresPorIdIntento($intento->id));
				$errores_tabla = TableRegistry::get("Errores");
				$query = $errores_tabla->find('all')
									   ->where(['intento_id' => $intento->id])
									   ->toArray();
				$num_errores = count($query);
				
				$point_error = $serie_errores->getPointWithX($clave);
				if($point_error != null){
					$point_error->setY(($point_error->getY() + $num_errores) / $num_alumnos_por_intento[$clave]);
				}else{
					$serie_errores->addPoint(new \Point($clave, $num_errores));
				}
			}
		}
	
		if($intento_realizado){
			$dataSet = new \XYSeriesDataSet();
			$dataSet->addSerie("Violaciones de código", $serie_violaciones);
			$dataSet->addSerie("Errores unitarios", $serie_errores);
			$chart->setDataSet($dataSet);
			$chart->setTitle("Promedio de la clase de Violaciones-Errores por intento realizado");
			$chart->render("img/".$_SESSION["lti_idTarea"]."-prof-promedioViolacionesErrores.png");
		}
	
	}
	
	public function generarGraficaMediaErrores(){
	
		$chart = new \VerticalBarChart(800, 350);
		$serie_errores_unitarios = new \XYDataSet();
		$serie_errores_excepcion = new \XYDataSet();
	
		$num_alumnos_por_intento = array();
		//$alumnos = $this->obtenerAlumnos();
		$alumnos_tabla = TableRegistry::get("Alumnos");
		$alumnos = $alumnos_tabla->find('all');
		$intento_realizado = false;
	
		foreach ($alumnos as $alumno){
			//$intentos = $this->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $alumno->id);
			$intentos_tabla = TableRegistry::get("Intentos");
			$intentos = $intentos_tabla->find('all')
    								   ->where(['tarea_id' => $_SESSION["lti_idTarea"], 'alumno_id' => $alumno->id]);
			
			foreach ($intentos as $intento){
				$intento_realizado = true;
				$clave = "Intento ".$intento->numero_intento;
				if(array_key_exists($clave, $num_alumnos_por_intento)){
					$num_alumnos_por_intento[$clave] += 1;
				}
				else {
					$num_alumnos_por_intento[$clave] = 1;
				}
	
				// Errores
				$num_errores_unitarios = 0;
				$num_errores_excepcion = 0;
				//$errores = $this->obtenerErroresPorIdIntento($intento->id);
				$errores_tabla = TableRegistry::get("Errores");
				$errores = $errores_tabla->find('all')
							       		 ->where(['intento_id' => $intento->id])
							       	     ->toArray();
				
				foreach ($errores as $error){
					if($error->tipo_error == "failure"){
						$num_errores_unitarios++;
					}else{
						$num_errores_excepcion++;
					}
				}
	
				$point_error_unitario = $serie_errores_unitarios->getPointWithX($clave);
				if($point_error_unitario != null){
					$point_error_unitario->setY(($point_error_unitario->getY() + $num_errores_unitarios) / $num_alumnos_por_intento[$clave]);
				}else{
					$serie_errores_unitarios->addPoint(new \Point($clave, $num_errores_unitarios));
				}
	
				$point_error_excepcion = $serie_errores_excepcion->getPointWithX($clave);
				if($point_error_excepcion != null){
					$point_error_excepcion->setY(($point_error_excepcion->getY() + $num_errores_excepcion) / $num_alumnos_por_intento[$clave]);
				}else{
					$serie_errores_excepcion->addPoint(new \Point($clave, $num_errores_excepcion));
				}
	
			}
		}
	
		if($intento_realizado){
			$dataSet = new \XYSeriesDataSet();
			$dataSet->addSerie("Unitarios", $serie_errores_unitarios);
			$dataSet->addSerie("Excepciones", $serie_errores_excepcion);
			$chart->setDataSet($dataSet);
			$chart->getPlot()->setGraphCaptionRatio(0.75);
			$chart->setTitle("Promedio de errores cometidos por intento");
			$chart->render("img/".$_SESSION["lti_idTarea"]."-prof-promedioErroresUnitariosExcepciones.png");
		}
	
	}
	
	public function generarGraficaVerticalAlumnosViolacionesCometidas(){
	
		//$alumnos = $this->obtenerAlumnos();
		$alumnos_tabla = TableRegistry::get("Alumnos");
		$alumnos = $alumnos_tabla->find('all');
		$intentos_realizados = false;
	
		$chart = new \VerticalBarChart(800, 350);
		$dataSet = new \XYDataSet();
		$this->__añadirIntervalosViolacionesEjeX($dataSet);
	
		foreach($alumnos as $alumno){
			//$intentos_alumno = $this->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $alumno->id);
			$intentos_tabla = TableRegistry::get("Intentos");
			$intentos_alumno = $intentos_tabla->find('all')
    										  ->where(['tarea_id' => $_SESSION["lti_idTarea"], 'alumno_id' => $alumno->id]);
			
			if(!$intentos_alumno->isEmpty()){
				$intentos_realizados = true;
				$total_violaciones = 0;
				foreach($intentos_alumno as $intento){
					//$violaciones = $this->obtenerViolacionesPorIdIntento($intento->id);
					$violaciones_tabla = TableRegistry::get("Violaciones");
					$violaciones = $violaciones_tabla->find('all')
												     ->where(['intento_id' => $intento->id])
												     ->toArray();
					
					$total_violaciones += count($violaciones);
				}
				$intervalo = $this->__obtenerIntervaloViolacion($total_violaciones);
				$point = $dataSet->getPointWithX($intervalo);
				$point->setY($point->getY() + 1);
			}
		}
	
		if($intentos_realizados){
			$chart->setDataSet($dataSet);
			$chart->setTitle("Número de alumnos que cometen X violaciones");
			$chart->render("img/".$_SESSION["lti_idTarea"]."-prof-violaciones_intervalos.png");
		}
	
	}
	
	private function __añadirIntervalosViolacionesEjeX($dataSet){
	
		$dataSet->addPoint(new \Point("[0]", 0));
		$dataSet->addPoint(new \Point("[1,10]", 0));
		$dataSet->addPoint(new \Point("[11,20]", 0));
		$dataSet->addPoint(new \Point("[21,30]", 0));
		$dataSet->addPoint(new \Point("[31,40]", 0));
		$dataSet->addPoint(new \Point("[41,50]", 0));
		$dataSet->addPoint(new \Point("[51,60]", 0));
		$dataSet->addPoint(new \Point("[61,70]", 0));
		$dataSet->addPoint(new \Point("[71,80]", 0));
		$dataSet->addPoint(new \Point("[81,+]", 0));
	
	}
	
	private function __obtenerIntervaloViolacion($valor){
	
		switch($valor){
			case 0:
				return "[0]";
				break;
			case $valor >= 1 && $valor <= 10:
				return "[1,10]";
				break;
			case $valor >= 11 && $valor <= 20:
				return "[11,20]";
				break;
			case $valor >= 21 && $valor <= 30:
				return "[21,30]";
				break;
			case $valor >= 31 && $valor <= 40:
				return "[31,40]";
				break;
			case $valor >= 41 && $valor <= 50:
				return "[41,50]";
				break;
			case $valor >= 51 && $valor <= 60:
				return "[51,60]";
				break;
			case $valor >= 61 && $valor <= 70:
				return "[61,70]";
				break;
			case $valor >= 71 && $valor <= 80:
				return "[71,80]";
				break;
			case $valor >= 81:
				return "[81,+]";
				break;
		}
	
	}
	
	public function generarGraficaVerticalAlumnosIntentos(){

		//$alumnos = $this->obtenerAlumnos();
		$alumnos_tabla = TableRegistry::get("Alumnos");
		$alumnos = $alumnos_tabla->find('all');
	
		$intentos_pasa_test = false;
		$intentos_no_pasa_test = false;
	
		$chart_pasa_test = new \VerticalBarChart(800, 350);
		$dataSet_pasa_test = new \XYDataSet();
		$this->__añadirIntervalosIntentosEjeX($dataSet_pasa_test);
	
		$chart_no_pasa_test = new \VerticalBarChart(800, 350);
		$dataSet_no_pasa_test = new \XYDataSet();
		$this->__añadirIntervalosIntentosEjeX($dataSet_no_pasa_test);
	
		if(file_exists("img/".$_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest.png")){
			unlink("img/".$_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest.png");
		}
	
		foreach($alumnos as $alumno){
			//$intentos_alumno = $this->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $alumno->id);	
			$intentos_tabla = TableRegistry::get("Intentos");
			$intentos_alumno = $intentos_tabla->find('all')
    										  ->where(['tarea_id' => $_SESSION["lti_idTarea"], 'alumno_id' => $alumno->id]);
			
			if(!$intentos_alumno->isEmpty()){
				$num_intentos_realizados = 0;
				$test_pasados = false;
				foreach($intentos_alumno as $intento){
					$num_intentos_realizados++;
					if($intento->resultado == 1){	// Test pasados
						$intentos_pasa_test = true;
						$test_pasados = true;
						$intervalo = $this->__obtenerIntervaloIntento($num_intentos_realizados);
						$point = $dataSet_pasa_test->getPointWithX($intervalo);
						$point->setY($point->getY() + 1);
						break;
					}
				}
				if(!$test_pasados){		// Test no pasados
					$intentos_no_pasa_test = true;
					$intervalo = $this->__obtenerIntervaloIntento($num_intentos_realizados);
					$point = $dataSet_no_pasa_test->getPointWithX($intervalo);
					$point->setY($point->getY() + 1);
				}
			}
		}
	
		if($intentos_pasa_test){
			$chart_pasa_test->setDataSet($dataSet_pasa_test);
			$chart_pasa_test->setTitle("Número de alumnos que han realizado X intentos para pasar los test");
			$chart_pasa_test->render("img/".$_SESSION["lti_idTarea"]."-prof-intentos_pasaTest_intervalos.png");
		}
		if($intentos_no_pasa_test){
			$chart_no_pasa_test->setDataSet($dataSet_no_pasa_test);
			$chart_no_pasa_test->setTitle("Número de alumnos que han realizado X intentos sin conseguir pasar los test");
			$chart_no_pasa_test->render("img/".$_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest_intervalos.png");
		}
	
	}
	
	private function __añadirIntervalosIntentosEjeX($dataSet){
	
		$dataSet->addPoint(new \Point("[0]", 0));
		$dataSet->addPoint(new \Point("[1,3]", 0));
		$dataSet->addPoint(new \Point("[4,6]", 0));
		$dataSet->addPoint(new \Point("[7,9]", 0));
		$dataSet->addPoint(new \Point("[10,12]", 0));
		$dataSet->addPoint(new \Point("[13,15]", 0));
		$dataSet->addPoint(new \Point("[16,18]", 0));
		$dataSet->addPoint(new \Point("[19,21]", 0));
		$dataSet->addPoint(new \Point("[22,24]", 0));
		$dataSet->addPoint(new \Point("[25,+]", 0));
	
	}
	
	private function __obtenerIntervaloIntento($valor){
	
		switch($valor){
			case 0:
				return "[0]";
				break;
			case $valor >= 1 && $valor <= 3:
				return "[1,3]";
				break;
			case $valor >= 4 && $valor <= 6:
				return "[4,6]";
				break;
			case $valor >= 7 && $valor <= 9:
				return "[7,9]";
				break;
			case $valor >= 10 && $valor <= 12:
				return "[10,12]";
				break;
			case $valor >= 13 && $valor <= 15:
				return "[13,15]";
				break;
			case $valor >= 16 && $valor <= 18:
				return "[16,18]";
				break;
			case $valor >= 19 && $valor <= 21:
				return "[19,21]";
				break;
			case $valor >= 22 && $valor <= 24:
				return "[22,24]";
				break;
			case $valor >= 25:
				return "[25,+]";
				break;
		}
	
	}
	
	public function generarGraficaAlumnosTest(){

		//$alumnos = $this->obtenerAlumnos();
		$alumnos_tabla = TableRegistry::get("Alumnos");
		$alumnos = $alumnos_tabla->find('all');
		$alumnos_registrados = false;
		$alumnos_pasan_test = 0;
		$alumnos_no_pasan_test = 0;
	
		foreach($alumnos as $alumno){
			$alumnos_registrados = true;
			//$intentos_pasan_test = $this->obtenerIntentosTestPasados($_SESSION["lti_idTarea"], $alumno->id);
			$intentos_tabla = TableRegistry::get("Intentos");
			$intentos_pasan_test = $intentos_tabla->find('all')
    											  ->where(['tarea_id' => $_SESSION["lti_idTarea"], 'alumno_id' => $alumno->id, 'resultado' => 1]);
			
			if($intentos_pasan_test->isEmpty()){
				$alumnos_no_pasan_test++;
			}
			else{
				$alumnos_pasan_test++;
			}
		}
	
		if($alumnos_registrados){	// hay alumnos
			$chart = new \PieChart(800, 350);
			$dataSet = new \XYDataSet();
			$dataSet->addPoint(new \Point("Alumnos que pasan los test", $alumnos_pasan_test));
			$dataSet->addPoint(new \Point("Alumnos que no pasan los test", $alumnos_no_pasan_test));
			$chart->setDataSet($dataSet);
			$chart->setTitle("Porcentaje de alumnos que pasan y no pasan los test");
			$chart->render("img/".$_SESSION["lti_idTarea"]."-prof-test.png");
		}
	
	}
	
	public function generarGraficaMedias(){
	
		$chart = new \HorizontalBarChart(800, 350);
		$dataSet = new \XYDataSet();
		
		$this->__calcularMediaIntentos();
		$this->__calcularMediaViolaciones();
	
		if($_SESSION["media_intentos_no_pasa_test"] != false)
			$dataSet->addPoint(new \Point("Intentos sin pasar los test", $_SESSION["media_intentos_no_pasa_test"]));
			if($_SESSION["media_intentos_pasa_test"] != false)
				$dataSet->addPoint(new \Point("Intentos para pasar los test", $_SESSION["media_intentos_pasa_test"]));
				if($_SESSION["media_violaciones"] != false)
					$dataSet->addPoint(new \Point("Violaciones de código", $_SESSION["media_violaciones"]));
	
					if($_SESSION["media_intentos_no_pasa_test"] != false || $_SESSION["media_intentos_pasa_test"] != false ||
							$_SESSION["media_violaciones"] != false){
								$chart->setDataSet($dataSet);
								$chart->getPlot()->setGraphPadding(new \Padding(5, 30, 20, 140));
								$chart->setTitle("MEDIAS");
								$chart->render("img/".$_SESSION["lti_idTarea"]."-prof-medias.png");
					}
	
	}
	
	private function __calcularMediaIntentos(){
	
		//$alumnos = $this->obtenerAlumnos();
		$alumnos_tabla = TableRegistry::get("Alumnos");
		$alumnos = $alumnos_tabla->find('all');
	
		$_SESSION["media_intentos_pasa_test"] = false;
		$_SESSION["media_intentos_no_pasa_test"] = false;
		$intentos_pasa_test = false;
		$intentos_no_pasa_test = false;
		$num_alumnos_pasan_test = 0;
		$num_alumnos_no_pasan_test = 0;
		$total_intentos_pasan_test = 0;
		$total_intentos_no_pasan_test = 0;
	
		if(file_exists("img/".$_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest.png")){
			unlink("img/".$_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest.png");
		}
	
		foreach($alumnos as $alumno){
			//$intentos_alumno = $this->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $alumno->id);
			$intentos_tabla = TableRegistry::get("Intentos");
			$intentos_alumno = $intentos_tabla->find('all')
    										  ->where(['tarea_id' => $_SESSION["lti_idTarea"], 'alumno_id' => $alumno->id]);	
			
			if(!$intentos_alumno->isEmpty()){
				$num_intentos_realizados = 0;
				$test_pasados = false;
				foreach($intentos_alumno as $intento){
					$num_intentos_realizados++;
					if($intento->resultado == 1){	// Test pasados
						$intentos_pasa_test = true;
						$test_pasados = true;
						$num_alumnos_pasan_test++;
						$total_intentos_pasan_test += $num_intentos_realizados;
						break;
					}
				}
				if(!$test_pasados){		// Test no pasados
					$intentos_no_pasa_test = true;
					$num_alumnos_no_pasan_test++;
					$total_intentos_no_pasan_test += $num_intentos_realizados;
				}
			}
		}
	
		if($intentos_pasa_test){
			$_SESSION["media_intentos_pasa_test"] = round($total_intentos_pasan_test/$num_alumnos_pasan_test, 2);
		}
		if($intentos_no_pasa_test){
			$_SESSION["media_intentos_no_pasa_test"] = round($total_intentos_no_pasan_test/$num_alumnos_no_pasan_test, 2);
		}
	
	}
	
	private function __calcularMediaViolaciones(){

		//$alumnos = $this->obtenerAlumnos();
		$alumnos_tabla = TableRegistry::get("Alumnos");
		$alumnos = $alumnos_tabla->find('all');
	
		$_SESSION["media_violaciones"] = false;
		$intentos_realizados = false;
		$total_violaciones = 0;
		$num_alumnos = 0;
	
		foreach($alumnos as $alumno){
			//$intentos_alumno = $this->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $alumno->id);
			$intentos_tabla = TableRegistry::get("Intentos");
			$intentos_alumno = $intentos_tabla->find('all')
											  ->where(['tarea_id' => $_SESSION["lti_idTarea"], 'alumno_id' => $alumno->id]);
			
			if(!$intentos_alumno->isEmpty()){
				$intentos_realizados = true;
				$total_violaciones_alumno = 0;
				$num_alumnos += 1;
				foreach($intentos_alumno as $intento){
					//$violaciones = $this->obtenerViolacionesPorIdIntento($intento->id);
					$violaciones_tabla = TableRegistry::get("Violaciones");
					$violaciones = $violaciones_tabla->find('all')
												     ->where(['intento_id' => $intento->id])
												     ->toArray();
					
					$total_violaciones_alumno += count($violaciones);
				}
				$total_violaciones += $total_violaciones_alumno;
			}
		}
	
		if($intentos_realizados){
			$_SESSION["media_violaciones"] = round($total_violaciones/$num_alumnos, 2);
		}
	
	}
	
}

?>