<div class="page-header">
	<h3>Parámetros LTI</h3>
</div>

<?php foreach ($parametros as $param): ?>

<div class="bs-example">
	<div class="list-group">
	    <div class="list-group-item">
            <span class="glyphicon glyphicon-cog"></span><b>URL: </b>http://localhost/AutoCorreccionJava_TFG/conexiones/establecerConexion
        </div>
		<div class="list-group-item">
            <span class="glyphicon glyphicon-cog"></span><b>Consumer key: </b><?= $param->consumer_key ?>
        </div>
        <div class="list-group-item">
            <span class="glyphicon glyphicon-cog"></span><b>Secret: </b><?= $param->secret ?>
        </div>
	</div>
</div>
	
<?php endforeach; ?>


