<?php
use App\Controller\IntentosController;
?>

<link href="https://cdn.datatables.net/1.10.10/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
<script src="https://cdn.datatables.net/1.10.10/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
	    $('#tablaAlumnos').DataTable( {
	        "pagingType": "full_numbers"
	    } );
	} );
	function btnFuncion(nombre_reporte) {
	    var selected_radioButton = $('input[name=radioAlumno]:checked', '#tablaAlumnos');
	    var $row = $(selected_radioButton).closest("tr"),
		    $nombre_completo = $row.find("td:nth-child(2)").text(),
		    $numero_intento = $row.find("td:nth-child(3)").text();
        /*
	    $.each($id_alumno, function() {
	        console.log($(this).text());
	    });
	    */
	    var map_alumnos = <?php echo json_encode($_SESSION["map_alumnos_id"]) ?>;
	    var ruta = "http://localhost/"+<?=$_SESSION['lti_idCurso']?>+"/"  +
	    			<?=$_SESSION['lti_idTarea']?>+"/Learner"			  +
					"/"+map_alumnos[$nombre_completo]+
					"/"+$numero_intento+"/site/"+nombre_reporte;
	    window.open(
    		  ruta,
    		  '_blank'
    	);
	}
</script>

<div class="jumbotron">
  <h3>Estadísticas de los Alumnos</h3>
  <p>Visualiza las estadísticas referentes a cada uno de los alumnos. Se podrá descargar el zip correspondiente a las
     prácticas y también visualizar las gráficas con las estadísticas de cada alumno.</p>
</div>

<?= $this->Html->link('Panel profesor', ['action' => 'mostrarPanel']) ?>

<?php 
if(!$alumnos->isEmpty() && !$intentos->isEmpty()){
	$intentos_controller = new IntentosController();
?>
<table id="tablaAlumnos" class="display">
	<thead>
		<tr>
			<th>Select</th>
			<th>Nombre completo</th>
			<th>Número del intento</th>
			<th>Test pasado</th>
			<th>Fecha de subida</th>
			<th>Práctica</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$_SESSION["map_alumnos_id"] = array();
		foreach ($alumnos as $alumno):
			$_SESSION["map_alumnos_id"][$alumno->nombre." ".$alumno->apellidos] = $alumno->id;
			//$intentos_alumno = $intentos_controller->obtenerIntentosPorIdAlumno($alumno->id);
			$intentos_alumno = $intentos_controller->obtenerIntentosPorIdTareaAlumno($_SESSION["lti_idTarea"], $alumno->id);
			if(!$intentos_alumno->isEmpty()){
		?>
				<?php foreach ($intentos_alumno as $intento):?>
				<tr>

					<td scope="col">
			            <input name="radioAlumno" id="usuarioSeleccionado_1" type="radio" value="1">
			        </td>
			        <td><b><?= $alumno->nombre." ".$alumno->apellidos ?></b></td>
					<td align="center"><?= $intento->numero_intento ?></td>
					<td><?= ($intento->resultado == true ? "sí" : "no") ?></td>
					<td><?= $intento->fecha_intento ?></td>
					<td><a href=<?= $intento->ruta.$intento->nombre ?> ><?= $intento->nombre?></a></td>
				</tr>
				<?php endforeach;?>
		<?php 
			}
		endforeach; 
		?>
	</tbody>
</table>
<?php }?>

<button class="btn btn-default btn-sm dropdown-toggle" onclick="btnFuncion('javancss.html')">Reporte JavanCSS</button>
<button class="btn btn-default btn-sm dropdown-toggle" onclick="btnFuncion('jdepend-report.html')">Reporte JDepend</button>
<button class="btn btn-default btn-sm dropdown-toggle" onclick="btnFuncion('pmd.html')">Reporte PMD</button>
<button class="btn btn-default btn-sm dropdown-toggle" onclick="btnFuncion('findbugs.html')">Reporte FindBugs</button>
<button class="btn btn-default btn-sm dropdown-toggle" onclick="btnFuncion('surefire-report.html')">Reporte Errores</button>