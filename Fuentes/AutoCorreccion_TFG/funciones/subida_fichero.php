<?php

// Codificación
header('Content-Type: text/html; charset=UTF-8');

session_start();

$extension = pathinfo($_FILES["ficheroAsubir"]["name"], PATHINFO_EXTENSION);

if($extension != "java"){
	echo "Debes de subir un fichero de extensión .java!";
}else{
	
	if($_SESSION["num_max_intentos"] == 0){
		echo "No puedes subir más veces la práctica <br>";
	}else{
		
		$directorio_destino = "../" . $_SESSION["lti_tituloCurso"] . "/" . $_SESSION["lti_tituloActividad"] . "/"
							  . $_SESSION["lti_rol"] . "/" . $_SESSION["lti_userId"] . "/" . date("Y-m-d H,i,s") . "/";
		
		// Creación de la/s carpeta/s
		mkdir($directorio_destino, 0777, true);
		
		// Se guarda el fichero
		$path_fichero = $directorio_destino . $_FILES["ficheroAsubir"]["name"];
		move_uploaded_file($_FILES["ficheroAsubir"]["tmp_name"], $path_fichero);
		echo "Fichero guardado correctamente <br>";
		
		$_SESSION["num_max_intentos"]--;
	}
	
}

echo "<a href='../Formulario_subidaFichero.php'>Formulario para la subida de ficheros </a>";

