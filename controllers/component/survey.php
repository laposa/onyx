<?php
/** 
 * Copyright (c) 2011-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('models/education/education_survey.php');
require_once('models/education/education_survey_entry.php');
require_once('models/client/client_action.php');

class Onxshop_Controller_Component_Survey extends Onxshop_Controller {

	const TAXONOMY_TREE_PROVINCE_ID = 53;

	/**
	 * main action
	 */
	 
	public function mainAction() {

		/**
		 * input
		 */
		 
		if (is_numeric($this->GET['survey_id'])) $survey_id = $this->GET['survey_id'];
		else {
			msg("Survey ID is not numeric", 'error');
			return false;
		}

		/**
		 * initialise
		 */
		 
		$this->Survey = new education_survey();
		$this->Entry = new education_survey_entry();
		$this->Entry->setCacheable(false);

		/**
		 * get survey detail
		 */
		 
		$survey_detail = $this->Survey->getFullDetail($survey_id);

		if ($survey_detail['publish'] == 1) {

			if ($this->hasCustomerVoted($survey_id)) {

				/**
				 * display results when voted already
				 */
				 
				$this->displayResult($survey_id);

			} else {

				/**
				 * Save on request
				 */
				
				if ($this->checkVoteEligibility($survey_id) && $_POST['save'] && is_array($_POST['answer'])) {

					$survey_entry_id = $this->processAndSaveForm($survey_id);

					if ($survey_entry_id) {
					
						msg("Survey ID {$survey_detail['id']} has been submitted as entry ID $survey_entry_id.", 'ok', 1, 'survey_submitted');
						
						$this->createFacebookStory();

						if ($this->GET['href']) $this->displaySuccessPage($this->GET['href']);
						else $this->displayResult($survey_id, $survey_entry_id);

					}
					
				} else {
				
					$this->displaySurvey($survey_detail);
				
				}

			}

		} else {

			// survey is unpublished
			$this->tpl->parse('content.closed');

		}
		
		return true;
		
	}
	
	/**
	 * displayResult
	 */
	 
	public function displayResult($survey_id, $survey_entry_id = false) {
		
		if (!is_numeric($survey_id)) return false;
		
		if (is_numeric($this->GET['display_results'])) {
			
			if ($this->GET['display_results'] == 1) $display_results = 1;
			else $display_results = 0;
			
		} else {
			
			$display_results = 1;
		
		}
		
		$_Onxshop_Request = new Onxshop_Request("component/survey_result~survey_id=$survey_id:display_results=$display_results~");
		$this->tpl->assign('SURVEY_RESULT', $_Onxshop_Request->getContent());
		$this->tpl->parse('content.result');
		
	}

	/**
	 * Forward to thank you page
	 */
	public function displaySuccessPage($href) {

		onxshopGoTo($href);

	}

	/**
	 * Process and save survey form
	 * @return int|FALSE
	 */
	public function processAndSaveForm($survey_id) {

		// check captcha
		$word = strtolower($_SESSION['captcha'][$this->GET['node_id']]);
		$isCaptchaValid = strlen($_POST['captcha']) > 0 && $_POST['captcha'] == $word;
		$captchaEnabled = ($this->GET['spam_protection'] == "captcha_text_js");

		if ($captchaEnabled && !$isCaptchaValid) {
			msg("Please enter correct code", 'error');
			return false;
		}

		$customer_id = (int) $_SESSION['client']['customer']['id'];

		if ($this->areUserDetailsRequired()) {

			$customer = $_POST['client']['customer'];

			if ($customer['first_name'] && $customer['last_name'] && $customer['email']) {

				$customer_id = $this->processCustomerDetails($customer);

			} else {

				msg("Invalid personal details entered.", 'error');
				return false;
			} 
		}

		$survey_entry_id = $this->saveEntry($survey_id, $_POST['answer'], $customer_id);

		if (!$survey_entry_id) {
			msg("An error occurred during survey submission", 'error');
			return false;
		}

		return $survey_entry_id;
	}

	/**
	 * processCustomerDetails
	 */
	 
	public function processCustomerDetails($form_data)
	{
		require_once 'models/client/client_customer.php';
		$Customer = new client_customer();
		$Customer->setCacheable(false);
		
		$customer_details = $Customer->getClientByEmail($form_data['email']);

		if (is_numeric($customer_details['id'])) {

			$customer_details['other_data'] = unserialize($customer_details['other_data']);

			// update only certain properties

			if (strlen($form_data['other_data']['city']) > 0) 
				$customer_details['other_data']['city'] = $form_data['other_data']['city'];

			if (strlen($form_data['other_data']['county']) > 0) 
				$customer_details['other_data']['county'] = $form_data['other_data']['county'];
				
			if (is_numeric($form_data['other_data']['home_store_id']) > 0) 
				$customer_details['other_data']['home_store_id'] = $form_data['other_data']['home_store_id'];

			if (strlen($form_data['telephone']) > 0) 
				$customer_details['telephone'] = $form_data['telephone'];

			if (strlen($form_data['birthday']) > 0) 
				$customer_details['birthday'] = $form_data['birthday'];

			$Customer->updatePreservedCustomer($customer_details);

			return $customer_details['id'];
		}

		return $Customer->insertPreservedCustomer($form_data);
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
			
			if ($item['publish'] == 1) $this->displayQuestion($item, $selected_value);
			
		}

		if ($this->GET['spam_protection'] == "captcha_text_js") {
			$this->tpl->parse("content.form.invisible_captcha_field");
		}

		if ($this->areUserDetailsRequired()) {
			
			/**
			 * decide whether to ask for nearest store or county/town
			 * TODO: allow to configure or use automatic selection based on data in ecommerce_store
			 */
			 
			if (1 == 1) $this->parseStoreSelect($_POST['client']['customer']['other_data']['county']);
			else $this->parseLocationSelect($_POST['client']['customer']['other_data']['county']);
			
			$this->tpl->parse("content.form.require_user_details");
		}

		$this->tpl->assign('SURVEY', $survey_detail);
		
		if ($this->GET['require_t_and_c'] == "1") $this->tpl->parse('content.form.require_t_and_c');
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
		
		/**
		 * if mandatory, than add 'required' CSS class
		 */
		 
		if ($question_detail['mandatory']) $this->tpl->assign('CLASS_REQUIRED', 'required');
		else $this->tpl->assign('CLASS_REQUIRED', '');
		
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
			
			case 'file':
				$this->tpl->parse('content.form.question.answer_file');
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
	 
	public function prepareSurveyEntry($survey_id, $answers, $customer_id) {
	
		if (!is_numeric($survey_id)) return false;
		
		if (!is_array($answers)) {
			msg("Answers isn't array", 'error');
			return false;
		}
		
		$survey_entry = array();
		$survey_entry['survey_id'] = $survey_id;
		$survey_entry['customer_id'] = $customer_id;
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
				 * for text and file type save as value
				 */
				 
				if ($question_detail['type'] == 'text' || $question_detail['type'] == 'file') {
				
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
	
	public function saveEntry($survey_id, $answers, $customer_id) {
		
		if (!is_array($answers)) {
			msg("saveEntry data isn't array", 'error');
			return false;
		}
		
		if ($survey_entry_data = $this->prepareSurveyEntry($survey_id, $answers, $customer_id)) {
		
			return $this->Entry->saveEntryFull($survey_entry_data);
		
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
		
		return false;
		
	}


	/**
	 * can customer vote? (terms of the limits)
	 */

	protected function checkVoteEligibility($survey_id)
	{
		$can_vote = true;

		// get parameters

		$limit = $this->GET['limit'];
		if (!in_array($limit, array('once_per_competition', 'once_per_day', 'num_per_day')))
			$limit = 'unlimited';

		$votes_per_day = (int) $this->GET['votes_per_day'];

		$restriction = $this->GET['restriction'];
		if (!in_array($restriction, array('to_customer', 'to_session', 'to_ip_address')))
			$restriction = 'none';

		switch ($limit) {
			case 'once_per_competition':
				$max_votes = 1;
				$justToday = false;
				break;
			
			case 'once_per_day':
				$max_votes = 1;
				$justToday = true;
				break;
			
			case 'num_per_day':
				$max_votes = $votes_per_day > 0 ? $votes_per_day : 1;
				$justToday = true;
				break;

		}

		if ($limit != 'unlimited') {

			switch ($restriction) {
				case 'to_ip_address':
					$num = $this->Entry->numEntriesForIpAddress($survey_id, $_SERVER['REMOTE_ADDR'], $justToday);
					break;

				case 'to_session':
					$num = $this->Entry->numEntriesForSessionId($survey_id, session_id(), $justToday);
					break;

				default:
			}

			$can_vote = ($num < $max_votes);
		}

		return $can_vote;
	}


	/**
	 * has customer voted already during active session?
	 */

	protected function hasCustomerVoted($survey_id)
	{
		$has_voted = false;

		// get parameters

		$limit = $this->GET['limit'];
		if (!in_array($limit, array('once_per_competition', 'once_per_day', 'num_per_day')))
			$limit = 'unlimited';

		$votes_per_day = (int) $this->GET['votes_per_day'];

		switch ($limit) {

			case 'once_per_competition':
				$max_votes = 1;
				$justToday = false;
				break;
			
			case 'once_per_day':
				$max_votes = 1;
				$justToday = true;
				break;
			
			case 'num_per_day':
				$max_votes = $votes_per_day > 0 ? $votes_per_day : 1;
				$justToday = true;
				break;

		}

		if ($limit != 'unlimited') {

			$num = $this->Entry->numEntriesForSessionId($survey_id, session_id(), $justToday);
			$has_voted = ($num >= $max_votes);
		}

		return $has_voted;
	}

	/**
	 * areUserDetailsRequired
	 */
	 
	public function areUserDetailsRequired()
	{
		$configuration_flag = ($this->GET['require_user_details'] == "1");
		$user_not_logged_in = !$_SESSION['client']['customer']['id'];
		return $configuration_flag && $user_not_logged_in;
	}
	
	/**
	 * parseLocationSelect
	 */

	protected function parseLocationSelect($selected_id)
	{
	
		$provinces = $this->getTaxonomyBranch(self::TAXONOMY_TREE_PROVINCE_ID);

		foreach ($provinces as $province) {

			$this->tpl->assign("PROVINCE_NAME", $province['label']['title']);

			$counties = $this->getTaxonomyBranch($province['id']);

			foreach ($counties as $county) {
				$county['selected'] = ($selected_id == $county['id'] ? 'selected="selected"' : '');
				$this->tpl->assign("COUNTY", $county);
				$this->tpl->parse("content.form.require_user_details.location.county_dropdown.province.county");
			}

			$this->tpl->parse("content.form.require_user_details.location.county_dropdown.province");

		}

		$this->tpl->parse("content.form.require_user_details.location.county_dropdown");
		$this->tpl->parse("content.form.require_user_details.location");

	}
	
	/**
	 * parseStoreSelect
	 */

	protected function parseStoreSelect($selected_id)
	{
		
		require_once('models/ecommerce/ecommerce_store.php');
		$Store = new ecommerce_store();
	
		$provinces = $this->getTaxonomyBranch(self::TAXONOMY_TREE_PROVINCE_ID);

		foreach ($provinces as $province) {

			$this->tpl->assign("PROVINCE_NAME", $province['label']['title']);

			$counties = $this->getTaxonomyBranch($province['id']);

			foreach ($counties as $county) {
				$county['selected'] = ($selected_id == $county['id'] ? 'selected="selected"' : '');
				$this->tpl->assign("COUNTY", $county);
				// get all stores in this count
				$store_list = $Store->getFilteredStoreList($county['id']);
				
				foreach ($store_list[0] as $store_item) {
					$this->tpl->assign('STORE', $store_item);
					$this->tpl->parse("content.form.require_user_details.store.county_dropdown.province.store");
				}
			}

			$this->tpl->parse("content.form.require_user_details.store.county_dropdown.province");

		}

		$this->tpl->parse("content.form.require_user_details.store.county_dropdown");
		$this->tpl->parse("content.form.require_user_details.store");

	}

	/**
	 * getTaxonomyBranch
	 */
	 
	public function getTaxonomyBranch($parent)
	{
		require_once('models/common/common_taxonomy.php');
		$Taxonomy = new common_taxonomy();
		
		return $Taxonomy->getChildren($parent);
	}

	protected function createFacebookStory()
	{
		if (client_action::hasOpenGraphStory('enter', 'competition')) {
			$request = new Onxshop_Request("component/client/facebook_story_create~" 
				. "action=enter"
				. ":object=competition"
				. ":node_id=" . $_SESSION['active_pages'][0]
				. "~");
		}
	}

}
