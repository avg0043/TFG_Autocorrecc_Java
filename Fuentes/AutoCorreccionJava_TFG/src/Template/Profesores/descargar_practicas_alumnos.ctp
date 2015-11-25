<?php
use App\Controller\IntentosController;
?>
<div class="page-header">
	<h3>Descarga de prácticas de los alumnos</h3>
</div>

<?= $this->Html->link('Panel profesor', ['action' => 'mostrarPanel']) ?>

<?php 
if(!$alumnos->isEmpty() && !$intentos->isEmpty()){
	$intentos_controller = new IntentosController();
?>
<table>
	<tr>
		<th>Nombre completo</th>
		<th>Número del intento</th>
		<th>Test pasado</th>
		<th>Fecha de subida</th>
		<th>Práctica</th>
	</tr>
	<?php 
	foreach ($alumnos as $alumno):
		$intentos_alumno = $intentos_controller->obtenerIntentosPorIdAlumno($alumno->id);
		if(!$intentos_alumno->isEmpty()){
	?>
		<tr>
			<td colspan="5"><b><?= $alumno->nombre." ".$alumno->apellidos ?></b></td>
		</tr>
			<?php foreach ($intentos_alumno as $intento):?>
			<tr>
				<td></td>
				<td><?= $intento->numero_intento ?></td>
				<td><?= ($intento->resultado == true ? "sí" : "no") ?></td>
				<td><?= $intento->fecha_intento ?></td>
				<td><a href=<?= $intento->ruta.$intento->nombre ?> ><?= $intento->nombre?></a></td>
			</tr>
			<?php endforeach;?>
	<?php 
		}
	endforeach; 
	?>
</table>
<?php }?>