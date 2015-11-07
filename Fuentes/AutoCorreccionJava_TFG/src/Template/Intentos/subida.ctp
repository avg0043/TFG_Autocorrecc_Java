<h3>Formulario subida de ficheros</h3>

<?php
if($tipo == 'profesor'){
	echo $this->Html->link('Panel profesor', ['controller' => 'Profesores', 'action' => 'mostrarPanel']);
}else{
	
	echo "* Número máximo de intentos posibles: " . $num_maximo_intentos . "<br>";
	echo "* Fecha límite de entrega: " . $fecha_limite . "<br>";
	echo "* Número de intentos realizados: " . $num_intentos_realizados . "<br>";
	echo "* Número de intentos restantes: " . ($num_maximo_intentos - $num_intentos_realizados);
	
}

echo $this->Form->create('Post', ['type' => 'file']);
echo $this->Form->input('ficheroAsubir', ['type' => 'file', 'label' => 'Fichero a subir:']);
echo $this->Form->button(__('Subir'));
echo $this->Form->end();

?>

