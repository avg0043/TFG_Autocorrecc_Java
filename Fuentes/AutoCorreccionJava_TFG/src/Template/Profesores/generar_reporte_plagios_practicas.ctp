
<?= $this->Html->css('custom.css') ?>

<div class="jumbotron">
  <h3>Comprobación de plagios de las prácticas
      <img class="mensajeInfo iconos" data-content="La comprobación de plagios es realizada entre la última práctica subida de todos los alumnos." 
      data-placement="auto" title="INFORMACIÓN" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/info_2.png"/>
  </h3>
</div>

<div class="jumbotron col-md-6 col-md-offset-3">
<?php
if($reporte_generado){
?>
	<h5>La comprobación de plagios se ha realizado entre <?= $numero_practicas_subidas ?> prácticas.</h5>
	<h5>Alumnos que han subido prácticas:</h5>
	<div style="width:400px;height:150px;overflow-y: scroll;">
		<ul>
			<?php foreach($alumnos_con_practicas as $alumno):?>
			<li><?= $alumno ?></li>
			<?php endforeach;?>
		</ul>
	</div>
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


<script type="text/javascript">

	$('.mensajeInfo').popover({ trigger: "hover" });
	$('.mensajeInfo').popover();
	
</script>