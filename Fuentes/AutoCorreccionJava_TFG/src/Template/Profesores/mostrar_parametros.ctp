<h3>Parámetros LTI</h3>

<?php foreach ($parametros as $param): ?>
	<ul>
		<li><b>Consumer key: </b><?= $param->consumer_key ?></li>
		<li><b>Secret: </b><?= $param->secret ?></li>
	</ul>
<?php endforeach; ?>


