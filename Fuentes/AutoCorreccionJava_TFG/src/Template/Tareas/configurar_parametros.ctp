<h3 class='page-header'>Configuración de los parámetros obligatorios de la práctica</h3>

<div class="container">
	<div class="row">
		<div class="col-md-6">
			<?php
			echo $this->Form->create($nueva_tarea);
			echo $this->Form->input('num_max_intentos', ['label' => 'Número máximo de intentos', 'class' => 'form-control']);
			echo $this->Form->input('paquete', ['placeholder' => 'Separado por puntos en caso de ser varios', 'class' => 'form-control']);
			echo $this->Form->input('fecha_limite', ['type' => 'date', 'class' => 'form-control']);
			//echo $this->Form->button(__('Guardar configuración'));
			?>
			<br>
			<?php echo $this->Form->button('Guardar configuración', ['type' => 'submit', 'class' => 'btn btn-success']); ?>
			<?php echo $this->Form->end(); ?>
			<br>
		</div>
	</div>
</div>