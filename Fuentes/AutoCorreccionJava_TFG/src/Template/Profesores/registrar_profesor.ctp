<nav class="navbar navbar-inverse">
  <p class="navbar-text pull-right">TFG - Autocorrección de prácticas en Java</p>
</nav>

<div class="jumbotron">
  <h1>Bienvenido!</h1>
  <p>Rellene el siguiente formulario para registrarse en el servicio web y así poder obtener los
  	 parámetros LTI necesarios para crear la tarea de tipo "herramienta externa" desde Moodle.</p>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<?php
			echo $this->Form->create($nuevo_profesor);
			echo $this->Form->input('nombre', ['type' => 'text', 'label' => 'Nombre', 'class' => 'form-control']);
			echo $this->Form->input('apellidos', ['type' => 'text', 'label' => 'Apellidos', 'class' => 'form-control']);
			echo $this->Form->input('correo', ['type' => 'email', 'label' => 'Correo electrónico', 'class' => 'form-control', 'placeholder' => 'Tiene que ser el de Moodle']);
			echo $this->Form->input('contraseña', ['type' => 'password', 'label' => 'Contraseña', 'class' => 'form-control']);
			echo $this->Form->input('confirmar_contraseña', ['type' => 'password', 'label' => 'Confirmar contraseña', 'class' => 'form-control', 'placeholder' => 'Asegúrese que sea la misma']);
			?>
			<br>
			<?php echo $this->Form->button('Registrar', ['type' => 'submit', 'class' => 'btn btn-success']); ?>
			<?php echo $this->Form->end(); ?>
			<br>
		</div>
	</div>
</div>

<?php
/*
echo $this->Form->create($nuevo_profesor, ['class' => 'form-horizontal col-md-6']);
echo $this->Form->input('nombre_completo', ['type' => 'text', 'label' => 'Nombre y apellido', 'class' => 'form-control']);
echo $this->Form->input('correo', ['type' => 'email', 'label' => 'Correo electrónico', 'class' => 'form-control', 'placeholder' => 'Tiene que ser el de Moodle']);
echo $this->Form->input('contraseña', ['type' => 'password', 'label' => 'Contraseña', 'class' => 'form-control']);
echo $this->Form->input('confirmar_contraseña', ['type' => 'password', 'label' => 'Confirmar contraseña', 'class' => 'form-control', 'placeholder' => 'Asegúrese que sea la misma']);
//echo $this->Form->button(__('Registrar', ['class' => 'btn btn-primary btn-xs']));
echo $this->Form->button('Registrar', ['type' => 'submit', 'class' => 'btn btn-success']);
echo $this->Form->end();
*/
?>