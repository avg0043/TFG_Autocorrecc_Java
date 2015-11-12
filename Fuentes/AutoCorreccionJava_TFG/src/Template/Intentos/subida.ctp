<div class="page-header">
	<h3>Subida de Prácticas</h3>
</div>

<div>
	<ul>
		<li><b>Número máximo de intentos posibles: </b><?= $num_maximo_intentos ?></li>
		<li><b>Fecha límite de entrega: </b><?= $fecha_limite ?></li>
		<li><b>Número de intentos realizados: </b><?= $num_intentos_realizados ?></li>
		<li><b>Número de intentos restantes: </b><?= ($num_maximo_intentos - $num_intentos_realizados) ?></li>
	</ul>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-6">
			<?php 
			echo $this->Form->create('Post', ['type' => 'file']);
			echo $this->Form->input('ficheroAsubir', ['type' => 'file', 'label' => 'Fichero a subir:', 'class' => 'form-control']);
			//echo $this->Form->button(__('Subir'));
			?>
			<br>
			<?php echo $this->Form->button('Subir', ['type' => 'submit', 'class' => 'btn btn-success']); ?>
			<?php echo $this->Form->end(); ?>
			<br>
		</div>
	</div>
</div>

