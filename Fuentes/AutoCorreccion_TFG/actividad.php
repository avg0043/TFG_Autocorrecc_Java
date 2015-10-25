<?php

require_once("funciones/Conexion_LTI.php");

session_start();

// Llamada a connectLTI para hacer una conexión LTI segura
$ltiObject = connectLTI();

if(isset($ltiObject)){

	// Almacenamiento de la información LTI 
	$_SESSION["lti_tituloActividad"] = $ltiObject->info["resource_link_title"];
	$_SESSION["lti_nombreCompleto"] = $ltiObject->info["lis_person_name_full"];
	$_SESSION["lti_correo"] = $ltiObject->info["lis_person_contact_email_primary"];
	$_SESSION["lti_rol"] = $ltiObject->info["roles"];
	$_SESSION["lti_userId"] = $ltiObject->info["user_id"];
	$_SESSION["lti_tituloLink"] = $ltiObject->info["resource_link_title"];
	$_SESSION["lti_tituloCurso"] = $ltiObject->info["context_title"];
	
	// Acceso a la información del objeto LTI obtenido
	echo "<a href='Usuario_info.php'>Información del Usuario </a>";
	
	echo "<br>";
	
	// Formulario subida de ficheros
	echo "<a href='Formulario_subidaFichero.php'>Formulario para la subida de ficheros </a>";
	
}






