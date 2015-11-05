<h1>Añadir Artículo</h1>
<?php
echo $this->Form->create($article);
echo $this->Form->input('title');
echo $this->Form->input('body', ['rows' => '3']);
echo $this->Form->button(__('Guardar artículo'));
echo $this->Form->end();
?>