<div class="page-header">
	<h3>Subida de Prácticas</h3>
</div>

<?php
if(!$test_subido){
?>
	<h4>Todavía no puede subir la práctica porque el profesor no ha subido test</h4>
<?php
}
else{
?>

<div>
	<ul>
		<li><b>Nombre del paquete: </b><?= $paquete ?></li>
		<li><b>Número máximo de intentos posibles: </b><?= $num_maximo_intentos ?></li>
		<li><b>Fecha límite de entrega: </b><?= $fecha_limite ?></li>
		<li><b>Número de intentos realizados: </b><?= $num_intentos_realizados ?></li>
		<li><b>Número de intentos restantes: </b><?= ($num_maximo_intentos - $num_intentos_realizados) ?></li>
	</ul>
</div>

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

<?php
if($intento != null){
	$ruta = "http://localhost/".$_SESSION['lti_idCurso']."/".$_SESSION['lti_idTarea']."/".$_SESSION['lti_rol'].
			"/".$_SESSION['lti_userId']."/".$intento."/site/";
	//echo $this->Html->link('Reporte PMD', $ruta);
?>
	<h4 class="page-header">Reportes Generados</h4>
	<a href=<?= $ruta."/javancss.html" ?> class="btn btn-default btn-lg" role="button">JAVANCSS</a>
	<a href=<?= $ruta."/jdepend-report.html" ?> class="btn btn-default btn-lg" role="button">JDEPEND</a>
	<?php if($_SESSION["pmd_generado"]){ ?>
			<a href=<?= $ruta."/pmd.html" ?> class="btn btn-default btn-lg" role="button">PMD</a>
	<?php } ?>
	<?php if($_SESSION["findbugs_generado"]){ ?>
			<a href=<?= $ruta."/findbugs.html" ?> class="btn btn-default btn-lg" role="button">FINDBUGS</a>
	<?php } 
		  if($_SESSION["errores_unitarios"]){ ?>
		  	<a href=<?= $ruta."/surefire-report.html" ?> class="btn btn-default btn-lg" role="button">ERRORES UNITARIOS</a>
	<?php }
}
}
?>

