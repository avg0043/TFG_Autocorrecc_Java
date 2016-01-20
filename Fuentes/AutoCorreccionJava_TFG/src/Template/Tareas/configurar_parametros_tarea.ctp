
<?= $this->Html->css('custom.css') ?>


<div class="jumbotron">
  <h3><?= __('Configuración de los parámetros de la tarea') ?>
     <img class="mensajeInfo iconos" data-content="<?= __("El 'número máximo de intentos' debe de estar comprendido entre 1 y 20. El 'paquete' únicamente podrá ser configurado la primera vez que el profesor accede a la aplicación web.") ?>" 
     	  data-placement="auto" title="<?= __('INFORMACIÓN') ?>" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/info_2.png"/>
  </h3>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<?php
			echo $this->Form->create($nueva_tarea);
			echo $this->Form->input('num_max_intentos', ['label' => __('Número máximo de intentos'), 'class' => 'form-control']);
			if(empty($tarea_actual))
				echo $this->Form->input('paquete', ['label' => __('Paquete'), 'placeholder' => __('Separado por puntos en caso de ser varios'), 'class' => 'form-control']);
			else
				echo $this->Form->input('paquete', ['label' => __('Paquete'), 'placeholder' => $tarea_actual[0]->paquete, 'class' => 'form-control', 'disabled' => 'disabled']);
			echo $this->Form->input('fecha_limite', ['label' => __('Fecha límite'), 'type' => 'date', 'class' => 'form-control']);
			?>
			<br>
			<?php 
			echo $this->Form->button(__('Guardar'), ['type' => 'submit', 'class' => 'btn btn-success']);
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
