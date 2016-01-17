<!--  
<nav class="navbar navbar-inverse">
  <ul class="nav navbar-nav">
  	<li><a href="http://localhost/AutoCorreccionJava_TFG/profesores/mostrar-panel">Inicio</a></li>
  </ul>
  <p class="navbar-text pull-right">TFG - Autocorrección de prácticas en Java</p>
</nav>
-->

<?= $this->Html->css('custom.css') ?>


<div class="jumbotron">
  <h3>Subida de Tests</h3>
  <p>Se deberá de subir un fichero .zip que contenga el test de extensión .java. Es importante que el test
     pertenezcan al paquete establecido previamente en la configuración de parámetros de la tarea.</p>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<?php 
			echo $this->Form->create('Post', ['type' => 'file']);
			echo $this->Form->input('enunciado', ['type' => 'textarea', 'label' => 'Enunciado de la práctica', 'rows' => '5', 'cols' => '5', 'class' => 'form-control']);
			echo $this->Form->input('ficheroAsubir', ['type' => 'file', 'label' => 'Fichero a subir:', 'class' => 'form-control']);
			//echo $this->Form->button(__('Subir'));
			?>
			<br>
			<?php 
			echo $this->Form->button('Subir', ['type' => 'submit', 'class' => 'btn btn-success']);
			echo " ";
			echo $this->Form->button('Resetear campos', ['type' => 'reset', 'class' => 'btn btn-danger']);
			echo $this->Form->end(); 
			?>
			<br>
		</div>
	</div>
</div>

