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

<div class="container">
	<div class="row">
		<div class="col-md-6">
			<?php 
			echo $this->Form->create('Post');
			echo $this->Form->input('MediasGlobales', ['type' => 'checkbox', 'value' => true, 'label' => 'Medias Globales', 'class' => 'form-control']);
			echo "<br>";
			echo $this->Form->input('MediaViolacionesErrores', ['type' => 'checkbox', 'value' => true, 'label' => 'Media Violaciones-Errores', 'class' => 'form-control']);
			echo "<br>";
			echo $this->Form->input('AlumnosViolaciones', ['type' => 'checkbox', 'value' => true, 'label' => 'Violaciones', 'class' => 'form-control']);
			echo "<br>";
			echo $this->Form->input('AlumnosIntentos', ['type' => 'checkbox', 'value' => true, 'label' => 'Intentos realizados', 'class' => 'form-control']);
			echo "<br>";
			echo $this->Form->input('AlumnosTest', ['type' => 'checkbox', 'value' => true, 'label' => 'Alumnos test', 'class' => 'form-control']);
			echo "<br>";
			echo $this->Form->input('Todas', ['type' => 'checkbox', 'value' => true, 'label' => 'Todas', 'class' => 'form-control']);
			echo "<br>";
			//$valores = array();
			//$valores["3"] = "alvaro";
			//$valores["5"] = "juan";
			echo $this->Form->input('field', ['options' => $alumnos_intentos, 'type' => 'select', 'empty' => '-- Selecciona el alumno --', 'label' => 'Mostrar gráficas alumno']);
			//echo $this->Form->select('field', $alumnos_intentos, ['empty' => 'Lista de alumnos'], ['class' => 'form-control']);
			echo "<br>";
			//echo $this->Form->input('dropdown', ['type' => 'select', 'options'=> [2,3] , 'label'=> 'epa', 'empty'=>'Category', 'class' => 'form-control']);
			//echo "<br>";
			?>
			<br>
			<?php echo $this->Form->button('Generar Gráficas', ['type' => 'submit', 'class' => 'btn btn-success']); ?>
			<?php echo $this->Form->end(); ?>
			<br>
		</div>
	</div>
</div>

<?php if($_SESSION["grafica_alumnos_test"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-test.png")){?>
	  	<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-test.png" ?>" style="border: 1px solid gray;"/>
<?php }
	  if($_SESSION["grafica_medias_globales"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-medias.png")){?>
	  	<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-medias.png" ?>" style="border: 1px solid gray;"/>
<?php } 
	  if($_SESSION["grafica_promedio_errores_violaciones"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-promedioViolacionesErrores.png")){?>
		<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-promedioViolacionesErrores.png" ?>" style="border: 1px solid gray;"/>
<?php } 
	  if($_SESSION["grafica_alumnos_intentos"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-intentos_pasaTest_intervalos.png")){?>
	  	<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-intentos_pasaTest_intervalos.png" ?>" style="border: 1px solid gray;"/>
<?php }
	  if($_SESSION["grafica_alumnos_intentos"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest_intervalos.png")){?>
	  	<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-intentos_noPasaTest_intervalos.png" ?>" style="border: 1px solid gray;"/>
<?php }
	  if($_SESSION["grafica_alumnos_violaciones"] && file_exists("img/".$_SESSION["lti_idTarea"]."-prof-violaciones_intervalos.png")){?>
	  	<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-violaciones_intervalos.png" ?>" style="border: 1px solid gray;"/>
<?php } 
	  if($_SESSION["dropdown"]){?>
		<h3 class="page-header">Gráficas Alumno: <?= $alumnos_intentos[$id_alumno] ?></h3>
		<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-".$id_alumno."-linea.png" ?>" style="border: 1px solid gray;"/>
		<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-".$id_alumno."-errores_unitarios.png" ?>" style="border: 1px solid gray;"/>
		<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-".$id_alumno."-violaciones.png" ?>" style="border: 1px solid gray;"/>
		<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-".$id_alumno."-prioridades_violaciones.png" ?>" style="border: 1px solid gray;"/>
<?php } ?>
