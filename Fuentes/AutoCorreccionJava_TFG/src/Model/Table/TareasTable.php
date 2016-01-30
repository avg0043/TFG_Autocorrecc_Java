<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Clase asociada a la tabla Tareas de la base de datos y 
 * encargada de gestionarla.
 * 
 * @author Álvaro Vázquez Gómez.
 *
 */
class TareasTable extends Table{
	
	/**
	 * Función encargada de validar los datos correspondientes
	 * a los campos del formulario de configuración de parámetros
	 * de la tarea.
	 * 
	 * @param Validator $validator	objeto validador de los datos.
	 */
	public function validationDefault(Validator $validator){

		$validator->add('num_max_intentos', 
					    'valid', [
					    	'rule' => ['range', 1, 20], 
					    	'message' => 'Valor incorrecto. Debe de estar comprendido entre 1 y 20.' 		
					    ]
					)
				  ->notEmpty('num_max_intentos')
				  ->notEmpty('paquete')
				  ->notEmpty('fecha_limite');
		
		return $validator;
		
	}

}

?>