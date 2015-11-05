<h1>Formulario subida de ficheros</h1>

<?php
if($tipo == 'profesor'){
	echo $this->Html->link('Panel profesor', ['controller' => 'Profesores', 'action' => 'mostrarPanel']);
}
?>


<?php

echo $this->Form->create('Post', ['type' => 'file']);
echo $this->Form->input('ficheroAsubir', ['type' => 'file', 'label' => 'Fichero a subir']);
echo $this->Form->button(__('Subir'));
echo $this->Form->end();

?>

<!--
<form action="subida" method="post" enctype="multipart/form-data">
	Selecciona el fichero:
	<input type="file" name="ficheroAsubir">
	<input type="submit" value="Subir fichero" name="submit">
</form>
-->
