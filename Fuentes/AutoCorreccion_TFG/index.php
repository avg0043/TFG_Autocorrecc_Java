<?php

require_once("functions/Conexion_LTI.php");

//Llamada a connectLTI para hacer una conexión LTI segura basada en la info de config.php
$ltiObject = connectLTI();

//Acceso a la información del objeto LTI obtenido
echo "<h3>" . "Información del Usuario" . "</h3>";
echo "- Nombre completo: " . $ltiObject->info['lis_person_name_full'];
echo "<br>";
echo "- Correo electrónico: " . $ltiObject->info['lis_person_contact_email_primary'];
echo "<br>";
echo "- Rol: " . $ltiObject->info['roles'];
