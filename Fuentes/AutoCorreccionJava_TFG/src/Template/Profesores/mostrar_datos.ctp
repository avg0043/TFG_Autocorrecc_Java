<h1>Datos</h1>

<?= $this->Html->link('Panel profesor', ['action' => 'mostrarPanel']) ?>

<?php foreach ($profesor as $datos): ?>
	<ul>
		<li><b>ID de Moodle: </b><?= $datos->id_moodle ?></li>
		<li><b>Nombre completo: </b><?= $datos->nombre_completo ?></li>
		<li><b>Correo: </b><?= $datos->correo ?></li>
	</ul>
<?php endforeach; ?>