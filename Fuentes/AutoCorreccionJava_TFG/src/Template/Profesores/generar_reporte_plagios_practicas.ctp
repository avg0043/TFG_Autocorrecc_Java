<nav class="navbar navbar-inverse">
  <ul class="nav navbar-nav">
  	<li><a href="http://localhost/AutoCorreccionJava_TFG/profesores/mostrar-panel">Inicio</a></li>
  </ul>
  <p class="navbar-text pull-right">TFG - Autocorrección de prácticas en Java</p>
</nav>

<div class="jumbotron">
  <h3>Plagios</h3>
  <p>Selecciona el botón <i>Reporte Plagios</i> para comprobar los posibles plagios existentes u observe la lista de razones
  	 posibles por las que no ha podido generarse el reporte.</p>
</div>

<div class="col-md-4 col-md-offset-4">
<?php
if($reporte_generado){
?>
	<ul>
		<li>La comprobación de plagios se ha realizado entre <?= $numero_practicas_subidas ?> prácticas.</li>
		<li>Las prácticas pertenecen a los siguientes Alumnos:
			<ul>
			<?php foreach($alumnos_con_practicas as $alumno):?>
			<li><?= $alumno ?></li>
			<?php endforeach;?>
			</ul>
		</li>
	</ul>
	<a href="../../plagios/reporte/index.html" class="btn btn-default btn-lg" role="button" target="_blank">Reporte Plagios</a>
<?php
}else{
?>
	<h5>Posibles razones:</h5>
	<ol>
		<li>No hay alumnos registrados.</li>
		<li>Los alumnos no han subido ninguna práctica.</li>
		<li>Solo hay un alumno que haya subido su práctica.</li>	
	</ol>
<?php 
}
?>

</div>