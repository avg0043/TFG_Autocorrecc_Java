<h3>Alumnos registrados</h3>

<?= $this->Html->link('Panel profesor', ['controller' => 'Profesores', 'action' => 'mostrarPanel']) ?>

<?php
if(!$alumnos->isEmpty()){
?>

<table>
	<tr>
		<th>Id de Moodle</th>
		<th>Nombre completo</th>
		<th>Correo electrónico</th>
	</tr>
	
	<?php foreach ($alumnos as $alumno): ?>
	<tr>
		<td><?= $alumno->id ?></td>
		<td><?= $alumno->nombre_completo ?></td>
		<td><?= $alumno->correo ?></td>
	</tr>
	<?php endforeach; ?>
	
</table>

<?php
}else{
?>
	<h4>No hay alumnos registrados</h4>
<?php
}
?>


