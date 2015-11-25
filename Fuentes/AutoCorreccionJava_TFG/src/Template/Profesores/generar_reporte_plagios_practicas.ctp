<div class="page-header">
	<h3>Plagios</h3>
</div>

<div>
	<?= $this->Html->link('Panel profesor', ['action' => 'mostrarPanel']) ?>
</div>

<?php
if($reporte_generado){
?>
	<ul>
		<li>La comprobación de plagios se ha realizado entre <?= $numero_practicas_subidas ?> prácticas</li>
		<li>Las prácticas pertenecen a los siguientes Alumnos:
			<ul>
			<?php foreach($alumnos_con_practicas as $alumno):?>
			<li><?= $alumno ?></li>
			<?php endforeach;?>
			</ul>
		</li>
	</ul>
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