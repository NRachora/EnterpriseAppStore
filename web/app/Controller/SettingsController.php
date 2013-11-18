<?php

class SettingsController extends AppController {
	
	var $uses = array('Settings');
	
	public function index() {
		$this->setPageIcon('gear');
		if ($this->request->is('post')) {
			$this->Settings->saveSettings($this->request->data['settings']);
			if (isset($this->request->form['file'])) {
				$this->Settings->saveFiles($this->request->form['file']);
			}
			Error::add('Settings has been successfully updated.');
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('settings', $this->Settings->settings());
	}
		
}
