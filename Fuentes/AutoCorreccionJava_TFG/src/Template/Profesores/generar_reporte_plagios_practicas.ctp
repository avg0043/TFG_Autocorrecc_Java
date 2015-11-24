<div class="page-header">
	<h3>Plagios</h3>
</div>

<div>
	<?= $this->Html->link('Panel profesor', ['action' => 'mostrarPanel']) ?>
</div>

<?php
if($reporte_generado){
?>
	<a href="../../plagios/reporte/index.html" class="btn btn-default btn-lg" role="button">Reporte Plagios</a>
<?php
}else{
?>
	<h5>Posibles razones:</h5>
	<ol>
		<li>No hay alumnos registrados</li>
		<li>Los alumnos no han subido ninguna práctica</li>
		<li>Solo hay un alumno que haya subido su práctica</li>	
	</ol>
<?php 
}
?>