<?php

session_start();

// Acceso a la información del objeto LTI obtenido
echo "<h3>" . "Información del Usuario" . "</h3>";
echo "<ul>";
echo "<li> Nombre completo: " . $_SESSION["lti_nombreCompleto"] . "</li>";
echo "<li> Correo electrónico: " . $_SESSION["lti_correo"] . "</li>";
echo "<li> Rol: " . $_SESSION["lti_rol"] . "</li>";
echo "<li> ID: " . $_SESSION["lti_userId"] . "</li>";
echo "</ul>";