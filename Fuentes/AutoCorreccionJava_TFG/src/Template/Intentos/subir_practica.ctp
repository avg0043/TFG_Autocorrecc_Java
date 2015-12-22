<!--
<div class="page-header">
	<h3>Subida de Prácticas</h3>
</div>
-->

<?php
if(!$test_subido){
?>
	<h4>Todavía no puede subir la práctica porque el profesor no ha subido test</h4>
<?php
}
else{
?>

<div class="jumbotron">
  <h3>Subida de Prácticas</h3>
  <p>Suba la práctica en un fichero zip que contenga la estructura de carpetas correcta correspondiente al paquete.
  	 Tras la subida se mostrarán los reportes y, si no lo estaban ya, las gráficas correspondientes.</p>
</div>

<!-- 
<div class="panel panel-primary, col-md-6">
 	<div class="panel-body">
		<ul class="list-group">
			<li class="list-group-item"><b>Nombre del paquete</b><span class="badge"><?= $paquete ?></span></li>
			<li class="list-group-item"><b>Número máximo de intentos posibles</b><span class="badge"><?= $num_maximo_intentos ?></span></li>
			<li class="list-group-item"><b>Fecha límite de entrega</b><span class="badge"><?= $fecha_limite ?></span></li>
			<li class="list-group-item"><b>Número de intentos realizados</b><span class="badge"><?= $num_intentos_realizados ?></span></li>
			<li class="list-group-item"><b>Número de intentos restantes</b><span class="badge"><?= ($num_maximo_intentos - $num_intentos_realizados) ?></span></li>
		</ul>
	</div>
</div>
 -->

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
		<div class="col-md-6">
	 		<div class="panel-body">
				<ul class="list-group">
					<li class="list-group-item"><b>Nombre del paquete</b><span class="badge"><?= $paquete ?></span></li>
					<li class="list-group-item"><b>Número máximo de intentos posibles</b><span class="badge"><?= $num_maximo_intentos ?></span></li>
					<li class="list-group-item"><b>Fecha límite de entrega</b><span class="badge"><?= $fecha_limite ?></span></li>
					<li class="list-group-item"><b>Número de intentos realizados</b><span class="badge"><?= $num_intentos_realizados ?></span></li>
					<li class="list-group-item"><b>Número de intentos restantes</b><span class="badge"><?= ($num_maximo_intentos - $num_intentos_realizados) ?></span></li>
				</ul>
			</div>
		</div>
	</div>
</div>

<?php if($intento != null){
		$ruta = "http://localhost/".$_SESSION['lti_idCurso']."/".$_SESSION['lti_idTarea']."/".$_SESSION['lti_rol'].
				"/".$_SESSION['lti_userId']."/".$intento."/site/"; ?>
		
		<h4 class="page-header">Reportes Generados</h4>
		<a href=<?= $ruta."javancss.html" ?> class="btn btn-default btn-lg" role="button">JAVANCSS</a>
		<a href=<?= $ruta."jdepend-report.html" ?> class="btn btn-default btn-lg" role="button">JDEPEND</a>
		<?php if($_SESSION["pmd_generado"]){ ?>
				<a href=<?= $ruta."pmd.html" ?> class="btn btn-default btn-lg" role="button">PMD</a>
		<?php } ?>
		<?php if($_SESSION["findbugs_generado"]){ ?>
				<a href=<?= $ruta."findbugs.html" ?> class="btn btn-default btn-lg" role="button">FINDBUGS</a>
		<?php } 
			  if($_SESSION["errores_unitarios"]){ ?>
			  	<a href=<?= $ruta."surefire-report.html" ?> class="btn btn-default btn-lg" role="button">ERRORES UNITARIOS</a>
		<?php } 
		}?>
		

<?php if(file_exists("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-linea.png")){ ?>
	  	<h4 class="page-header">Gráfica Errores Unitarios - Violaciones de código</h4>
	  	<?php $nombre_grafica = $_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-linea.png"; ?>
		<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $nombre_grafica ?>" style="border: 1px solid gray;"/>
<?php }
	  if(file_exists("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-errores_unitarios.png")){ ?>
	  	<h4 class="page-header">Gráfica Errores Unitarios</h4>
	  	<?php $nombre_grafica = $_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-errores_unitarios.png"; ?>
		<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $nombre_grafica ?>" style="border: 1px solid gray;"/>
<?php }
	  if(file_exists("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-violaciones.png")){?>
		<h4 class="page-header">Gráfica Violaciones</h4>
		<?php $nombre_grafica = $_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-violaciones.png"; ?>
		<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $nombre_grafica ?>" style="border: 1px solid gray;"/>
<?php }
	  if(file_exists("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones.png")){?>
		<h4 class="page-header">Gráfica Prioridades de las Violaciones</h4>
		<?php $nombre_grafica = $_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones.png"; ?>
		<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $nombre_grafica ?>" style="border: 1px solid gray;"/>
<?php }

}
?>



