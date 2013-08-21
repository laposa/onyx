<?php
/** 
 * Copyright (c) 2011-2012 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */
 
require_once('controllers/bo/export/csv.php');

class Onxshop_Controller_Bo_Export_CSV_Surveys extends Onxshop_Controller_Bo_Export_CSV {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		set_time_limit(0);
		
		require_once('models/education/education_survey.php');
		
		$Survey = new education_survey();
		
		/**
		 * Get the list
		 */
		
		$records = $Survey->getSurveyListStats();
		
		$this->commonCSVAction($records, 'surveys');

		return true;
	}
}
