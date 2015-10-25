<?php
echo "<h3>" . "Formulario subida de ficheros" . "</h3>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plantilla básica de Bootstrap</title>
 
    <!-- CSS de Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    
	<form class="col-md-4" action="funciones/subida_fichero.php" method="post" enctype="multipart/form-data">
	    Selecciona la practica (.java) a subir (con sintaxis "practicaX_nombreAlumno.java"):
	    <div class="form-group">
	    	<input type="file" class="btn btn-default" name="ficheroAsubir">
	    </div>
	    <div class="form-group">
	    	<input type="submit" class="btn btn-primary btn-lg" value="Subir fichero" name="submit">
	    </div>
	</form>
</body>
</html>
