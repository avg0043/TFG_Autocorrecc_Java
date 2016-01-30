<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\I18n\I18n;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
	
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        
        if ($this->request->session()->check('Config.locale')) {
        	I18n::locale($this->request->session()->read('Config.locale'));	// cambio del idioma
        }
        
    }
    
    /**
     * Función encargada de establecer en la variabla de sesión
     * el idioma al que va a ser cambiado la aplicación.
     * Por último redirecciona a la vista actual.
     * 
     * @return $this->redirect	redirección a la vista actual.
     */
    public function changeLocale(){
    
    	$idioma = "en_EN" == $this->request->session()->read('Config.locale')?"en_ES":"en_EN";  	
    	$this->request->session()->write('Config.locale', $idioma);
    	
    	return $this->redirect($this->referer());

    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
        
    }
    
    /**
     * Functión que comprueba si la sesión está vacía, mirando el id del usuario.
     * Garantiza que únicamente se pueda acceder a la aplicación
     * desde las tareas de Moodle, y nunca desde local.
     * En caso de que no exista la sesión del id, redirecciona a la vista de error.
     * 
     */
    public function comprobarSesion(){
    	
    	if(!isset($_SESSION["lti_userId"])){
    		return $this->redirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoLocal']);
    	}

    }
    
    /**
     * Functión que comprueba que el usuario que ha accedido a la
     * aplicación es un profesor. Si no es así, redirecciona
     * a la vista de error.
     * 
     */
    public function comprobarRolProfesor(){
	
    	if($_SESSION["lti_rol"] != "Instructor"){
    		return $this->redirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoIncorrectoAlumno']);
    	}
    	
    }
    
    /**
     * Función que comprueba que el usuario que ha accedido a la
     * aplicación es un alumno. Si no es así, redirecciona a la 
     * vista de error.
     * 
     */
    public function comprobarRolAlumno(){
    
    	if($_SESSION["lti_rol"] != "Learner"){
    		return $this->redirect(['controller' => 'Excepciones', 'action' => 'mostrarErrorAccesoIncorrectoProfesor']);
    	}
    	 
    }
    
}
