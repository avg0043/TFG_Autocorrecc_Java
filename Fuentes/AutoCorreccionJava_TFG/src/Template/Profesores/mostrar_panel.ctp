<div class="page-header">
	<h3>Panel de opciones del profesor</h3>
</div>

<?php
echo $this->Html->link('Configurar parámetros de la práctica', ['controller' => 'Tareas', 'action' => 'configurarParametrosTarea']);
echo "<br>";
echo $this->Html->link('Información del profesor', ['action' => 'mostrarDatosProfesor']);
echo "<br>";
echo $this->Html->link('Subida de Tests', ['controller' => 'Tests', 'action' => 'subirTest']);
echo "<br>";
echo $this->Html->link('Ver alumnos registrados', ['controller' => 'Alumnos', 'action' => 'mostrarAlumnos']);
?>