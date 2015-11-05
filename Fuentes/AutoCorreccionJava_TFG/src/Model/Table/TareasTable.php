<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class TareasTable extends Table{
	
	public function validationDefault(Validator $validator){
		$validator->notEmpty('num_intentos')
				  ->notEmpty('fecha_tope');
		return $validator;
	}

}

?>