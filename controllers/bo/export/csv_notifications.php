<?php
/** 
 * Copyright (c) 2011-2012 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */
 
require_once('controllers/bo/export/csv.php');

class Onxshop_Controller_Bo_Export_CSV_Notifications extends Onxshop_Controller_Bo_Export_CSV {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		set_time_limit(0);
		
		require_once('models/common/common_watchdog.php');
		
		$Watchdog = new common_watchdog();
		
		/**
		 * Get the list
		 */
		
		$date_from = $this->GET['date_from'];
		$date_to = $this->GET['date_to'];

		$records = $Watchdog->getDataForReport($date_from, $date_to);
		
		$this->commonCSVAction($records, 'notifications');

		return true;
	}
}
