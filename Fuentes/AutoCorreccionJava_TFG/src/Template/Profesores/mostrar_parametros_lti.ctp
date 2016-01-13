<nav class="navbar navbar-inverse">
  <p class="navbar-text pull-right">TFG - Autocorrección de prácticas en Java</p>
</nav>

<div class="jumbotron">
  <h3>Parámetros LTI</h3>
  <p>Utiliza estos parámetros para poder crear desde Moodle una tarea de tipo "herramienta externa"
  que enlace con la aplicación web de Autocorrección de prácticas Java.</p>
</div>

<?php foreach ($parametros as $param): ?>

<div class="bs-example">
	<div class="list-group col-md-8 col-md-offset-2">
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


