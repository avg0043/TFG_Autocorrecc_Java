<div class="container">
	<div class="row">
		<div class="col-md-6">
			<?php 
			echo $this->Form->create('Post');
			echo $this->Form->input('Medias', ['type' => 'checkbox', 'value' => true, 'label' => 'Medias', 'class' => 'form-control']);
			echo "<br>";
			echo $this->Form->input('Test', ['type' => 'checkbox', 'value' => true, 'label' => 'Test', 'class' => 'form-control']);
			echo "<br>";
			echo $this->Form->input('Violaciones', ['type' => 'checkbox', 'value' => true, 'label' => 'Violaciones', 'class' => 'form-control']);
			echo "<br>";
			echo $this->Form->input('IntentosPasaTest', ['type' => 'checkbox', 'value' => true, 'label' => 'Intentos pasan test', 'class' => 'form-control']);
			echo "<br>";
			echo $this->Form->input('IntentosNoPasaTest', ['type' => 'checkbox', 'value' => true, 'label' => 'Intentos no pasan test', 'class' => 'form-control']);
			echo "<br>";
			echo $this->Form->input('Todas', ['type' => 'checkbox', 'value' => true, 'label' => 'Todas', 'class' => 'form-control']);
			?>
			<br>
			<?php echo $this->Form->button('Generar Gráficas', ['type' => 'submit', 'class' => 'btn btn-success']); ?>
			<?php echo $this->Form->end(); ?>
			<br>
		</div>
	</div>
</div>

<?php 
if($_SESSION["violaciones"]){ ?>
	<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/<?= $_SESSION["lti_idTarea"]."-prof-test.png" ?>" style="border: 1px solid gray;"/>
<?php 	
}?>