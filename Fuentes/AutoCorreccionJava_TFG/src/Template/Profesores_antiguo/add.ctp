<h1>Añadir profesor</h1>
<?php
echo $this->Form->create($profesore);
echo $this->Form->input('correo');
echo $this->Form->button(__('Guardar artículo'));
echo $this->Form->end();
?>