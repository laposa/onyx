<?php
/** 
 * Copyright (c) 2011-2012 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */
 
require_once('controllers/bo/export/csv.php');

class Onxshop_Controller_Bo_Export_CSV_Survey_Results extends Onxshop_Controller_Bo_Export_CSV {

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
		
		$survey_id = (int) $this->GET['survey_id'];
		$detail = $Survey->getFullDetail($survey_id);
		if (!$detail) return false;

		$records = array();
		foreach ($detail['question_list'] as $question) {
			$item = array(
				'question_id' => $question['id'],
				'question_title' => $question['title'],
				'question_type' => $question['type']
			);
			foreach ($question['answer_list'] as $answer) {
				$item['answer_id'] = $answer['id'];
				$item['answer_title'] = $answer['title'];
				$item['answer_votes'] = (int) $Survey->getAnswerUsage($answer['id']);
				$records[] = $item;
			}
		}

		if (is_array($records)) {
		
				/**
				 * parse records
				 */
				$header = 0;
				
				foreach ($records as $record) {
					
					/**
					 * Create header
					 */
					if ($header == 0) {
					
						foreach ($record as $key=>$val) {
					
							$column['name'] = $key;
					
							$this->tpl->assign('COLUMN', $column);
							$this->tpl->parse('content.th');
						}
						$header = 1;
					}
		        
					foreach ($record as $key=>$val) {
					
						if (!is_numeric($val)) {
					
							$val = addslashes($val);
							$val = '"' . $val . '"';
							$val = preg_replace("/[\n\r]/", '', $val);
						}

						$this->tpl->assign('value', $val);
						$this->tpl->parse('content.item.attribute');
					}
			
					$this->tpl->parse('content.item');
				}
		
			
			//set the headers for the output
			// $this->sendCSVHeaders('surveys');
			
		} else {
			
			echo "no records"; exit;
		
		}

		return true;
	}
}
