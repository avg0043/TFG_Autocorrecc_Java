<?php

namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * Clase asociada a la tabla Intentos de la base de datos
 * y encargada de gestionarla.
 * 
 * @author Álvaro Vázquez Gómez.
 *
 */
class IntentosTable extends Table{
	
	/**
	 * Función encargada de establecer las relaciones existentes
	 * con las tablas Violaciones y Errores de la base de datos.
	 * 
	 */
    public function initialize(array $config){
    	
        $this->hasMany('Violaciones', [
        	'className' => 'Violaciones',
            'foreignKey' => 'intento_id',
            'joinType' => 'INNER',
        ]);
        
        $this->hasMany('Errores', [
        		'className' => 'Errores',
        		'foreignKey' => 'intento_id',
        		'joinType' => 'INNER',
        ]);
        
    }

}

?>