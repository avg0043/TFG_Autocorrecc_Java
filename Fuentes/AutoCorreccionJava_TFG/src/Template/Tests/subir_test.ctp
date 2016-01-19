
<?= $this->Html->css('custom.css') ?>


<div class="jumbotron">
  <h3><?= __('Subida de Test') ?>
     <img class="mensajeInfo iconos" data-content="<?= __('Se deberá de subir un fichero .zip que contenga el test de extensión .java. Es importante que el test
     pertenezca al paquete establecido en la configuración de parámetros de la tarea. Se podrá actualizar el enunciado aunque no se suban test.') ?>" 
     data-placement="auto" title="<?= __('INFORMACIÓN') ?>" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/info_2.png"/>
  </h3>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<?php 
			echo $this->Form->create('Post', ['type' => 'file']);
			echo $this->Form->input('enunciado', ['type' => 'textarea', 'label' => __('Enunciado de la práctica'), 'rows' => '5', 'cols' => '5', 'class' => 'form-control']);
			echo $this->Form->input('ficheroAsubir', ['type' => 'file', 'label' => __('Fichero a subir:'), 'class' => 'form-control']);
			?>
			<br>
			<?php 
			echo $this->Form->button(__('Subir'), ['type' => 'submit', 'class' => 'btn btn-success']);
			echo " ";
			echo $this->Form->button(__('Resetear campos'), ['type' => 'reset', 'class' => 'btn btn-danger']);
			echo $this->Form->end(); 
			?>
			<br>
		</div>
	</div>
</div>

<script type="text/javascript">

	$('.mensajeInfo').popover({ trigger: "hover" });
	$('.mensajeInfo').popover();
	
</script>

