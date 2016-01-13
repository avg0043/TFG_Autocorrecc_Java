<nav class="navbar navbar-inverse">
  <ul class="nav navbar-nav">
  	<li><a href="http://localhost/AutoCorreccionJava_TFG/profesores/mostrar-panel">Inicio</a></li>
  </ul>
  <p class="navbar-text pull-right">TFG - Autocorrección de prácticas en Java</p>
</nav>

<div class="jumbotron">
  <h3>Subida de Tests</h3>
  <p>Se deberá de subir un fichero .zip que contenga el test de extensión .java. Es importante que el test
     pertenezcan al paquete establecido previamente en la configuración de parámetros de la tarea.</p>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-6 col-md-offset-3">
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

