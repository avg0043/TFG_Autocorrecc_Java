<div class="page-header">
	<h3>Datos del profesor</h3>
</div>

<?= $this->Html->link('Panel profesor', ['action' => 'mostrarPanel']) ?>

<?php foreach ($profesor as $datos): ?>
	<ul>
		<li><b>Nombre completo: </b><?= $datos->nombre_completo ?></li>
		<li><b>Correo: </b><?= $datos->correo ?></li>
	</ul>
<?php endforeach; ?>