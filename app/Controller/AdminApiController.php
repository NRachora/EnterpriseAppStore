<?php

class AdminApiController extends AppController {

	var $uses = array('Download', 'Application');
	
	public function isAuthorized($user) {
	    if (Me::minUser()) {
	        return true;
	    }
		else {
			Error::add('You are not authorized to access this section.', Error::TypeError);
			return false;
		}
	}
	
	/* Add slash (/) to uncomment this method
	// This method is designed for testing only, do not ever leave uncommented on a production server
	public function generateDummyAppsAndDownloads() {
		$x = 0;
		for ($p = 0; $p <= 5; $p++) {
			for ($d = 0; $d <= 24; $d++) {
				$rand = rand(5, 25);
				for ($r = 0; $r <= $rand; $r++) {
					$this->Download->saveDownload(1, (time() - $this->Download->daysToSeconds($d)));
					$x++;
				}
			}
		}
		die('Generated: '.$x);
	}
	//*/
	
	public function calendarData($days=15) {
		$data = array();
		$apps = $this->Application->getAllForCalendar(Me::groupIds());
		foreach ($apps as $app) {
			$time = strtotime($app['Application']['created']);
			$data[] = array(
				'title' => $app['Application']['name'].' ('.$app['Application']['version'].')',
				'start'=> $time,
				'end'=> ($time + (60 * 10)),
				'url'=>Router::url(array(
					'controller'=>'applications',
					'action'=>'view',
					(int)$app['Application']['id'],
					TextHelper::safeText($app['Application']['name'])
				)
			));
		}
		$this->outputApi($data, false);
	}
	
	public function platformDownloads($days=15) {
		$data = $this->Download->dataForChartForLastNumberOfDaysWithInfo(12);
		$this->outputApi($data, false);
	}
	
}
