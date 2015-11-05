<h1>Alumnos registrados</h1>

<?= $this->Html->link('Panel profesor', ['controller' => 'Profesores', 'action' => 'mostrarPanel']) ?>

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

