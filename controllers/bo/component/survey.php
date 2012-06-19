<?php
/**
 * Copyright (c) 2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_Survey extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		$this->Survey = $this->initializeSurvey();
		
		
		/**
		 * Display Detail
		 */
		 
		if (is_numeric($this->GET['id'])) $this->displaySurvey($this->GET['id']);
		

		/**
		 * destroy
		 */
		 
		$this->Survey = false;
		
		return true;
	}
	
	/**
	 * initialize survey
	 */
	 
	public function initializeSurvey() {
		
		require_once('models/education/education_survey.php');
		$Survey = new education_survey();
		
		return $Survey;
	}
	
	/**
	 * initialize question
	 */
	 
	public function initializeQuestion() {
		
		require_once('models/education/education_survey_question.php');
		$Question = new education_survey_question();
		
		return $Question;
	}
	
	/**
	 * initialize questionAnswer
	 */
	 
	public function initializeQuestionAnswer() {
		
		require_once('models/education/education_survey_question_answer.php');
		$QuestionAnswer = new education_survey_question_answer();
		
		return $QuestionAnswer;
	}

	/**
	 * save survey
	 */
	 
	public function saveSurvey($survey_data) {
		
		if (!is_array($survey_data)) return false;
	
		if ($this->Survey->saveSurvey($survey_data)) msg("Survey id={$survey_data['id']} saved");
		else msg("Survey id={$survey_data['id']} save failed", 'error');

	}
	
	/**
	 * save question
	 */
	 
	public function saveQuestion($question_data) {
		
		if (!is_array($question_data)) return false;
	
		if ($this->Question->saveQuestion($question_data)) msg("Question id={$question_data['id']} saved");
		else msg("Question id={$question_data['id']} save failed", 'error');

	}
	
	/**
	 * save question
	 */
	 
	public function saveQuestionAnswer($question_answer_data) {
		
		if (!is_array($question_answer_data)) return false;
	
		if (!is_numeric($question_answer_data['is_correct'])) $question_answer_data['is_correct'] = 0;
		
		if ($this->QuestionAnswer->saveAnswer($question_answer_data)) msg("Answer id={$question_answer_data['id']} saved");
		else msg("Answer id={$question_answer_data['id']} save failed", 'error');

	}
	
	/**
	 * display survey
	 */
	
	public function displaySurvey($id) {
	
		if (!is_numeric($id)) return false;
	 
		$survey_detail = $this->Survey->getDetail($id);

		if (count($survey_detail) > 0) {
			
			$this->tpl->assign("SELECTED_{$survey_detail['publish']}", "selected='selected'");
			
			$this->tpl->assign('SURVEY', $survey_detail);
		}
		
	}
	
	/**
	 * display question
	 */
	
	public function displayQuestion($id) {
	
		if (!is_numeric($id)) return false;
	 
		$question_detail = $this->Question->getDetail($id);

		if (count($question_detail) > 0) {
			
			$this->tpl->assign("SELECTED_{$question_detail['publish']}", "selected='selected'");
			$this->tpl->assign("SELECTED_{$question_detail['type']}", "selected='selected'");
			$this->tpl->assign("CHECKED_mandatory_{$question_detail['mandatory']}", "checked='checked'");
			
			$this->tpl->assign('QUESTION', $question_detail);
		}
		
	}
	
	/**
	 * display questionAnswer
	 */
	
	public function displayQuestionAnswer($id) {
	
		if (!is_numeric($id)) return false;
	 
		$question_answer_detail = $this->QuestionAnswer->getDetail($id);

		if (count($question_answer_detail) > 0) {
			
			$this->tpl->assign("SELECTED_{$question_answer_detail['publish']}", "selected='selected'");
			$this->tpl->assign("SELECTED_{$question_answer_detail['type']}", "selected='selected'");
			
			if ($question_answer_detail['is_correct']) $this->tpl->assign('CHECKED_is_correct', 'checked="checked"');
			
			$this->tpl->assign('ANSWER', $question_answer_detail);
		}
		
	}
}

