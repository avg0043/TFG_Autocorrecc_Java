<h1>Configurar parámetros de la práctica</h1>

<?php
echo $this->Form->create($nueva_tarea);
echo $this->Form->input('num_intentos');
echo $this->Form->input('fecha_tope', array('type' => 'date'));
echo $this->Form->button(__('Configurar'));
echo $this->Form->end();
?>