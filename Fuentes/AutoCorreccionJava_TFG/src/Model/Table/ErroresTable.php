<?php

namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * Clase asociada a la tabla Errores de la base de datos y 
 * encargada de gestionarla.
 * 
 * @author Álvaro Vázquez Gómez.
 *
 */
class ErroresTable extends Table{
	
	/**
	 * Función encargada de establecer las relaciones existentes
	 * con la tabla Intentos de la base de datos.
	 * 
	 */
    public function initialize(array $config){
    	
        $this->belongsTo('Intentos', [
        	'className' => 'Intentos',
            'foreignKey' => 'intento_id',
            'joinType' => 'INNER',
        ]);
        
    }

}

?>