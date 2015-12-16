<!--  
<div class="page-header">
	<h3>Panel de opciones del profesor</h3>
</div>
-->

<?php
/*
echo $this->Html->link('Configurar parámetros de la práctica', ['controller' => 'Tareas', 'action' => 'configurarParametrosTarea']);
echo "<br>";
echo $this->Html->link('Información del profesor', ['action' => 'mostrarDatosProfesor']);
echo "<br>";
echo $this->Html->link('Subida de Tests', ['controller' => 'Tests', 'action' => 'subirTest']);
echo "<br>";
echo $this->Html->link('Ver alumnos registrados', ['controller' => 'Alumnos', 'action' => 'mostrarAlumnos']);
echo "<br>";
echo $this->Html->link('Comprobar plagios de prácticas', ['action' => 'generarReportePlagiosPracticas']);
echo "<br>";
echo $this->Html->link('Descargar prácticas subidas por los alumnos', ['action' => 'descargarPracticasAlumnos']);
echo "<br>";
echo $this->Html->link('Mostrar gráficas', ['action' => 'generarGraficas']);
*/
?>

<div class="row">

  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/tuerca3.jpg" alt="epa">
      <div class="caption">
        <h3>Configuración de parámetros</h3>
        <p>Formulario para poder configurar los parámetros asociados a las prácticas.
           Es imprescindible configurarlo para que los alumnos puedan subir sus prácticas.</p>
        <p><a href="http://localhost/AutoCorreccionJava_TFG/tareas/configurarParametrosTarea" class="btn btn-primary" role="button">Acceder</a></p>
      </div>
    </div>
  </div>
  
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/test.png" alt="epa">
      <div class="caption">
        <h3>Subida de test</h3>
        <p>Formulario para subir los test que van a aplicarse a las prácticas. Permite la subida nuevos test.</p>
        <p><a href="http://localhost/AutoCorreccionJava_TFG/Tests/subirTest" class="btn btn-primary" role="button">Acceder</a></p>
      </div>
    </div>
  </div>
    
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/comprobacion.jpg" alt="epa">
      <div class="caption">
        <h3>Comprobación de plagios de las prácticas</h3>
        <p>Realizará una comprobación de las últimas prácticas subidas por todos los alumnos. Si todo es correcto
        se mostrará el reporte con los plagios, en caso contrario se mostrará un mensaje
        con los motivos por los que no ha podido realizarse tal comprobación.</p>
        <p><a href="http://localhost/AutoCorreccionJava_TFG/Profesores/generarReportePlagiosPracticas" class="btn btn-primary" role="button">Acceder</a></p>
      </div>
    </div>
  </div>
  
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/perfil.jpg" alt="epa">
      <div class="caption">
        <h3>Estadísticas Alumnos</h3>
        <p>Tabla con estadísticas referentes a los diferentes alumnos. Se mostrará la información de cada alumno
        y además las estadísticas que se corresponden con cada uno de los intentos de subida de práctica realizados.</p>
        <p><a href="http://localhost/AutoCorreccionJava_TFG/Profesores/descargarPracticasAlumnos" class="btn btn-primary" role="button">Acceder</a></p>
      </div>
    </div>  
  </div>
  
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <img src="http://localhost/AutoCorreccionJava_TFG/webroot/img/grafica.png" alt="epa">
      <div class="caption">
        <h3>Gráficas</h3>
        <p>Ofrece una visualización a partir de gráficas, de las estadísticas globales correspondientes a las
        prácticas subidas por los alumnos.</p>
        <p><a href="http://localhost/AutoCorreccionJava_TFG/Profesores/generarGraficas" class="btn btn-primary" role="button">Acceder</a></p>
      </div>
    </div>
  </div>
  
</div>