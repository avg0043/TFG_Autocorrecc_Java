<?php
use App\Controller\IntentosController;
?>

<link href="https://cdn.datatables.net/1.10.10/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
<script src="https://cdn.datatables.net/1.10.10/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
	    $('#tablaAlumnos').DataTable( {
	        "pagingType": "full_numbers",
		    "language": {
		    	"lengthMenu": "Mostrar _MENU_ intentos",
		    	"search": "Buscar:",
		    	"info": "Mostrando _START_ a _END_ de _TOTAL_ intentos",
		    	"infoFiltered": "(filtrado de _MAX_ total de intentos)",
			    "paginate": {
				    "first": "Primera",
				    "previous": "Anterior",
				    "next": "Siguiente",
				    "last": "Última"
			    }
		    }
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
	
	function radioBtnFuncion(){
	    var selected_radioButton = $('input[name=radioAlumno]:checked', '#tablaAlumnos');
	     	$row = $(selected_radioButton).closest("tr"),
		    $nombre_completo = $row.find("td:nth-child(2)").text(),
		    $numero_intento = $row.find("td:nth-child(3)").text(),
	    	map_alumnos = <?php echo json_encode($_SESSION["map_alumnos_id"]) ?>,
	     	ruta = "http://localhost/"+<?=$_SESSION['lti_idCurso']?>+"/"  +
	    			<?=$_SESSION['lti_idTarea']?>+"/Learner"			  +
					"/"+map_alumnos[$nombre_completo]+
					"/"+$numero_intento+"/site/";

		$.ajax({
		    data: 'id=' + map_alumnos[$nombre_completo] + '&num_intento=' + $numero_intento,
		    //url: 'http://localhost/AutoCorreccionJava_TFG/Pruebas/recibeValor',
		    url: 'http://localhost/AutoCorreccionJava_TFG/Profesores/compruebaExistenciaReportes',
		    method: 'POST', // or GET
		    success: function(respuesta) {
		    	var respuesta_reportes = $.parseJSON(respuesta);
		    	console.log(respuesta_reportes);
		    	if(!respuesta_reportes.pmd){
		    		$('#btn_pmd').prop('disabled', true);
		    	}else{
		    		$('#btn_pmd').prop('disabled', false);
		    	}
		    	if(!respuesta_reportes.findbugs){
		    		$('#btn_findbugs').prop('disabled', true);
		    	}else{
		    		$('#btn_findbugs').prop('disabled', false);
		    	}
		    	if(!respuesta_reportes.errores){
		    		$('#btn_errores').prop('disabled', true);
		    	}else{
		    		$('#btn_errores').prop('disabled', false);
		    	}
		    }
		});
				
	}
</script>

<nav class="navbar navbar-inverse">
  <ul class="nav navbar-nav">
  	<li><a href="http://localhost/AutoCorreccionJava_TFG/profesores/mostrar-panel">Inicio</a></li>
  </ul>
  <p class="navbar-text pull-right">TFG - Autocorrección de prácticas en Java</p>
</nav>

<div class="jumbotron">
  <h3>Estadísticas de los Alumnos</h3>
  <p>Visualiza las estadísticas referentes a cada uno de los alumnos. Se podrá descargar el zip correspondiente a las
     prácticas y también visualizar las gráficas con las estadísticas de cada alumno.</p>
</div>

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
			            <input name="radioAlumno" id="usuarioSeleccionado_1" type="radio" value="1" onclick="radioBtnFuncion()">
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

<button id="btn_javancss" class="btn btn-default btn-sm dropdown-toggle" onclick="btnFuncion('javancss.html')">Reporte JavanCSS</button>
<button id="btn_jdepend" class="btn btn-default btn-sm dropdown-toggle" onclick="btnFuncion('jdepend-report.html')">Reporte JDepend</button>
<button id="btn_pmd" class="btn btn-default btn-sm dropdown-toggle" onclick="btnFuncion('pmd.html')">Reporte PMD</button>
<button id="btn_findbugs" class="btn btn-default btn-sm dropdown-toggle" onclick="btnFuncion('findbugs.html')">Reporte FindBugs</button>
<button id="btn_errores" class="btn btn-default btn-sm dropdown-toggle" onclick="btnFuncion('surefire-report.html')">Reporte Errores</button>