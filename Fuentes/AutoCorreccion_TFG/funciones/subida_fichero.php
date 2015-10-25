<?php

// Codificación
header('Content-Type: text/html; charset=UTF-8');

session_start();

$extension = pathinfo($_FILES["ficheroAsubir"]["name"], PATHINFO_EXTENSION);

// ---------- COMPROBACIÓN DEL NÚMERO DE INTENTOS ?¿?¿?¿?¿?

if($extension != "java"){
	echo "Debes de subir un fichero de extensión .java!";
}else{
	
	$crear_carpetas = false;
	$directorio_destino = "../" . $_SESSION["lti_tituloCurso"] . "/" . $_SESSION["lti_tituloActividad"] . "/"
						  . $_SESSION["lti_rol"] . "/" . $_SESSION["lti_userId"] . "/";;
	
	// Creación si no existe de la carpeta: título del cruso
	if(!is_dir("../" . $_SESSION["lti_tituloCurso"] . "/")){
		$crear_carpetas = true;
	}else{
		
		// Creación si no existe de la carpeta: título de la actividad
		if(!is_dir("../" . $_SESSION["lti_tituloCurso"] . "/" . $_SESSION["lti_tituloActividad"] . "/")){
			$crear_carpetas = true;		
		}else{
			
			// Creación si no existe de la carpeta: rol del usuario
			if(!is_dir("../" . $_SESSION["lti_tituloCurso"] . "/" . $_SESSION["lti_tituloActividad"] . "/"
					. $_SESSION["lti_rol"] . "/")){
				$crear_carpetas = true;
			}else{
				
				// Creación si no existe de la carpeta: id del usuario
				if(!is_dir("../" . $_SESSION["lti_tituloCurso"] . "/" . $_SESSION["lti_tituloActividad"] . "/"
						. $_SESSION["lti_rol"] . "/" . $_SESSION["lti_userId"] . "/")){
					$crear_carpetas = true;
				}
			}
		}
		
	}
	
	if($crear_carpetas){
		mkdir($directorio_destino, 0777, true);
	}
	
	// Se guarda el fichero
	$path_fichero = $directorio_destino . $_FILES["ficheroAsubir"]["name"];
	move_uploaded_file($_FILES["ficheroAsubir"]["tmp_name"], $path_fichero);
	echo "Fichero guardado correctamente <br>";
	
}

echo "<a href='../Formulario_subidaFichero.php'>Formulario para la subida de ficheros </a>";

