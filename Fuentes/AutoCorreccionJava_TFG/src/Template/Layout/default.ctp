<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = 'CakePHP: the rapid development php framework';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>
			    
    <?= $this->Html->css('bootstrap.min') ?>
    <?= $this->Html->css('bootstrap-theme.min') ?>
    <?= $this->Html->css('custom.css') ?>
    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('cake.css') ?>
    
    <?= $this->Html->css('jquery.dataTables.min.css') ?> <!-- new -->
    <?= $this->Html->script('jquery-1.11.3.min') ?> <!-- new -->
    <?= $this->Html->script('jquery.dataTables.min') ?> <!-- new -->
    

	<!-- $this->Html->script('jquery-1.11.3') ->PROBAR A PONERLO EN LA VISTA EN VEZ DE AQUÍ -->
	<?= $this->Html->script('bootstrap.min') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
	
	<nav class="top-bar expanded" data-topbar role="navigation">
	  <ul class="right title-area large-4 medium-4 columns">
	    <li class="name">
	      <h1><a href=""><?= __('TFG - Autocorrección prácticas en Java') ?></a></h1>
	    </li>
	  </ul>
	  
	  <section class="top-bar-section">
	    <ul class="left">
	      <?php if(isset($_SESSION["lti_userId"]) && $this->name != "excepciones" && $_SESSION["lti_rol"] == "Instructor"){?>
		      <li><a id="panelInicio" href="http://localhost/AutoCorreccionJava_TFG/profesores/mostrar-panel"><?= __('Panel Inicio') ?></a></li>
		      <li><a href="http://localhost/AutoCorreccionJava_TFG/tareas/configurarParametrosTarea"><?= __('Configuración') ?></a></li>
		      <li><a href="http://localhost/AutoCorreccionJava_TFG/tests/subirTest"><?= __('Subida') ?></a></li>
		      <li><a href="http://localhost/AutoCorreccionJava_TFG/profesores/descargarPracticasAlumnos"><?= __('Estadísticas') ?></a></li>
		      <li><a href="http://localhost/AutoCorreccionJava_TFG/profesores/generarGraficas"><?= __('Gráficas') ?></a></li>   
		      <li><a href="http://localhost/AutoCorreccionJava_TFG/profesores/generarReportePlagiosPracticas"><?= __('Plagios') ?></a></li> 
	      <?php 
	      }
	      ?>
	      <li>
	   	  <?php 
	   	  	echo $this->Html->link('Cambiar Idioma', array('controller' => 'app', 'action' => 'change_locale'), array('id' => 'cambiarIdioma'));	   
	   	  	//echo $this->Html->link($this->Html->image("enunciado_2.png", ["alt" => "Brownies"]), array('controller' => 'app', 'action' => 'change_locale'), array('id' => 'cambiarIdioma'),  ['escape' => false]);   
	   	  ?>
	   	  </li>
	    </ul>
	  </section>
	  
	</nav>
	
		
    <?= $this->Flash->render() ?>
    <section class="container clearfix">
        <?= $this->fetch('content') ?>
    </section>
    <footer>
    </footer>
</body>
</html>


