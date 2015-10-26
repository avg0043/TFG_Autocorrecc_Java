<?php

require_once("/../ims-blti/blti.php");
require_once("/../BaseDeDatos.php");

/**
 * Función que realiza la conexión LTI.
 * Si el proceso de conexión ha sido válido devolverá
 * el correspondiente objeto LTI, y en caso contrario
 * mostrará un mensaje indicando que no ha sido posible.
 * 
 * @return $context objeto LTI.
 */
function connectLTI($consumer_key)
{

	// Conexión con la base de datos
	$bd = new BaseDeDatos();
	$conexion_bd = $bd->conectar();
	
	// Comprobación de que el consumer_key sea el correcto
	$consumer_key_encriptada = encriptar($consumer_key);	 
	$valores = mysqli_query($conexion_bd, "select * from tfg_lti_claves where oauth_consumer_key='$consumer_key_encriptada'") or
							die("Problemas en el select:".mysqli_error($conexion_bd));
	 
	if($val = mysqli_fetch_array($valores)){
		
		$secret_encriptada = $val["secret"];
		$secret_desencriptada = desencriptar($secret_encriptada);
		$context = new BLTI($secret_desencriptada, true, false);
		
		if($context->valid == false){
			echo "No se ha podido establecer una conexión LTI válida, el shared secret no es el correcto.";
			die;
		}else{
			return $context;
		}
		
	}else{
		echo "La key introducida no es la correcta.";
	}

}

function encriptar($cadena){
	
	$key='clave_codificacion';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
	$encriptada = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $cadena, MCRYPT_MODE_CBC, md5(md5($key))));
	return $encriptada; //Devuelve el string encriptado

}

function desencriptar($cadena){
	
	$key='clave_codificacion';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
	$desencriptada = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($cadena), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	return $desencriptada;  //Devuelve el string desencriptado
	
}


