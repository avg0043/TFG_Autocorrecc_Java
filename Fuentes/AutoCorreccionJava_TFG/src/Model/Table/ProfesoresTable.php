<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ProfesoresTable extends Table{
	
	public function validationDefault(Validator $validator){
		$validator->notEmpty('nombre_completo')
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