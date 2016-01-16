<nav class="navbar navbar-inverse">
  <p class="navbar-text pull-right">TFG - Autocorrección de prácticas en Java</p>
</nav>

<?php
if(!$test_subido){
?>
	<h4>Todavía no puede subir la práctica porque el profesor no ha subido test</h4>
<?php
}
else{
?>

<div class="jumbotron">
  <h3>Subida de Prácticas 
  	<img class="mensajeInfo" data-content="Sube la práctica en un fichero .zip que contenga la estructura de carpetas correcta correspondiente al paquete.
  	 Es decir: si el paquete es 'uni.ubu', la estructura de carpetas a subir sería: uni/ubu/nombrePractica.java.
  	 Tras la subida se mostrarán los reportes y, si no lo estaban ya, las gráficas correspondientes." data-placement="auto" title="INFORMACIÓN DE LA SUBIDA" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/info.png"/>
  	
  	<!-- Verificar si existe Enunciado o no -->
  	<?php if($enunciado != null){ ?>	
  	<img class="mensajeInfo" data-content="Se deberán de crear 2 subpaquetes adicionales: es.ubu.model y 
  	 es.ubu.controller. En el primero se deberá de crear la clase Ficheros.java, la cual debe permitir crear un Fichero gracias a sus métodos 'set' y 'get'.. ETC ETC" data-placement="auto" title="ENUNCIADO DE LA PRÁCTICA" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/enunciado.png"/>
  	<?php }?>
  </h3>
</div>
 
<div class="container">
	<div class="row">
		<div class="col-md-6">
			<?php 
			echo $this->Form->create('Post', ['type' => 'file']);
			echo $this->Form->input('comentarios', ['type' => 'textarea', 'label' => 'Comentarios', 'rows' => '6', 'cols' => '5', 'class' => 'form-control']);
			echo $this->Form->input('ficheroAsubir', ['type' => 'file', 'label' => 'Fichero a subir:', 'class' => 'form-control']);
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
		<div class="col-md-6">
	 		<div class="panel-body">
				<ul class="list-group">
					<li class="listTitulo list-group-item"><b>Parámetros de la tarea</b><span class="badge"></span></li>
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

<?php 
	  if($num_ultimo_intento != null){
	
		$ruta = "http://localhost/".$_SESSION['lti_idCurso']."/".$_SESSION['lti_idTarea']."/".$_SESSION['lti_rol'].
					"/".$_SESSION['lti_userId']."/".$num_ultimo_intento."/site/";
		$ruta_local = "../../".$_SESSION['lti_idCurso']."/".$_SESSION['lti_idTarea']."/".$_SESSION['lti_rol'].
					"/".$_SESSION['lti_userId']."/".$num_ultimo_intento."/site/";
		?>
		
		<h4 class="page-header">Reportes del último intento realizado</h4>
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
				  	<a href=<?= $ruta."surefire-report.html" ?> class="btn btn-default btn-lg" role="button" target="_blank">ERRORES UNITARIOS</a>
			<?php } ?>
		</div>
		
		<!--  <div class="container"> -->
		<h4 class="page-header">Gráficas disponibles</h4>
		<ul class="nav nav-tabs">
		    <li class="active"><a data-toggle="tab" href="#violaciones">Violaciones</a></li>
		    <li><a data-toggle="tab" href="#errores">Errores</a></li>
		    <?php if(file_exists("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-linea.png")){ ?> 
		    <li><a data-toggle="tab" href="#violacionesErrores">Violaciones - Errores</a></li>
		    <?php }
		    	  if(file_exists("img/".$_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones_ultimoIntento_barras.png")){?>
		    <li><a data-toggle="tab" href="#violacionesPrioridades">Violaciones - Prioridades</a></li>
		    <?php }?>
		</ul>
		
		<div class="tab-content">
		    <div id="violaciones" class="divGraficas tab-pane fade in active">
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
		      	<?php $nombre_grafica = $_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones_ultimoIntento_barras.png"; ?>
				<img class="imgGraficas" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $nombre_grafica ?>"/>
		    	<?php $nombre_grafica = $_SESSION["lti_idTarea"]."-".$_SESSION["lti_userId"]."-prioridades_violaciones_ultimoIntento.png"; ?>
				<img class="imgGraficas" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $nombre_grafica ?>"/>
		    </div>
		</div>
		<!--  </div> -->

		
		<?php
	 	}

}
?>

<script type="text/javascript">
	//$('#popoverData').popover();
	$('.mensajeInfo').popover({ trigger: "hover" });
	$('.mensajeInfo').popover();
</script>


