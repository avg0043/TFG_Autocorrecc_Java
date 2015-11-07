<h3>Panel de opciones del profesor</h3>

<?php
echo $this->Html->link('Configurar parámetros de la práctica', ['controller' => 'Tareas', 'action' => 'configurarParametros']);
echo "<br>";
echo $this->Html->link('Información del profesor', ['action' => 'mostrarDatos']);
echo "<br>";
echo $this->Html->link('Formulario subida de ficheros', ['controller' => 'Intentos', 'action' => 'subida', 'profesor']);
echo "<br>";
echo $this->Html->link('Ver alumnos registrados', ['controller' => 'Alumnos', 'action' => 'mostrarAlumnos']);
?>