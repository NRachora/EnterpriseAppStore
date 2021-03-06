<?php

App::uses('Platforms', 'Lib/Platform');
App::uses('ApplicationsDataHelper', 'Lib/Data/Helpers');


class ApplicationsController extends AppController {
	
	var $uses = array('Application', 'Category', 'Group', 'Attachment', 'History', 'Download');
	
	public function isAuthorized($user) {
	    $ok = false;
	    if (Me::minUser()) {
	    	$a = strtolower($this->params['action']);
	    	if ($a == 'delete' || $a == 'deleteall' || $a == 'edit' || $a == 'uploadapp') {
	        	$ok = Me::minAdmin();
	        }
	        else {
		        return true;
	        }
	    }
		if (!$ok) {
			Error::add('You are not authorized to access this section.', Error::TypeError);
		}
		return $ok;
	}
	
	public function beforeFilter() {
		parent::beforeFilter();
		if (isset($this->request->query['key']) && $this->Apikey->isKeyValid($this->request->query['key']) && !Me::id()) {
			$this->Auth->allow('distributionplist', 'uploadApp');
		}
		else {
			$this->Auth->allow('distributionplist');
		}
	}
	
	public function distributionplist($id) {
		$this->layout = 'ajax';
		$this->response->header(array('Content-type: text/plain'));
		$app = $this->Application->getOne($id);
		$this->set('app', $app);
		$this->set('largeIcon', Storage::urlForIconForAppWithId($app['Application']['id'], $app['Application']['location']));
		$this->set('smallIcon', Storage::urlForIconForAppWithId($app['Application']['id'], $app['Application']['location']));
		
		// TODO: Add shine effect for the app to the admin panel
		$this->set('needsShine', false);
	}
	
	public function download($id, $name) {
		$app = $this->Application->getOne($id);
		if ($app['Application']['id']) {
			$this->Download->saveDownload($app['Application']['id']);
			if ($name == 'install') {
				$this->History->saveHistory($id, 'INS');
			}
			else {
				$this->History->saveHistory($id, 'DWN');
			}
			// TODO: Enable S3 support
			$ext = ($app['Application']['platform'] <= 2) ? 'ipa' : 'apk';
			$path = 'Userfiles'.DS.'Applications'.DS.$app['Application']['id'].DS.'app.'.$ext;
			$options = array('download' => true);
			$options['name'] = TextHelper::safeText($app['Application']['name']).'.'.$ext;
			if ($app['Application']['platform'] <= 2) {
				$this->response->type(array('ipa' => 'application/octet-stream'));
			}
			else {
		    	$this->response->type(array('apk' => 'application/vnd.android.package-archive'));
		    }
		    $this->response->file($path, $options);
		    return $this->response;
		}
		else {
			return $this->redirect(array('action' => 'index'));
		}
	}
	
	public function install($id) {
		return $this->download($id, 'install');
	}
	
