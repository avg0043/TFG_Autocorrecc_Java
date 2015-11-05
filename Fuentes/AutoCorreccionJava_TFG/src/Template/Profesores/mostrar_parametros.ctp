<h1>Datos Profesor</h1>
<?php

session_start();

?>

<ul>
	<li><b>Consumer key: </b><?= $_SESSION['datosProfesor']->oauth_key ?></li>
	<li><b>Secret: </b><?= $_SESSION['datosProfesor']->secret ?></li>
</ul>


