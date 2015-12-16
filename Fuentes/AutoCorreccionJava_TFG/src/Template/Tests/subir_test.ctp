<!-- 
<div class="page-header">
	<h3>Subida de Tests</h3>
</div>
 -->

<div class="jumbotron">
  <h3>Subida de Tests</h3>
  <p>Se deberá de subir un único fichero .zip que contenga los ficheros test con extensión .java. Es importante que los test
     pertenezcan al paquete establecido previamente en la configuración de parámetros.</p>
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

