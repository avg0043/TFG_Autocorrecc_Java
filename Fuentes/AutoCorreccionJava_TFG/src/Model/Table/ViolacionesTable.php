<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class ViolacionesTable extends Table{
	
    public function initialize(array $config){
    	
        $this->belongsTo('Intentos', [
        	'className' => 'Intentos',
            'foreignKey' => 'intento_id',
            'joinType' => 'INNER',
        ]);
        
    }

}

?>