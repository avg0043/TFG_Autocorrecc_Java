<?php

namespace App\Controller;

class ProfesoresController extends AppController{

	/*
	public function index(){
		$this->set('profesores', $this->profesores->find('all'));
	}
	
	public function view($id = null){
		$this->set('profesore', $this->profesores->get($id));
    }
	*/
	
	public function add(){
		$profesore = $this->Profesores->newEntity();
		if ($this->request->is('post')) {
			$profesore = $this->Profesores->patchEntity($profesore, $this->request->data);
			if ($this->Profesores->save($profesore)) {
				$this->Flash->success(__('Your profesore has been saved.'));
				return $this->redirect(['action' => 'index']);
			}
			$this->Flash->error(__('Unable to add your profesore.'));
		}
		$this->set('profesore', $profesore);
	}
	
}

?>