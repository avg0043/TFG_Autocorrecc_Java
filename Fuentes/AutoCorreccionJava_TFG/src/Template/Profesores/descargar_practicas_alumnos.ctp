<?php
use App\Controller\IntentosController;
use Cake\ORM\TableRegistry;
?>

<?= $this->Html->css('custom.css') ?>

<?php
if($intentos_todos->isEmpty()){
?>
	<h4><?= __('Las estadísticas no están disponibles: ningún alumno ha subido su práctica.') ?></h4>
<?php 
}
else{
?>

<!------------------------ TITULO DEL PANEL ----------------------->

<div class="jumbotron">
  <h3><?= __('Estadísticas de las prácticas de los alumnos') ?></h3>
</div>

<?php 
if(!$alumnos->isEmpty() && !$intentos->isEmpty()){
	$intentos_controller = new IntentosController();
?>

<!---------------------- TABLA CON LAS ESTADISTAS DE LAS PRACTICAS ------------>

<table id="tablaAlumnos" class="display">
	<thead>
		<tr>
			<th style="width: 10px;"></th>
			<th style="width: 190px;"><?= __('Nombre') ?></th>
			<th style="width: 50px;"><?= __('Intento') ?></th>
			<th style="width: 90px;"><?= __('Test pasado') ?></th>
			<th style="width: 85px;"><?= __('Comentarios') ?></th>
			<th style="width: 120px;"><?= __('Fecha subida') ?></th>
			<th style="width: 140px;"><?= __('Práctica') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$_SESSION["map_alumnos_id"] = array();
		foreach ($alumnos as $alumno):
			$_SESSION["map_alumnos_id"][$alumno->nombre." ".$alumno->apellidos] = $alumno->id;
			$intentos_tabla = TableRegistry::get("Intentos");
			$intentos_alumno = $intentos_tabla->find('all')
    										  ->where(['tarea_id' => $_SESSION["lti_idTarea"], 'alumno_id' => $alumno->id]);
			
			if(!$intentos_alumno->isEmpty()){
		?>
				<?php foreach ($intentos_alumno as $intento):?>
				<tr>

					<td scope="col">
			            <input name="radioAlumno" id="usuarioSeleccionado_1" type="radio" value="1" onclick="radioBtnFuncion()">
			        </td>
			        <td><?= $alumno->nombre." ".$alumno->apellidos ?></td>
					<td align="center"><?= $intento->numero_intento ?></td>
					<td><?= ($intento->resultado == true ? __("sí") : __("no")) ?></td>
					<td>
						<?php if($intento->comentarios != null){ ?>
							<img class="mensajeInfo" data-content="<?= $intento->comentarios ?>" data-placement="auto" title="<?= __('COMENTARIOS DE LA PRÁCTICA') ?>" src="http://localhost/AutoCorreccionJava_TFG/webroot/img/comentarios.png"/>
						<?php }else{?>
							<img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/no_comentario.png"/>
						<?php }?>
					</td>
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

<!------------------------- BOTONES CON LOS REPORTES ----------------------->

<div class="botonesReportes">
	<button id="btn_javancss" class="btn btn-default btn-sm dropdown-toggle" disabled onclick="btnFuncion('javancss.html')"><?= __('Reporte JavanCSS') ?></button>
	<button id="btn_jdepend" class="btn btn-default btn-sm dropdown-toggle" disabled onclick="btnFuncion('jdepend-report.html')"><?= __('Reporte JDepend') ?></button>
	<button id="btn_pmd" class="btn btn-default btn-sm dropdown-toggle" disabled onclick="btnFuncion('pmd.html')"><?= __('Reporte PMD') ?></button>
	<button id="btn_findbugs" class="btn btn-default btn-sm dropdown-toggle" disabled onclick="btnFuncion('findbugs.html')"><?= __('Reporte FindBugs') ?></button>
	<button id="btn_errores" class="btn btn-default btn-sm dropdown-toggle" disabled onclick="btnFuncion('surefire-report.html')"><?= __('Reporte Errores') ?></button>
</div>

<?php
}
?>

<script type="text/javascript">
	$(document).ready(function() {
		<?php if($this->request->session()->read('Config.locale') != "en_EN"){?>
	    $('#tablaAlumnos').DataTable( {
	        "pagingType": "full_numbers",
		    "language": {
		    	"lengthMenu": "Mostrar _MENU_ intentos",
		    	"search": "Buscar:",
		    	"info": "Mostrando _START_ a _END_ de _TOTAL_ intentos",
		    	"infoFiltered": "(filtrado de _MAX_ total de intentos)",
		    	"zeroRecords": "No se encontraron intentos concidentes",
			    "paginate": {
				    "first": "Primera",
				    "previous": "Anterior",
				    "next": "Siguiente",
				    "last": "Última"
			    }
		    }
	    } );
	    <?php }else{?>
	    $('#tablaAlumnos').DataTable( {
	        "pagingType": "full_numbers",
		    "language": {
		    	"lengthMenu": "Show _MENU_ attempts",
		    	"search": "Search:",
		    	"info": "Showing _START_ to _END_ of _TOTAL_ attempts",
		    	"infoFiltered": "(filtered from _MAX_ total de attempts)",
		    	"zeroRecords": "No matching attempts found",
			    "paginate": {
				    "first": "First",
				    "previous": "Previous",
				    "next": "Next",
				    "last": "Last"
			    }
		    }
	    } );
	    <?php }?>
	} );
	
	function btnFuncion(nombre_reporte) {
		
	    var selected_radioButton = $('input[name=radioAlumno]:checked', '#tablaAlumnos');
	    var $row = $(selected_radioButton).closest("tr"),
		    $nombre_completo = $row.find("td:nth-child(2)").text(),
		    $numero_intento = $row.find("td:nth-child(3)").text();
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
		    url: 'http://localhost/AutoCorreccionJava_TFG/Profesores/compruebaExistenciaReportes',
		    method: 'POST', 
		    success: function(respuesta) {
			    
		    	var respuesta_reportes = $.parseJSON(respuesta);
		    	
		    	// Los reportes JavanCSS y JDepend siempre están disponibles
		    	$('#btn_javancss').prop('disabled', false);
		    	$('#btn_jdepend').prop('disabled', false);
		    	
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

	$('.mensajeInfo').popover({ trigger: "hover" });
	$('.mensajeInfo').popover();

</script>
