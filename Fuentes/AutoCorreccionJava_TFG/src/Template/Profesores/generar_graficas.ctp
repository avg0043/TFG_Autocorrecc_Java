<div class="page-header">
	<h3>Gráficas</h3>
</div>

<?php if(file_exists("img/".$_SESSION["lti_idTarea"]."-prof-alumnos_test.png")){?>
	  	<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-alumnos_test.png" ?>" style="border: 1px solid gray;"/>
<?php } 
	  if(file_exists("img/".$_SESSION["lti_idTarea"]."-prof-intentos_pasaTest.png")){?>
	  	<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-intentos_pasaTest.png" ?>" style="border: 1px solid gray;"/>
<?php }
	  if(file_exists("img/".$_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest.png")){?>
	  	<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest.png" ?>" style="border: 1px solid gray;"/>
<?php }
	  if(file_exists("img/".$_SESSION["lti_idTarea"]."-prof-violaciones_alumnos.png")){?>
	  	<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-violaciones_alumnos.png" ?>" style="border: 1px solid gray;"/>
<?php }
