<h3>Configuración de los parámetros obligatorios de la práctica</h3>

<?php
echo $this->Form->create($nueva_tarea);
echo $this->Form->input('num_max_intentos');
echo $this->Form->input('fecha_limite', array('type' => 'date'));
echo $this->Form->button(__('Guardar configuración'));
echo $this->Form->end();
?>