	public function delete($id) {
		if ($this->Application->deleteApp($id)) {
			Error::add('Application has been deleted.', Error::TypeOk);
		}
		else {
			Error::add('Application can not be deleted.', Error::TypeError);
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	public function deleteAll($id) {
		if ($this->Application->deleteAllApps($id)) {
			Error::add('All applications have been deleted.', Error::TypeOk);
		}
		else {
			Error::add('Application can not be deleted.', Error::TypeError);
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	public function index() {
		$this->setPageIcon('puzzle-piece');
		$this->enablePageClass('basic-edit');
		$this->setAdditionalCssFiles(array('basic-edit'));
		$this->setAdditionalJavascriptFiles(array('application-list'));
		
		$groupIds = Me::groupIds();
		if ($this->request->is('post')) {
			$this->set('searchTerm', $this->request->data['search']);
			$data = $this->Application->searchFor($this->request->data['search'], $groupIds);
		}
		else {
			$data = $this->Application->getAll($groupIds);
		}
		$this->set('apps', $data);
	}
	
	public function view($id) {
		// Basic template settings
		$this->setPageIcon('puzzle-piece');
		$this->enablePageClass('basic-edit');
		$this->setAdditionalCssFiles(array('basic-edit'));
		
		// Application detail
		$app = $this->Application->getOne($id);
		$this->set('data', $app);
		
		// Starting basic info
		$basicInfo = ApplicationsDataHelper::prepareBasicInfoForApp($app);
		
		// Saving view to the history
		$this->History->saveHistory($id, 'VEW');
		
		// Parsing system files
		$platform = $app['Application']['platform'];
		
		App::uses('InfoPlistTemplateParser', 'Lib/Parsing');
		$data = json_decode($app['Application']['config'], true);
		if ($platform <= Platforms::iOSUniversal) {
			// iOS
			$parser = new InfoPlistTemplateParser();
			$parsed = $parser->processArray($data['plist']);
			$this->set('appSystemInfo', $parsed);
			
			$basicInfo = ApplicationsDataHelper::prepareBasicInfoForApple($data, $platform, $basicInfo);
		}
		else {
			// Android
			$basicInfo = ApplicationsDataHelper::prepareBasicInfoForAndroid($data, $platform, $basicInfo);
			
			$parsed = array();
			if (isset($data['screen-sizes'])) {
				if (isset($data['screen-sizes']['resizeable'])) {
					$parsed['Resizeable'] = $data['screen-sizes']['resizeable'] ? 'Yes' : 'No';
					unset($data['screen-sizes']['resizeable']);
				}
				foreach ($data['screen-sizes'] as $k=>$v) {
					$k = ucfirst(preg_replace('/screens/si', ' screen', $k));
					$parsed[$k] = $v ? 'Yes' : 'No';
				}
			}
			if (isset($data['permissions'])) {
				$s = '';
				foreach ($data['permissions'] as $k=>$v) {
					$s .= implode(' ', explode('_', ucfirst($v))).'<br />';
				}
				$parsed['Permissions'] = $s;
			}
			$this->set('appSystemInfo', $parsed);
		}
		
		// Setting basic info
		$this->set('basicInfo', $basicInfo);
		
		// History
		$groupIds = Me::groupIds();
		$apps = $this->Application->getAllHistoryForApp($app['Application']['identifier'], $app['Application']['platform'], $groupIds);
		$this->set('appsList', $apps);
		
		// Attachments
		$this->set('attachmentsList', $this->Attachment->getAllForApp($app));
	}
	
	public function edit($id=0) {
		$this->setPageIcon('puzzle-piece');
		$this->enablePageClass('basic-edit');
		$this->setAdditionalCssFiles(array('basic-edit'));
		$this->setAdditionalJavascriptFiles(array('applications-edit'));
		
		$this->enableAjaxFileUpload();
		
		// Checking for Id
		if (isset($this->request->data['appId'])) $ajaxId = (int)$this->request->data['appId'];
		if (isset($ajaxId) && (bool)$ajaxId) $id = $ajaxId;
		if ($id == 'new') {
			$id = 0;
		}
		else $id = (int)$id;
		
		// Groups for the join subset
		$list = $this->Application->Group->find('list');
		$this->set('groups', $list);
		
		// Users for the join subset
		$list = $this->Application->Group->find('all');
		$this->set('groupsList', $list);
		
		// Applications for the join subset
		$list = $this->Application->Category->find('list');
		$this->set('categories', $list);
		
		// Applications for the join subset
		$list = $this->Application->Category->find('all');
		$this->set('categoriesList', $list);
		
		if (empty($this->request->data)) {
			// Getting data
        	$this->request->data = $this->Application->findById($id);
		}
		else {
			// Saving data
			if (!$id) {
				$this->Application->create();
			}
			else {
				$this->Application->id = $id;
			}
			$appData = $this->request->data;
			$appData['form'] = $this->request->form;
			$ok = $this->Application->saveApp($appData, $this->request->data['formData'], null, null);
			if ($ok) Error::add('App has been successfully saved.');
			else {
				Error::add('Unable to save this app.', Error::TypeError);
				return false;
			}
			if (isset($this->request->data['apply'])) {
				// Redirecting for the same page (Apply)
				$this->redirect(array('controller' => 'applications', 'action' => 'edit', $this->Application->id, TextHelper::safeText($this->request->data['Application']['name'])));
			}
			else {
				// Redirecting to the index
				$this->redirect(array('controller' => 'applications', 'action' => 'view', $this->Application->id, TextHelper::safeText($this->request->data['Application']['name'])));
			}
		}
		
		if (isset($this->request->data['Application']['platform'])) {
			if ($this->request->data['Application']['platform'] <= 7) {
				$appType = 0;
			}
			else if ($this->request->data['Application']['platform'] == 8) {
				$appType = 1;
			}
			else if ($this->request->data['Application']['platform'] == 9) {
				$appType = 2;
			}
		}
		else $appType = 0;
		$this->set('appType', $appType);
		
		// Selected groups
		$arr = array();
		if (isset($this->request->data['Group'])) foreach ($this->request->data['Group'] as $group) {
			$arr[$group['id']] = 1;
		}
		$this->set('selectedGroups', $arr);
		
		// Selected categories
		$arr = array();
		if (isset($this->request->data['Category'])) foreach ($this->request->data['Category'] as $category) {
			$arr[$category['id']] = 1;
		}
		$this->set('selectedCategories', $arr);
		
		$app = $this->Application->getOne($id);
		$this->set('attachmentsList', $this->Attachment->getAllForApp($app));
	}
	
	public function uploadApp() {
		App::uses('ExtractAndroid', 'Lib/AppExtraction');
		App::uses('ExtractApple', 'Lib/AppExtraction');
		
		$file = $this->request->form['appFile'];
		
		$extract = null;
		$errors = null;
		
		$debug = false; // 'i' for iPhone & 'a' for Android or false to disable
		
		if ($debug) {
			if ($debug == 'i') {
				$file['name'] = 'iJenkins_Enterprise.ipa';
				$file['type'] = 'application/octet-stream';
				$file['tmp_name'] = 'debug';
				$file['path'] = APP.DS.'Dummy'.DS.'iJenkins_Enterprise.ipa';
				$file['size'] = 1234124;
				$file['error'] = null;
			}
			elseif ($debug == 'i2') {
				$file['name'] = 'iDeviant_Enterprise.ipa';
				$file['type'] = 'application/octet-stream';
				$file['tmp_name'] = 'debug';
				$file['path'] = APP.DS.'Dummy'.DS.'iDeviant_Enterprise.ipa';
				$file['size'] = 1234124;
				$file['error'] = null;
			}
			elseif ($debug == 'g') {
				$file['name'] = 'Garden.ipa';
				$file['type'] = 'application/octet-stream';
				$file['tmp_name'] = 'debug';
				$file['path'] = APP.DS.'Dummy'.DS.'Garden.ipa';
				$file['size'] = 1234124;
				$file['error'] = null;
			}
			elseif ($debug == 'a') {
				$file['name'] = '145.apk';
				$file['type'] = 'application/octet-stream';
				$file['tmp_name'] = 'debug';
				$file['path'] = APP.DS.'Dummy'.DS.'145.apk';
				$file['size'] = 1234124;
				$file['error'] = null;
			}
			elseif ($debug == 'a2') {
				$file['name'] = '226.apk';
				$file['type'] = 'application/octet-stream';
				$file['tmp_name'] = 'debug';
				$file['path'] = APP.DS.'Dummy'.DS.'226.apk';
				$file['size'] = 1234124;
				$file['error'] = null;
			}
		}
		
		if ($file) {
			$extract = new ExtractApple($file);
			if ($extract->is()) {
				
			}
			else {
				$extract = new ExtractAndroid($file);
				if ($extract->is()) {
					
				}
				else {
					$extract = null;
					// TODO: Error message goes here!
				}
			}
			if ($extract) {
				if ($extract->process()) {
					$app = $this->Application->saveApp(array('Application'=>$extract->data), $extract->data, $extract->app, $extract->icon);
					if (!$app) {
						$errors = array('Unable to process the app');
						$extract = null;
					}
					else {
						$extract->data['id'] = (int)$this->Application->getLastInsertId();
						if ((bool)$extract->data['id']) {
							$this->History->saveHistory($extract->data['id'], 'UPL');
						}
						$extract->clean();
						$errors = $extract->errors;
					}
				}
			}
			else {
				$errors = array('Unable to process the app');
			}
		}
		else {
			$errors = array('No file has been processed');
		}
		
		$data = null;
		if ($extract != null) {
			$data = $extract->data;
		}
		if (!$data) {
			if (!$errors) {
				$errors = array('No file has been processed');
			}
		}
		
		$this->outputApi($data, false, $errors);
	}
	
}
