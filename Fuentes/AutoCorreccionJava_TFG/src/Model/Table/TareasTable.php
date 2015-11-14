<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class TareasTable extends Table{
	
	public function validationDefault(Validator $validator){
		$validator->notEmpty('num_max_intentos')
				  ->notEmpty('paquete')
				  ->notEmpty('fecha_limite');
		return $validator;
	}

}

?>