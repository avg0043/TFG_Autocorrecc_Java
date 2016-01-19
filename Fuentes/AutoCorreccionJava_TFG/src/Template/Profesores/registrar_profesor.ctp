
<?= $this->Html->css('custom.css') ?>

<div class="jumbotron">
  <h3>Registro del profesor en la Aplicación
     <img class="mensajeInfo iconos" data-content="Tras rellenar el formulario se mostrarán los parámetros LTI
     necesarios para poder crear una tarea de Moodle que enlace con la aplicación web." data-placement="auto" title="INFORMACIÓN" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/info_2.png"/>
  </h3>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<?php
			echo $this->Form->create($nuevo_profesor);
			echo $this->Form->input('nombre', ['type' => 'text', 'label' => 'Nombre', 'class' => 'form-control']);
			echo $this->Form->input('apellidos', ['type' => 'text', 'label' => 'Apellidos', 'class' => 'form-control']);
			echo $this->Form->input('correo', ['type' => 'email', 'label' => 'Correo electrónico', 'class' => 'form-control', 'placeholder' => 'Tiene que ser el de Moodle']);
			echo $this->Form->input('contraseña', ['type' => 'password', 'label' => 'Contraseña', 'class' => 'form-control']);
			echo $this->Form->input('confirmar_contraseña', ['type' => 'password', 'label' => 'Confirmar contraseña', 'class' => 'form-control', 'placeholder' => 'Asegúrese que sea la misma']);
			?>
			<br>
			<?php
			echo $this->Form->button('Registrar', ['type' => 'submit', 'class' => 'btn btn-success']);
			echo " ";
			echo $this->Form->button('Resetear campos', ['type' => 'reset', 'class' => 'btn btn-danger']);
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