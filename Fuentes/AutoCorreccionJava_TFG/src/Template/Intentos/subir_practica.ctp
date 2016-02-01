
<?= $this->Html->css('custom.css') ?>
<?= $this->Html->script('waitingDialog') ?> <!-- new -->


<?php
if(!$test_subido){
?>
	<h4><?= __('Todavía no puede subir la práctica porque el profesor no ha subido test') ?></h4>
<?php
}
else{
?>

<!---------------------- TÍTULO DEL PANEL DEL ALUMNO ------------------------------>

<div class="jumbotron">
  <h3><?= __('SUBIDA DE PRÁCTICAS') ?>   
  	<img class="mensajeInfo iconos" data-content="<?= __("Sube la práctica en un fichero .zip que contenga la estructura de carpetas correcta correspondiente al paquete. Es decir: si el paquete es 'uni.ubu', la estructura de carpetas a subir sería: uni/ubu/nombrePractica.java. Tras la subida se mostrarán los reportes y, si no lo estaban ya, las gráficas correspondientes.") ?>" 
  		 data-placement="auto" title="<?= __('INFORMACIÓN') ?>" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/info_2.png"/>
  	
  	<!-- Verificar si existe Enunciado o no -->
  	<?php if($enunciado != null){ ?>	
  	<img class="mensajeInfo" data-content="<?= $enunciado ?>" data-placement="auto" title="<?= __('ENUNCIADO DE LA PRÁCTICA') ?>" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/enunciado_2.png"/>
  	<?php }?>
  </h3>
</div>

<!------------------------- FORMULARIO DE SUBIDA DE PRACTICAS ----------------------->

<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<?php 
			echo $this->Form->create('Post', ['type' => 'file', 'id' => 'login_form']);
			?>
			<label class='labelInline'><?= __('Parámetros de la tarea') ?></label><button type='button' class='btn btn-info btn-sm dropdown-toggle' id='botonVer' data-toggle='modal' data-target='#myModal'><?= __('Ver') ?></button>
			<?php
			echo "<br>";
			echo $this->Form->input('comentarios', ['type' => 'textarea', 'label' => __('Comentarios'), 'rows' => '6', 'cols' => '5', 'class' => 'form-control']);
			echo $this->Form->input('ficheroAsubir', ['type' => 'file', 'label' => __('Fichero a subir:'), 'class' => 'form-control']);
			?>
			<br>
			<?php 
			echo $this->Form->button(__('Subir'), ['type' => 'submit', 'class' => 'btn btn-success']);
			echo " ";
			echo $this->Form->button(__('Resetear campos'), ['type' => 'reset', 'class' => 'btn btn-danger']);
			echo $this->Form->end(); 
			?>
			<br>
		</div>
	</div>
</div>

<!-- Ventana con los parámetros de la tarea -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Contenido -->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?= __('Parámetros de la tarea') ?></h4>
      </div>
      <div class="modal-body">
				<ul class="list-group">
					<li class="list-group-item"><?= __('Nombre del paquete') ?><span class="badge"><?= $paquete ?></span></li>
					<li class="list-group-item"><?= __('Número máximo de intentos posibles') ?><span class="badge"><?= $num_maximo_intentos ?></span></li>
					<li class="list-group-item"><?= __('Fecha límite de entrega') ?><span class="badge"><?= $fecha_limite ?></span></li>
					<li class="list-group-item"><?= __('Número de intentos realizados') ?><span class="badge"><?= $num_intentos_realizados ?></span></li>
					<li class="list-group-item"><?= __('Número de intentos restantes') ?><span class="badge"><?= ($num_maximo_intentos - $num_intentos_realizados) ?></span></li>
				</ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cerrar') ?></button>
      </div>
    </div>

  </div>
</div>

<?php 
	  if($num_ultimo_intento != null){
	
		$ruta = "http://localhost/".$_SESSION['lti_idCurso']."/".$_SESSION['lti_idTarea']."/".$_SESSION['lti_rol'].
					"/".$_SESSION['lti_userId']."/".$num_ultimo_intento."/site/";
		$ruta_local = "../../".$_SESSION['lti_idCurso']."/".$_SESSION['lti_idTarea']."/".$_SESSION['lti_rol'].
					"/".$_SESSION['lti_userId']."/".$num_ultimo_intento."/site/";
		?>
		
		<!--------------------------- REPORTES --------------------------------->
		
		<h4 class="page-header"><?= __('Reportes del último intento realizado') ?></h4>
		<div class="div-reportes">
			<a href=<?= $ruta."javancss.html" ?> class="btn btn-default btn-lg" role="button" target="_blank">JAVANCSS</a>
			<a href=<?= $ruta."jdepend-report.html" ?> class="btn btn-default btn-lg" role="button" target="_blank">JDEPEND</a>
			<?php if(file_exists($ruta_local."pmd.html")){ ?>
					<a href=<?= $ruta."pmd.html" ?> class="btn btn-default btn-lg" role="button" target="_blank">PMD</a>
			<?php } ?>
			<?php if(file_exists($ruta_local."findbugs.html")){ ?>
					<a href=<?= $ruta."findbugs.html" ?> class="btn btn-default btn-lg" role="button" target="_blank">FINDBUGS</a>
			<?php } 
				  if(file_exists($ruta_local."surefire-report.html")){ ?>
				  	<a href=<?= $ruta."surefire-report.html" ?> class="btn btn-default btn-lg" role="button" target="_blank">ERRORES</a>
			<?php } ?>
		</div>
		
		<!-------------------------------- GRAFICAS ------------------------->
		
		<h4 class="page-header"><?= __('Gráficas disponibles') ?></h4>
		<ul class="nav nav-tabs">
		    <li><a data-toggle="tab" href="#violaciones"><?= __('Violaciones') ?></a></li>
		    <li><a data-toggle="tab" href="#errores"><?= __('Errores') ?></a></li>
		    <?php if(file_exists("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-linea.png")){ ?> 
		    <li><a data-toggle="tab" href="#violacionesErrores"><?= __('Violaciones - Errores') ?></a></li>
		    <?php }
		    	  if(file_exists("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones_ultimoIntento_barras.png")){?>
		    <li><a data-toggle="tab" href="#violacionesPrioridades"><?= __('Violaciones - Prioridades') ?></a></li>
		    <?php }?>
		</ul>
		
		<div class="tab-content">
		    <div id="violaciones" class="divGraficas tab-pane fade">
		    	<?php $nombre_grafica = $_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-violaciones.png"; ?>
				<img class="imgGraficas" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $nombre_grafica ?>"/>
			</div>
		    <div id="errores" class="divGraficas tab-pane fade">
				<?php $nombre_grafica = $_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-errores_unitarios.png"; ?>
				<img class="imgGraficas" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $nombre_grafica ?>"/>
		    </div>
		    <div id="violacionesErrores" class="divGraficas tab-pane fade">
				<?php $nombre_grafica = $_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-linea.png"; ?>
				<img class="imgGraficas" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $nombre_grafica ?>"/>
		    </div>
		    <div id="violacionesPrioridades" class="divGraficas tab-pane fade">
		    	<?php if(file_exists("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones_ultimoIntento_barras.png")){?>
		      	<?php $nombre_grafica = $_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones_ultimoIntento_barras.png"; ?>
				<img class="imgGraficas" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $nombre_grafica ?>"/>
		    	<?php $nombre_grafica = $_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones_ultimoIntento.png"; ?>
				<img class="imgGraficas" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $nombre_grafica ?>"/>
				<?php }?>
		    </div>
		</div>

		
		<?php
	 	}

	 	if($finalizado){?>
	 			waitingDialog.hide();
	 	<?php }
	 	
}
?>

<script type="text/javascript">

	$('#login_form').submit(function() {
		waitingDialog.show("Analizando práctica. El proceso podría durar varios segundos.");
	});

	$('.mensajeInfo').popover({ trigger: "hover" });
	$('.mensajeInfo').popover();
	
</script>


