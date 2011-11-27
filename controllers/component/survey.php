<?php
/** 
 * Copyright (c) 2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Survey extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		if (is_numeric($this->GET['survey_id'])) $survey_id = $this->GET['survey_id'];
		else {
			msg("Survey ID is not numeric", 'error');
			return false;
		}
		
		/**
		 * initialise
		 */
		 
		require_once('models/education/education_survey.php');
		$this->Survey = new education_survey();
		
		
		/**
		 * get survey detail
		 */
		 
		$survey_detail = $this->Survey->getFullDetail($survey_id);
		
		/**
		 * Save on request
		 */
		
		if ($_POST['save'] && is_array($_POST['answer'])) {
			
			$submitted_answers = $_POST['answer'];
			//try to save and if not successfull display survey form again
			if ($survey_entry_id = $this->saveEntry($survey_id, $submitted_answers)) {
				msg("Survey ID has been $survey_id submitted as entry ID $survey_entry_id.");
				onxshopGoTo($_SESSION['referer'], 2);
				/*$_nSite = new nSite("component/survey_result~survey_id=$survey_id~");
				$this->tpl->assign('SURVEY_RESULT', $_nSite->getContent());
				$this->tpl->parse('content.result');*/
			} else {
				msg("Some error occurred during survey submission", 'error');
				$this->displaySurvey($survey_detail, $submitted_answers);
			}
		} else {
		
			//display survey form
			$this->displaySurvey($survey_detail);
		
		}
		
		
		return true;
		
	}
	
	/**
	 * displaySurvey
	 */
	 
	public function displaySurvey($survey_detail, $submitted_answers = false) {
		
		if (!is_array($survey_detail)) {
			msg("Survey detail isn't array", 'error');
			return false;
		}
		
		foreach ($survey_detail['question_list'] as $item) {
			
			/**
			 * find what answer was submitted
			 */
			 
			if (is_array($submitted_answers)) $selected_value = $submitted_answers[$item['id']];
			else $selected_value = false;
			
			$this->displayQuestion($item, $selected_value);
			
		}
		
		$this->tpl->assign('SURVEY', $survey_detail);
	
		$this->tpl->parse('content.form');
		
	}

	/**
	 * displayQuestion
	 */
	 
	public function displayQuestion($question_detail, $selected_value = false) {
	
		if (!is_array($question_detail)) {
			msg("Question detail isn't array", 'error');
			return false;
		}

		$this->tpl->assign('QUESTION', $question_detail);
		
		switch ($question_detail['type']) {
			
			case 'text':
				if ($selected_value) $this->tpl->assign('SELECTED_VALUE', $selected_value);
				else  $this->tpl->assign('SELECTED_VALUE', '');
				$this->tpl->parse('content.form.question.answer_text');
			break;
			
			case 'radio':
				foreach ($question_detail['answer_list'] as $item) {
					if ($selected_value) {
						if ($item['id'] == $selected_value) $this->tpl->assign('SELECTED', 'checked="checked"');
						else $this->tpl->assign('SELECTED', '');
					} else {
						$this->tpl->assign('SELECTED', '');
					}
					$this->tpl->assign('ANSWER', $item);
					$this->tpl->parse('content.form.question.answer_list_radio.item');
				}
				$this->tpl->parse('content.form.question.answer_list_radio');
			break;

			case 'select':
			default:
				foreach ($question_detail['answer_list'] as $item) {
					if ($selected_value) {
						if ($item['id'] == $selected_value) $this->tpl->assign('SELECTED', 'selected="selected"');
						else $this->tpl->assign('SELECTED', '');
					} else {
						$this->tpl->assign('SELECTED', '');
					}
					$this->tpl->assign('ANSWER', $item);
					$this->tpl->parse('content.form.question.answer_list_select.item');	
				}		
				$this->tpl->parse('content.form.question.answer_list_select');
			break;
		}
		
		$this->tpl->parse('content.form.question');
		
	}

	/**
	 * prepare survey entry
	 */
	 
	public function prepareSurveyEntry($survey_id, $answers) {
	
		if (!is_numeric($survey_id)) return false;
		
		if (!is_array($answers)) {
			msg("Answers isn't array", 'error');
			return false;
		}
		
		$survey_entry = array();
		$survey_entry['survey_id'] = $survey_id;
		$survey_entry['customer_id'] = $_SESSION['client']['customer']['id'];
		//if GET params provided, use as relation_subject, othewise leave null (undefined)
		if ($relation_subject = $this->getRelationSubject()) $survey_entry['relation_subject'] = $relation_subject;
		$survey_entry['answers'] = array();
				
		require_once('models/education/education_survey_question.php');
		$Question = new education_survey_question();
		
		foreach ($answers as $question_id=>$answer_value) {
				
			if ($question_detail = $Question->getDetail($question_id)) {
				
				$answer = array();
				
				$answer['question_id'] = $question_id;
				
				/**
				 * for text type save as value
				 */
				 
				if ($question_detail['type'] == 'text') {
					$answer['value'] = $answer_value;
				} else {
					$answer['question_answer_id'] = $answer_value;
				}
				
				$survey_entry['answers'][] = $answer;
				
			}
				
		}
		
		return $survey_entry;
	}
	
	/**
	 * saveEntry
	 */
	
	public function saveEntry($survey_id, $answers) {
		
		if (!is_array($answers)) {
			msg("saveEntry data isn't array", 'error');
			return false;
		}
		
		if ($survey_entry_data = $this->prepareSurveyEntry($survey_id, $answers)) {
			require_once('models/education/education_survey_entry.php');
			$SurveyEntry = new education_survey_entry();
		
			return $SurveyEntry->saveEntryFull($survey_entry_data);
		
		} else {
		
			return false;
		}
		
	}
	
	/**
	 * get relation subject
	 * for SQL LIKE
	 */
	 
	public function getRelationSubject() {
	
		/**
		 * find params
		 */
		 
		/*
		if (preg_match("/\?/", $_SERVER['REQUEST_URI'])) $params = preg_replace("/[^\?]*\?/", "", $_SERVER['REQUEST_URI']);
		else $params = false;
		
		if ($params == '') $params = false;
		*/
		
		/**
		 * for STAG
		 */
		require_once('conf/stag.php');
		
		$params['rok'] = STAG_ROK;
		$params['semestr'] = STAG_SEMESTR;
		$params['fakulta'] = STAG_DEFAULT_FACULCY;
		if ($this->GET['katedra']) $params['katedra'] = $this->GET['katedra'];
		else $params['katedra'] = '%';
		if ($this->GET['zkratka']) $params['zkratka'] = $this->GET['zkratka'];
		else $params['zkratka'] = '%';
		if ($this->GET['ucitIdno']) $params['ucitIdno'] = $this->GET['ucitIdno'];
		else $params['ucitIdno'] = '%';
		if ($this->GET['ucitel_fullname']) $params['ucitel_fullname'] = $this->GET['ucitel_fullname'];
		else $params['ucitel_fullname'] = '%';
		
		$params = json_encode($params);
		
		return $params;
		
	}
}
