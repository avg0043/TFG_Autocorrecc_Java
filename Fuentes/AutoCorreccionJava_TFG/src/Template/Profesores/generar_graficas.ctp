<!--  
<div class="page-header">
	<h3>Gráficas</h3>
</div>
-->

<nav class="navbar navbar-inverse">
  <ul class="nav navbar-nav">
  	<li><a href="http://localhost/AutoCorreccionJava_TFG/profesores/mostrar-panel">Inicio</a></li>
  </ul>
</nav>

<div class="jumbotron">
  <h3>Gráficas</h3>
  <p>Selecciona las gráficas que deseas visualizar.</p>
</div>

<?php if(file_exists("img/".$_SESSION["lti_idTarea"]."-prof-test.png")){?>
	  	<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-test.png" ?>" style="border: 1px solid gray;"/>
<?php }
	  if(file_exists("img/".$_SESSION["lti_idTarea"]."-prof-medias.png")){?>
	  	<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-medias.png" ?>" style="border: 1px solid gray;"/>
<?php } 
	  if(file_exists("img/".$_SESSION["lti_idTarea"]."-prof-intentos_pasaTest.png")){?>
	  	<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-intentos_pasaTest.png" ?>" style="border: 1px solid gray;"/>
<?php }
	  if(file_exists("img/".$_SESSION["lti_idTarea"]."-prof-intentos_pasaTest_intervalos.png")){?>
	  	<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-intentos_pasaTest_intervalos.png" ?>" style="border: 1px solid gray;"/>
<?php }
	  if(file_exists("img/".$_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest.png")){?>
	  	<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest.png" ?>" style="border: 1px solid gray;"/>
<?php }
	  if(file_exists("img/".$_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest_intervalos.png")){?>
	  	<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest_intervalos.png" ?>" style="border: 1px solid gray;"/>
<?php }
	  if(file_exists("img/".$_SESSION["lti_idTarea"]."-prof-violaciones.png")){?>
	  	<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-violaciones.png" ?>" style="border: 1px solid gray;"/>
<?php }
	  if(file_exists("img/".$_SESSION["lti_idTarea"]."-prof-violaciones_intervalos.png")){?>
	  	<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-violaciones_intervalos.png" ?>" style="border: 1px solid gray;"/>
<?php } ?>
