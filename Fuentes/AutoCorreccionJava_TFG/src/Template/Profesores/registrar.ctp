<h1>Registrar Profesor</h1>

<?php
echo $this->Form->create($nuevo_profesor);
echo $this->Form->input('correo', ['type' => 'email', 'label' => 'Correo electrónico (tiene que ser el mismo que el de Moodle)']);
echo $this->Form->input('contraseña', ['type' => 'password', 'label' => 'Contraseña']);
echo $this->Form->input('confirmar_contraseña', ['type' => 'password', 'label' => 'Confirmar contraseña']);
echo $this->Form->button(__('Registrar'));
echo $this->Form->end();
?>