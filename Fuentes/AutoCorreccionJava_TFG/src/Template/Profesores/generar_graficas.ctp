<!--  
<div class="page-header">
	<h3>Gráficas</h3>
</div>
-->

<nav class="navbar navbar-inverse">
  <ul class="nav navbar-nav">
  	<li><a href="http://localhost/AutoCorreccionJava_TFG/profesores/mostrar-panel">Inicio</a></li>
  </ul>
  <p class="navbar-text pull-right">TFG - Autocorrección de prácticas en Java</p>
</nav>

<div class="jumbotron">
  <h3>Gráficas</h3>
</div>

<div class="container">
	<div class="row">
		<!--  <div class="col-md-4 col-md-offset-4"> -->
		<?php 
		echo $this->Form->create('Post');
		?>
		<div class="col-xs-12 col-md-10">
		<?php 
			echo $this->Form->input('Todas', ['type' => 'checkbox', 'value' => true, 'label' => 'Todas']);
		?>
		</div>
		<div class="col-xs-6 col-md-4">
		<?php 
			echo $this->Form->input('MediasGlobales', ['type' => 'checkbox', 'value' => true, 'label' => 'Medias Globales']);
		?>
		</div>
		<div class="col-xs-6 col-md-4">
		<?php 
			echo $this->Form->input('MediaViolacionesErrores', ['type' => 'checkbox', 'value' => true, 'label' => 'Media Violaciones-Errores']);
		?>
		</div>
		<div class="col-xs-6 col-md-4">
		<?php 
			echo $this->Form->input('MediaErrores', ['type' => 'checkbox', 'value' => true, 'label' => 'Media Errores']);
		?>
		</div>
		<div class="col-xs-6 col-md-4">
		<?php 
			echo $this->Form->input('AlumnosViolaciones', ['type' => 'checkbox', 'value' => true, 'label' => 'Violaciones']);
		?>
		</div>
		<div class="col-xs-6 col-md-4">
		<?php 
			echo $this->Form->input('AlumnosIntentos', ['type' => 'checkbox', 'value' => true, 'label' => 'Intentos realizados']);
		?>
		</div>
		<div class="col-xs-6 col-md-4">
		<?php 
			echo $this->Form->input('AlumnosTest', ['type' => 'checkbox', 'value' => true, 'label' => 'Alumnos test']);
		?>
		<?php
		//echo $this->Form->input('Todas', ['type' => 'checkbox', 'value' => true, 'label' => 'Todas']);
		//echo "<br>";
		//echo $this->Form->input('field', ['options' => $alumnos_intentos, 'type' => 'select', 'empty' => '-- Selecciona el alumno --', 'label' => 'Mostrar gráficas alumno']);
		?>
		</div>
		<div class="col-xs-12 col-md-10">
		<br>
		<?php 
			echo $this->Form->input('field', ['options' => $alumnos_intentos, 'type' => 'select', 'empty' => '-- Selecciona el alumno --', 'label' => 'Mostrar gráficas alumno']);
		?>
		</div>
		<div class="col-xs-6 col-md-4">
		<br>
		<?php
			echo $this->Form->button('Generar Gráficas', ['type' => 'submit', 'class' => 'btn btn-success']);
		?>
		</div>
		<?php echo $this->Form->end(); ?>
		<!-- </div> -->
	</div>
</div>

<?php if($_SESSION["grafica_alumnos_test"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-test.png")){?>
	  	<h4 class="page-header">Gráfica Alumnos que pasan y no pasan los test</h4>
	  	<center><img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-test.png" ?>" style="border: 1px solid gray;"/></center>
<?php }
	  if($_SESSION["grafica_medias_globales"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-medias.png")){?>
	  	<h4 class="page-header">Gráfica Medias Globales</h4>
	  	<center><img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-medias.png" ?>" style="border: 1px solid gray;"/></center>
<?php } 
	  if($_SESSION["grafica_promedio_errores_violaciones"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-promedioViolacionesErrores.png")){?>
		<h4 class="page-header">Gráfica Media Violaciones - Errores</h4>
		<center><img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-promedioViolacionesErrores.png" ?>" style="border: 1px solid gray;"/></center>
<?php }
	  if($_SESSION["grafica_media_errores"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-promedioErroresUnitariosExcepciones.png")){?>
	  	<h4 class="page-header">Gráfica Media Errores unitarios - excepciones</h4>
	  	<center><img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-promedioErroresUnitariosExcepciones.png" ?>" style="border: 1px solid gray;"/></center> 
<?php }
	  if($_SESSION["grafica_alumnos_intentos"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-intentos_pasaTest_intervalos.png")){?>
	  	<h4 class="page-header">Gráfica Intentos para pasar los test</h4>
	  	<center><img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-intentos_pasaTest_intervalos.png" ?>" style="border: 1px solid gray;"/></center>
<?php }
	  if($_SESSION["grafica_alumnos_intentos"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest_intervalos.png")){?>
	  	<h4 class="page-header">Gráfica Intentos para no pasar los test</h4>
	  	<center><img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest_intervalos.png" ?>" style="border: 1px solid gray;"/></center>
<?php }
	  if($_SESSION["grafica_alumnos_violaciones"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-violaciones_intervalos.png")){?>
	  	<h4 class="page-header">Gráfica Violaciones</h4>
	  	<center><img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-violaciones_intervalos.png" ?>" style="border: 1px solid gray;"/></center>
<?php } 
	  if($_SESSION["dropdown"]){?>
		<h3 class="page-header">Gráficas Alumno: <?= $alumnos_intentos[$id_alumno] ?></h3>
		<center>
		<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-".$id_alumno."-linea.png" ?>" style="border: 1px solid gray;"/>
		<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-".$id_alumno."-errores_unitarios.png" ?>" style="border: 1px solid gray;"/>
		<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-".$id_alumno."-violaciones.png" ?>" style="border: 1px solid gray;"/>
		
		<!-- Gráficas del último intento realizado -->
		<?php 
	 	if(file_exists("img/".$_SESSION["lti_idTarea"]."-".$id_alumno."-prioridades_violaciones_ultimoIntento_barras.png")){?>
			<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-".$id_alumno."-prioridades_violaciones_ultimoIntento_barras.png" ?>" style="border: 1px solid gray;"/>
		<?php } ?>
		<?php 
	 	if(file_exists("img/".$_SESSION["lti_idTarea"]."-".$id_alumno."-prioridades_violaciones_ultimoIntento.png")){?>
			<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-".$id_alumno."-prioridades_violaciones_ultimoIntento.png" ?>" style="border: 1px solid gray;"/>
		<?php } ?>		
		</center>
<?php } ?>
