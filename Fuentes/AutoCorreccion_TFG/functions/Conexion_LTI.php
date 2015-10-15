<?php

require_once("/../ims-blti/blti.php");
require_once("/../config.php");

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
    $context = new BLTI($GLOBALS['ltiSecret'], true, false);

    //Conexión LTI inválida
    if ($context->valid == false) {
        echo "No se ha podido establecer una conexión LTI válida.";
        die;
    }

    return $context;
}


