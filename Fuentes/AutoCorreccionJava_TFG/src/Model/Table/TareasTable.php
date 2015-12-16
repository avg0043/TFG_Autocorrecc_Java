<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class TareasTable extends Table{
	
	public function validationDefault(Validator $validator){
		/*
		$validator->notEmpty('num_max_intentos')
				  ->notEmpty('paquete')
				  ->notEmpty('fecha_limite');
		*/
		$validator->add('num_max_intentos', 
					    'valid', [
					    	'rule' => ['range', 1, 20], 
					    	'message' => 'Valor incorrecto. Debe de estar comprendido entre 1 y 20.' 		
					    ]
					)
				  ->notEmpty('num_max_intentos')
				  ->notEmpty('paquete')
				  /*
				  ->add('fecha_limite', 
				  		'valid', [
				  			'rule' => function ($value) {
				            	return $value > date('Y-m-d'); },
				            'message' => 'Invalid date.'
				        ]
				  )
				  */
				  ->notEmpty('fecha_limite');
		return $validator;
	}

}

?>