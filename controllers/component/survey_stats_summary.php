<?php
/** 
 * Copyright (c) 2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Survey_Stats_Summary extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('conf/stag.php');
		$student_count = $this->getPocetStudentu();
		
		$this->tpl->assign("STUDENT_COUNT", $student_count);
		
		require_once('models/education/education_survey_entry.php');
		$SurveyEntry = new education_survey_entry();
		
		$survey_customer_count = $SurveyEntry->getSurveyCustomerCount();
		$average_rating = $SurveyEntry->getAverageRating();
		
		$this->tpl->assign("SURVEY_CUSTOMER_COUNT", $survey_customer_count);
		
		$this->tpl->assign("AVERAGE_RATING", $average_rating);

		return true;
		
	}
	
	
	/**
	 * getStudentiByFakulta
	 */
	 
	public function getStudentiByFakulta($fakulta = STAG_DEFAULT_FACULCY, $semestr = STAG_SEMESTR, $rok = STAG_ROK) {
		
		if (!preg_match("/[A-Z]{3}/", $fakulta)) return false;
				
		require_once('conf/stag.php');
		require_once('Zend/Rest/Client.php');
		
		$url = STAG_URL. "/student/getStudentiByFakulta?fakulta={$fakulta}&semestr={$semestr}&rok={$rok}";
		
		$client = new Zend_Rest_Client($url);
		$result = $client->get();

		if (!is_object($result)) return false;
		
		$list = array();
		
		foreach ($result->studenti->student as $item) {
			
			$list[] = (array)$item;
			
		}
		
		return (array)$list;
		
	}
	
	/**
	 * getPocetStudentu
	 */
	 
	public function getPocetStudentu() {
		
		$student_list = $this->getStudentiByFakulta();
		
		if (is_array($student_list)) return count($student_list);
		else return 'n/a';
		
	}

}
