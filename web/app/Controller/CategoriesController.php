<?php

class CategoriesController extends AppController {

	var $uses = array('Category');
	
	public function index() {
		$this->enablePageClass('basic-edit');
		$this->setAdditionalCssFiles(array('basic-edit'));
		$this->set('categories', $this->Category->getAll());
	}
	
	public function edit($id=0) {
		$this->enablePageClass('basic-edit');
		$this->setAdditionalCssFiles(array('basic-edit'));
		$this->set('category', $this->Category->getOne($id));
		
		$isEdit = true;
		if ($this->request->is('post')) {
			$this->Category->saveCategory($this->request->data['id'], $this->request->data['name'], $this->request->data['description'], $this->request->data['icon']);
		}
		else $isEdit = false;
		
		if ($isEdit) {
			if (isset($this->request->data['apply'])) {
				$this->redirect(array("controller" => "categories", "action" => "edit", $this->Category->id, $this->request->data['name']));
			}
			else {
				return $this->redirect(array('action' => 'index'));
			}
		}
	}
	
	public function view($id) {
		$this->set('category', $this->Category->getOne($id));
	}
	
	public function delete($id) {
		$this->Category->delete((int)$id);
		return $this->redirect(array('action' => 'index'));
	}
	
}
