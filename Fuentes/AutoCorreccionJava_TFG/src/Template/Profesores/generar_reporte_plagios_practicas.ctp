
 
<?= $this->Html->css('custom.css') ?>
 
<!----------------------- TITULO DEL PANEL ----------------------->

<div class="jumbotron">
  <h3><?= __('Comprobación de Plagios de prácticas') ?></h3>
</div>

<!------------------------ FORMULARIO PARA LA COMPROBACION DE PLAGIOS  ----------->

<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
		<?php 
		echo $this->Form->create('Post');
		?>
		<div style="width:400px;height:150px;overflow-y: scroll;">
		<?php
		echo $this->Form->input("TodosConTodos", ['type' => 'checkbox', 'value' => true, 'label' => __('TODOS CON TODOS')]);
		$clave = key($alumnos);
		foreach ($alumnos as $alumno):
			echo $this->Form->input($clave, ['type' => 'checkbox', 'value' => true, 'label' => $alumno]);
			$clave = key($alumnos);
			next($alumnos);
		endforeach;
		?>
		</div>
		<?php
		echo "<br>";
		echo $this->Form->button(__('Comprobar Plagios'), ['type' => 'submit', 'class' => 'btn btn-success']);
		echo $this->Form->end(); 
		?>
		</div>
	</div>
</div>


<?php
if($reporte_generado){
?>
	<h4 class="page-header"><?= __('Reporte de plagios') ?></h4>
	<div class="container">
		<div class="row">
			<div class="jumbotron col-md-8 col-md-offset-2">
				<h6><?= __('La comprobación de plagios se ha realizado entre') ?> <?= $numero_practicas_subidas ?> <?= __('prácticas.') ?></h6>
				<a href="../../plagios/reporte/index.html" class="btn btn-default btn-lg" role="button" target="_blank"><?= __('Ver Reporte') ?></a>
			</div>
		</div>
	</div>
<?php
}
?>



