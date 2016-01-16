<nav class="navbar navbar-inverse">
  <ul class="nav navbar-nav">
  	<li><a href="http://localhost/AutoCorreccionJava_TFG/profesores/mostrar-panel">Inicio</a></li>
  </ul>
  <p class="navbar-text pull-right">TFG - Autocorrección de prácticas en Java</p>
</nav>
 
<div class="jumbotron">
  <h3>Configuración de los parámetros de la tarea</h3>
  <p>El <i>número máximo de intentos</i> debe de estar comprendido entre 1 y 20. <br>
     El <i>paquete</i> únicamente podrá ser configurado la primera vez que se accede a la aplicación web.</p>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<?php
			echo $this->Form->create($nueva_tarea);
			echo $this->Form->input('num_max_intentos', ['label' => 'Número máximo de intentos', 'class' => 'form-control']);
			if(empty($tarea_actual))
				echo $this->Form->input('paquete', ['placeholder' => 'Separado por puntos en caso de ser varios', 'class' => 'form-control']);
			else
				echo $this->Form->input('paquete', ['placeholder' => $tarea_actual[0]->paquete, 'class' => 'form-control', 'disabled' => 'disabled']);
			echo $this->Form->input('fecha_limite', ['type' => 'date', 'class' => 'form-control']);
			//echo $this->Form->button(__('Guardar configuración'));
			?>
			<br>
			<?php 
			echo $this->Form->button('Guardar', ['type' => 'submit', 'class' => 'btn btn-success']);
			echo " ";
			echo $this->Form->button('Resetear campos', ['type' => 'reset', 'class' => 'btn btn-danger']);
			echo $this->Form->end(); 
			?>
			<br>
		</div>
	</div>
</div>