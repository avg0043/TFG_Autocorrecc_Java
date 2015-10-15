<?php

// FUNCIONES

function desencripta($clave_a_desencriptar){

	$data = base64_decode($clave_a_desencriptar);
	$iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));

	$decrypted = rtrim(
	    mcrypt_decrypt(
	        MCRYPT_RIJNDAEL_128,
	        hash('sha256', $GLOBALS['clave_maestra'], true),
	        substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)),
	        MCRYPT_MODE_CBC,
	        $iv
	    ),
	    "\0"
	);

	return $decrypted;

}

// DATOS

$clave_maestra = "maestro";  
$correct_key = 'key123';        // clave pública 
$correct_secret = 'secret123';  // clave secreta

// BASE DE DATOS

$conexion = mysqli_connect("localhost","root","","base1") or die("Problemas con la conexión");

// consulta de las claves de la BD
$registros = mysqli_query($conexion,"select clave, secreta from usuarios") or
  			die("Problemas en el select:".mysqli_error($conexion));

if ($reg = mysqli_fetch_array($registros))
{
	// desencriptación de las claves
	$key_desencriptada = desencripta($reg['clave']);
	$secret_desencriptada = desencripta($reg['secreta']);

	// comprobación de si las claves desencriptadas se corresponden con las correctas
	if($key_desencriptada == $correct_key && $secret_desencriptada == $correct_secret){
		echo "Correcto! <br>";
  		echo "- Clave: ". $key_desencriptada . "<br>";
  		echo "- Secreta: ". $secret_desencriptada;
	}
	else{
		echo "Las claves no se corresponden.";
	}
}
else
{
  echo "No existe un usuario con esas claves.";
}

mysqli_close($conexion);

?>