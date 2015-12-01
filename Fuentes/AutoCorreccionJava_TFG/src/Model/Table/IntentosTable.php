<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class IntentosTable extends Table{
	
    public function initialize(array $config){
    	
        $this->hasMany('Violaciones', [
        	'className' => 'Violaciones',
            'foreignKey' => 'intento_id',
            'joinType' => 'INNER',
        ]);
        
    }

}

?>