<?php

namespace App\Controller;

class FicherosXmlController extends AppController{
	
	public function editarPomArquetipoMaven($ruta_carpeta_id){
		
		$pom_xml = simplexml_load_file($ruta_carpeta_id . 'arquetipo/pom.xml');
		
		// Codificación
		$properties = $pom_xml->addChild('properties');
		$properties->addChild("project.build.sourceEncoding", "UTF-8");
		
		// Plugins
		$reporting = $pom_xml->addChild('reporting');
		$plugins = $reporting->addChild("plugins");
		
		// Plugin Surfire
		$plugin_surfire = $plugins->addChild("plugin");
		$plugin_surfire->addChild("groupId", "org.apache.maven.plugins");
		$plugin_surfire->addChild("artifactId", "maven-surefire-report-plugin");
		
		// Plugin JavaNCSS
		$plugin_javancss = $plugins->addChild("plugin");
		$plugin_javancss->addChild("groupId", "org.codehaus.mojo");
		$plugin_javancss->addChild("artifactId", "javancss-maven-plugin");
		
		// Plugin JDepend
		$plugin_jdepend = $plugins->addChild("plugin");
		$plugin_jdepend->addChild("groupId", "org.codehaus.mojo");
		$plugin_jdepend->addChild("artifactId", "jdepend-maven-plugin");
		
		// Plugin PMD
		$plugin_pmd = $plugins->addChild("plugin");
		$plugin_pmd->addChild("groupId", "org.apache.maven.plugins");
		$plugin_pmd->addChild("artifactId", "maven-pmd-plugin");
		
		// Plugin FindBugs
		$plugin_findbugs = $plugins->addChild("plugin");
		$plugin_findbugs->addChild("groupId", "org.codehaus.mojo");
		$plugin_findbugs->addChild("artifactId", "findbugs-maven-plugin");
		
		$pom_xml->asXml($ruta_carpeta_id . 'arquetipo/pom.xml');
		
	}
	
	public function guardarDatosXmlPluginPmd($ruta_carpeta_id, $id_intento, $intento_realizado){
		
		$violaciones_controller = new ViolacionesController();
		$xml_pmd = simplexml_load_file($ruta_carpeta_id. $intento_realizado . "/pmd.xml");
		
		foreach($xml_pmd->children() as $files){
			foreach($files->children() as $violations){
				$violaciones_controller->guardarViolacion($id_intento, $violations["class"].".java",
						$violations["rule"], $violations, $violations["priority"],
						$violations["beginline"], $violations["endline"]);
			}
		}
		
	}
	
	public function guardarDatosXmlPluginFindbugs($ruta_carpeta_id, $id_intento, $intento_realizado){
		
		$violaciones_controller = new ViolacionesController();
		$xml_findbugs = simplexml_load_file($ruta_carpeta_id. $intento_realizado . "/findbugsXml.xml");
		
		foreach($xml_findbugs->children()->BugInstance as $bug_instances){
			$_SESSION["findbugs_generado"] = true;
			if($bug_instances->SourceLine["start"] != null && $bug_instances->SourceLine["end"] != null){
				$violaciones_controller->guardarViolacion($id_intento, $bug_instances->Class->SourceLine["sourcefile"],
						$bug_instances["type"], $bug_instances->LongMessage, $bug_instances["priority"],
						$bug_instances->SourceLine["start"], $bug_instances->SourceLine["end"]);
			}
			else{
				$violaciones_controller->guardarViolacion($id_intento, $bug_instances->Class->SourceLine["sourcefile"],
						$bug_instances["type"], $bug_instances->LongMessage, $bug_instances["priority"]);
			}
		}
		
		if(!$_SESSION["findbugs_generado"]){
			unlink("../../".$_SESSION['lti_idCurso']."/".$_SESSION['lti_idTarea']."/".$_SESSION['lti_rol'].
					"/".$_SESSION['lti_userId']."/".$intento_realizado."/site/findbugs.html");
		}
		
	}
	
	public function guardarDatosXmlErroresUnitarios($ruta_carpeta_id, $id_intento, $intento_realizado){
		
		$errores_controller = new ErroresController();
		$ficheros_xml = glob($ruta_carpeta_id . $intento_realizado . "/surefire-reports/*xml");
		
		foreach($ficheros_xml as $fichero) {
			$xml = simplexml_load_file($fichero);
			$fallos = (int) $xml["failures"];
			$errores = (int) $xml["errors"];
			if($fallos > 0){	// test que falla por assert
				foreach($xml->children()->testcase as $test_case){
					if(isset($test_case->failure)){
						$errores_controller->guardarError($id_intento, $test_case["classname"], $test_case["name"],
								"failure", $test_case->failure["type"], $test_case->failure);
					}
				}
			}elseif($errores > 0){	// test que falla por excepción
				foreach($xml->children()->testcase as $test_case){
					if(isset($test_case->error)){
						$errores_controller->guardarError($id_intento, $test_case["classname"], $test_case["name"],
								"error", $test_case->error["type"]);
					}
				}
			}
		}
		
	}
	
}

?>