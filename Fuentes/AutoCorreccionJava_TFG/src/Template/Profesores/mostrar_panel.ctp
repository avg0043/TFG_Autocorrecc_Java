
<?= $this->Html->css('custom.css') ?>

<div class="jumbotron">
  <h3 class="panelProfesor"><?= __('PANEL DEL PROFESOR') ?></h3>
</div>

<div class="row">

  <div class="col-xs-6 col-md-4">
    <h3><?= __('Configuración') ?></h3>
    <a href="http://localhost/AutoCorreccionJava_TFG/tareas/configurarParametrosTarea" class="thumbnail">
      <img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/tuerca3.jpg" alt="...">
    </a>
  </div>
  
  <div class="col-xs-6 col-md-4">
    <h3><?= __('Subida') ?></h3>
    <a href="http://localhost/AutoCorreccionJava_TFG/Tests/subirTest" class="thumbnail">
      <img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/test.png" alt="...">
    </a>
  </div>
  
  <div class="col-xs-6 col-md-4">
    <h3><?= __('Estadísticas') ?></h3>
    <a href="http://localhost/AutoCorreccionJava_TFG/Profesores/descargarPracticasAlumnos" class="thumbnail">
      <img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/1452907845_multimedia-24.png" alt="..." height="150" width="150">
    </a>
  </div>
  
  <div class="col-xs-6 col-md-4">
  	<h3><?= __('Gráficas') ?></h3>
    <a href="http://localhost/AutoCorreccionJava_TFG/Profesores/generarGraficas" class="thumbnail">
      <img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/grafica.png" alt="...">
    </a>
  </div>
  
  <div class="col-xs-6 col-md-4">
    <h3><?= __('Plagios') ?></h3>
    <a href="http://localhost/AutoCorreccionJava_TFG/Profesores/generarReportePlagiosPracticas" class="thumbnail">
      <img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/comprobacion.jpg" alt="...">
    </a>
  </div>
  
</div>
