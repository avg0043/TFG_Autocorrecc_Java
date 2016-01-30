<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Clase asociada a la tabla Profesores de la base de datos y
 * encargada de gestionarla.
 * 
 * @author Álvaro Vázquez Gómez
 *
 */
class ProfesoresTable extends Table{
	
	/**
	 * Función encargada de validar los datos correspondientes
	 * a los campos del formulario de registro del profesor en la 
	 * aplicación.
	 * 
	 * @param Validator $validator objeto validador de los datos.
	 */
	public function validationDefault(Validator $validator){
		
		$validator->notEmpty('nombre')
				  ->notEmpty('apellidos')
				  ->notEmpty('correo')
				  ->notEmpty('contraseña')
				  ->add('confirmar_contraseña',
						'compareWith', [
							'rule' => ['compareWith', 'contraseña'],
							'message' => 'Las contraseñas deben de ser iguales.'
						]
					);
		
		return $validator;
		
	}

}

?>