<?php

include_once "oauth-php/library/OAuthStore.php";
include_once "oauth-php/library/OAuthRequester.php";

// FUNCIONES

function encripta($clave_a_encriptar){

    $iv = mcrypt_create_iv(
        mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC),
        MCRYPT_DEV_URANDOM
    );

    $encrypted = base64_encode(
        $iv .
        mcrypt_encrypt(
            MCRYPT_RIJNDAEL_128,
            hash('sha256', $GLOBALS['clave_maestra'], true),
            $clave_a_encriptar,
            MCRYPT_MODE_CBC,
            $iv
        )
    );

    return $encrypted;

}

// DATOS

$clave_maestra = "maestro";                           // clave para realizar el proceso de encriptado y desencriptado
$key = 'key123';                                      // clave pública 
$secret = 'secret123';                                // clave secreta
$url = "http://pruebas/oauth-ejemplo/server.php";     // URL del servidor

$options = array('consumer_key' => $key, 'consumer_secret' => $secret);
OAuthStore::instance("2Leg", $options);

$method = "GET";
$params = null;

// BASE DE DATOS

// encriptación de las claves
$key_encriptada = encripta($key);
$secret_encriptada = encripta($secret);

// inserción de las claves en la BD
$conexion = mysqli_connect("localhost","root","","base1") or die("Problemas con la conexión");
mysqli_query($conexion,"insert into usuarios(clave, secreta) values ('$key_encriptada', '$secret_encriptada')")
            or die("Problemas en el select".mysqli_error($conexion));
mysqli_close($conexion);

// CONEXIÓN CON EL SERVIDOR
try
{
        // Obtain a request object for the request we want to make
        $request = new OAuthRequester($url, $method, $params);

        // Sign the request, perform a curl request and return the results, 
        // throws OAuthException2 exception on an error
        // $result is an array of the form: array ('code'=>int, 'headers'=>array(), 'body'=>string)
        $result = $request->doRequest();
        
        $response = $result['body'];
        var_dump($response);

}
catch(OAuthException2 $e)
{
        echo "Exception" . $e->getMessage();
}


?>