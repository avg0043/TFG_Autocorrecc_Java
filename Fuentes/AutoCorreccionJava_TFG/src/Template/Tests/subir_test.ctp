<div class="page-header">
	<h3>Subida de Tests</h3>
</div>

<?php
echo $this->Html->link('Panel profesor', ['controller' => 'Profesores', 'action' => 'mostrarPanel']);
?>

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

