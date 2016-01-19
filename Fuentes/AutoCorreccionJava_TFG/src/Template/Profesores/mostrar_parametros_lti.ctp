
<?= $this->Html->css('custom.css') ?>

<div class="jumbotron">
  <h3><?= __('Parámetros LTI') ?>
     <img class="mensajeInfo iconos" data-content="<?= __("Utiliza estos parámetros para poder crear desde Moodle una tarea de tipo 'herramienta externa'
  que enlace con la aplicación web de Autocorrección de prácticas Java.") ?>" data-placement="auto" title="<?= __('INFORMACIÓN') ?>" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/info_2.png"/>
  </h3>
</div>

<?php foreach ($parametros as $param): ?>

<div class="bs-example">
	<div class="list-group col-md-10 col-md-offset-1">
	    <div class="list-group-item">
            <span class="glyphicon glyphicon-cog"></span><b> URL: </b>http://localhost/AutoCorreccionJava_TFG/conexiones/establecerConexion
        </div>
		<div class="list-group-item">
            <span class="glyphicon glyphicon-cog"></span><b> Consumer key: </b><?= $param->consumer_key ?>
        </div>
        <div class="list-group-item">
            <span class="glyphicon glyphicon-cog"></span><b> Secret: </b><?= $param->secret ?>
        </div>
	</div>
</div>
	
<?php endforeach; ?>

<script type="text/javascript">

	$('.mensajeInfo').popover({ trigger: "hover" });
	$('.mensajeInfo').popover();
	
</script>
