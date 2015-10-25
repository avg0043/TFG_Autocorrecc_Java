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
function connectLTI()
{
    //Objeto con la conexión LTI
    $context = new BLTI("secret", true, false);

    //Conexión LTI inválida
    if ($context->valid == false) {
    	// CREO QUE AQUÍ ENTRA CUANDO EL SECRETO NO ES EL CORRECTO, ES DECIR NO ES EL Q SE LE PASA AL BLTI
        echo "No se ha podido establecer una conexión LTI válida.";
        die;
    }else{
    	// Se comprueba que el consumer_key sea el correcto
    	$bd = new BaseDeDatos();
    	$conexion_bd = $bd->conectar();
    	$consumer_key_encriptada = md5($context->info["oauth_consumer_key"]);
    	
    	$valores = mysqli_query($conexion_bd, "select * from tfg_lti_claves where oauth_consumer_key='$consumer_key_encriptada'") or
    								die("Problemas en el select:".mysqli_error($conexion));
    	
    	if($val = mysqli_fetch_array($valores)){
    		return $context;
    	}else{
    		echo "El usuario con esa key NO existe";
    	}
    }

}


