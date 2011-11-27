<?php
/**
 * Copyright (c) 2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/survey.php');

class Onxshop_Controller_Bo_Component_Survey_Detail extends Onxshop_Controller_Bo_Component_Survey {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		if (is_numeric($this->GET['id'])) $survey_id = $this->GET['id'];
		else {
			msg("Survey ID is not numeric", 'error');
			return false;
		}
		
		require_once('models/education/education_survey.php');
		
		$Survey = new education_survey();
		
		$survey_detail = $Survey->getFullDetail($survey_id);
		
		$this->displaySurvey($survey_detail);
		
		return true;
		
	}
	
	/**
	 * displaySurvey
	 */
	 
	public function displaySurvey($survey_detail) {
		
		if (!is_array($survey_detail)) {
			msg("Survey detail isn't array", 'error');
			return false;
		}
		
		foreach ($survey_detail['question_list'] as $item) {
			
			$this->displayQuestion($item);
			
		}
		
		$this->tpl->assign('SURVEY', $survey_detail);
	
	}

	/**
	 * displayQuestion
	 */
	 
	public function displayQuestion($question_detail) {
	
		if (!is_array($question_detail)) {
			msg("Question detail isn't array", 'error');
			return false;
		}
		
		$this->tpl->assign('QUESTION', $question_detail);
		
		if ($question_detail['type'] == 'text') {
		
			$this->tpl->parse('content.question.answer_text');
		
		} else {
			
			foreach ($question_detail['answer_list'] as $item) {
				
				$this->displayAnswer($item);
				
			}
			
			$this->tpl->parse('content.question.answer_list');
		}
		
		$this->tpl->parse('content.question');
		
	}
	
	
	/**
	 * displayAnswer
	 */
	 
	public function displayAnswer($answer_detail) {
		
		if (!is_array($answer_detail)) {
			msg("Answer detail isn't array", 'error');
			return false;
		}
		
		$this->tpl->assign('ANSWER', $answer_detail);
		$this->tpl->parse('content.question.answer_list.item');
	}
}

