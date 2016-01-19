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
	  <?php if(isset($_SESSION["lti_userId"]) && $_SESSION["lti_rol"] == "Instructor"){?>
	  <section class="top-bar-section">
	    <ul class="left">
	      <li><a href="http://localhost/AutoCorreccionJava_TFG/profesores/mostrar-panel"><?= __('Panel Inicio') ?></a></li>
	    </ul>
	  </section>
	  <?php }?>
	  
	</nav>
	
    <?= $this->Flash->render() ?>
    <section class="container clearfix">
        <?= $this->fetch('content') ?>
    </section>
    <footer>
    </footer>
</body>
</html>
