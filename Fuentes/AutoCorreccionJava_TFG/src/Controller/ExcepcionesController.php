<?php

namespace App\Controller;

class ExcepcionesController extends AppController{
	
	public function mostrarErrorAccesoLocal(){
		$this->set("hora_actual", date("Y-m-d H:i:s"));
	}
	
	public function mostrarErrorConsumerKey($consumer_key){
		$this->set("consumer_key", $consumer_key);
	}
	
}

?>