
<?= $this->Html->css('custom.css') ?>

<!------------------------------ TITULO DEL PANEL  -------------------->

<div class="jumbotron">
  <h3><?= __('Registro del profesor en la Aplicación') ?>
     <img class="mensajeInfo iconos" data-content="<?= __('Tras el registro se le proporcionarán los parámetros LTI necesarios para poder crear una tarea de Moodle que enlace con la aplicación web.') ?>" 
     	  data-placement="auto" title="<?= __('INFORMACIÓN') ?>" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/info_2.png"/>
  </h3>
</div>

<!------------------------------- FORMULARIO REGISTRO DEL PROFESOR  -------->

<div class="container">	
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<?php
			echo $this->Form->create($nuevo_profesor);
			echo $this->Form->input('nombre', ['type' => 'text', 'label' => __('Nombre'), 'class' => 'form-control']);
			echo $this->Form->input('apellidos', ['type' => 'text', 'label' => __('Apellidos'), 'class' => 'form-control']);
			echo $this->Form->input('correo', ['type' => 'email', 'label' => __('Correo electrónico'), 'class' => 'form-control', 'placeholder' => __('Tiene que ser el de Moodle')]);
			echo $this->Form->input('contraseña', ['type' => 'password', 'label' => __('Contraseña'), 'class' => 'form-control']);
			echo $this->Form->input('confirmar_contraseña', ['type' => 'password', 'label' => __('Confirmar contraseña'), 'class' => 'form-control', 'placeholder' => __('Asegúrese que sea la misma')]);
			?>
			<br>
			<?php
			echo $this->Form->button(__('Registrar'), ['type' => 'submit', 'class' => 'btn btn-success']);
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