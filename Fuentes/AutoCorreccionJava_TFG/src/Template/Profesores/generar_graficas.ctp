<!--  
<div class="page-header">
	<h3>Gráficas</h3>
</div>
-->

<!-- 
<nav class="navbar navbar-inverse">
  <ul class="nav navbar-nav">
  	<li><a href="http://localhost/AutoCorreccionJava_TFG/profesores/mostrar-panel">Inicio</a></li>
  </ul>
  <p class="navbar-text pull-right">TFG - Autocorrección de prácticas en Java</p>
</nav>
 -->
 
<?= $this->Html->css('custom.css') ?>
 

<div class="jumbotron">
  <h3>Gráficas</h3>
</div>

<div class="jumbotron">
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

<?php if($_SESSION["grafica_alumnos_test"] || $_SESSION["grafica_medias_globales"] || $_SESSION["grafica_promedio_errores_violaciones"]
		 || $_SESSION["grafica_media_errores"] || $_SESSION["grafica_alumnos_intentos"] || $_SESSION["grafica_alumnos_violaciones"]
		 || $_SESSION["dropdown"]){?>
		 
		<h4 class="page-header">Gráficas seleccionadas</h4>
		<ul class="nav nav-tabs">
			<?php if($_SESSION["grafica_medias_globales"]){?>
		    	<li><a data-toggle="tab" href="#mediasGlobales">Medias Globales</a></li>
		    <?php }?>
		    <?php if($_SESSION["grafica_promedio_errores_violaciones"]){?>
		   		<li><a data-toggle="tab" href="#mediaViolacionesErrores">Media Violaciones-Errores</a></li>
		    <?php }?>
		    <?php if($_SESSION["grafica_media_errores"]){?>
		    	<li><a data-toggle="tab" href="#mediaErrores">Media Errores</a></li>
		    <?php }?>
		    <?php if($_SESSION["grafica_alumnos_violaciones"]){?>
		    	<li><a data-toggle="tab" href="#violaciones">Violaciones</a></li>
		    <?php }?>
		    <?php if($_SESSION["grafica_alumnos_intentos"]){?>
	    		<li><a data-toggle="tab" href="#intentos">Intentos realizados</a></li>
	    	<?php }?>
	    	<?php if($_SESSION["grafica_alumnos_test"]){?>
	    		<li><a data-toggle="tab" href="#test">Alumnos test</a></li>
	    	<?php }?>
	    	<?php if($_SESSION["dropdown"]){?>
	    		<li><a data-toggle="tab" href="#alumno">Alumno: <?= $alumnos_intentos[$id_alumno] ?></a></li>
	    	<?php }?>
		</ul>
		
		<div class="tab-content">
		    <div id="mediasGlobales" class="divGraficas tab-pane fade">
		    	<?php if($_SESSION["grafica_medias_globales"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-medias.png")){?>
				<img class="imgGraficas" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-medias.png" ?>"/>
				<?php }?>
			</div>
		    <div id="mediaViolacionesErrores" class="divGraficas tab-pane fade">
		    	<?php if($_SESSION["grafica_promedio_errores_violaciones"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-promedioViolacionesErrores.png")){?>
				<img class="imgGraficas" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-promedioViolacionesErrores.png" ?>"/>
		    	<?php }?>
		    </div>
		    <div id="mediaErrores" class="divGraficas tab-pane fade">
		    	<?php if($_SESSION["grafica_media_errores"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-promedioErroresUnitariosExcepciones.png")){?>
				<img class="imgGraficas" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-promedioErroresUnitariosExcepciones.png" ?>"/>
		 		<?php }?>
		    </div>
		    <div id="violaciones" class="divGraficas tab-pane fade">
		    	<?php if($_SESSION["grafica_alumnos_violaciones"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-violaciones_intervalos.png")){?>
				<img class="imgGraficas" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-violaciones_intervalos.png" ?>"/>
		 		<?php }?>
		    </div>
		    <div id="intentos" class="divGraficas tab-pane fade">
		    	<?php if($_SESSION["grafica_alumnos_intentos"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-intentos_pasaTest_intervalos.png")){?>
				<img class="imgGraficas" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-intentos_pasaTest_intervalos.png" ?>"/>
		 		<?php }?>
		 		<?php if($_SESSION["grafica_alumnos_intentos"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest_intervalos.png")){?>
				<img class="imgGraficas" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest_intervalos.png" ?>"/>
		 		<?php }?>
		    </div>
		    <div id="test" class="divGraficas tab-pane fade">
		    	<?php if($_SESSION["grafica_alumnos_test"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-test.png")){?>
				<img class="imgGraficas" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-test.png" ?>"/>
		 		<?php }?>
		    </div>
		    <div id="alumno" class="divGraficas tab-pane fade">
				<img class="imgGraficas" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-".$id_alumno."-linea.png" ?>"/>
				<img class="imgGraficas" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-".$id_alumno."-errores_unitarios.png" ?>"/>
				<img class="imgGraficas" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-".$id_alumno."-violaciones.png" ?>"/>
				
				<!-- Gráficas del último intento realizado -->
				<?php 
			 	if(file_exists("img/".$_SESSION["lti_idTarea"]."-".$id_alumno."-prioridades_violaciones_ultimoIntento_barras.png")){?>
					<img class="imgGraficas" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-".$id_alumno."-prioridades_violaciones_ultimoIntento_barras.png" ?>"/>
				<?php } ?>
				<?php 
			 	if(file_exists("img/".$_SESSION["lti_idTarea"]."-".$id_alumno."-prioridades_violaciones_ultimoIntento.png")){?>
					<img class="imgGraficas" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-".$id_alumno."-prioridades_violaciones_ultimoIntento.png" ?>"/>
				<?php } ?>	
		    </div>
		</div>
<?php }?>


