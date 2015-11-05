<?php

namespace App\Controller;

class ArticlesController extends AppController{

	/*
	public function index(){
		$this->set('articles', $this->Articles->find('all'));
	}
	
	public function view($id = null){
		$this->set('article', $this->Articles->get($id));
    }
	*/
	
	public function add(){
		$article = $this->Articles->newEntity();
		if ($this->request->is('post')) {
			$article = $this->Articles->patchEntity($article, $this->request->data);
			if ($this->Articles->save($article)) {
				$this->Flash->success(__('Your article has been saved.'));
				return $this->redirect(['action' => 'index']);
			}
			$this->Flash->error(__('Unable to add your article.'));
		}
		$this->set('article', $article);
	}
	
}

?>