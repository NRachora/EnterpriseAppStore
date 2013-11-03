<?php

App::uses('AppController', 'Controller');

class PagesController extends AppController {

	public $uses = array('Download', 'Application', 'Settings');

	public function display() {
		$this->setPageIcon('dashboard');
		
		$path = func_get_args();
		
		$this->enableWoodWrapper();
		$this->setAdditionalCssFiles(array('dashboard'));
		$this->setAdditionalJavascriptFiles(array('dashboard'));
		
		$this->set('averageDownloads', $this->Download->averageNumberOfDownloadsPerNumberOfDays());
		$this->set('downloadsYesterday', $this->Download->numberOfDownloadsYesterday());
		$this->set('thirtyDayDownloads', $this->Download->countAllForLastNumberOfDays(2));
		$this->set('allDownloads', $this->Download->countAll());
		
		$appsPerPlatform = array();
		$appsPerPlatform['iOS'] = $this->Application->countAppsForPlatforms(array(0, 1, 2));
		$appsPerPlatform['Android'] = $this->Application->countAppsForPlatforms(array(3, 4, 5));
		$appsPerPlatform['Windows8'] = $this->Application->countAppsForPlatforms(array(6, 7));
		$appsPerPlatform['WebClip'] = $this->Application->countAppsForPlatforms(array(8));
		$this->set('appsPerPlatform', $appsPerPlatform);

		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));

		try {
			$this->render(implode('/', $path));
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
	}
	
}
