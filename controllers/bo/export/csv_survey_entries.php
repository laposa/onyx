<?php
/** 
 * Copyright (c) 2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */
 
require_once('controllers/bo/export/csv.php');

class Onxshop_Controller_Bo_Export_CSV_Survey_Entries extends Onxshop_Controller_Bo_Export_CSV {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		set_time_limit(0);
		
		require_once('models/education/education_survey.php');
		require_once('models/education/education_survey_question.php');
		require_once('models/education/education_survey_entry.php');
		require_once('models/education/education_survey_entry_answer.php');
		require_once('models/client/client_customer.php');
		require_once('models/ecommerce/ecommerce_store.php');
		require_once('models/common/common_taxonomy_tree.php');
		
		$this->Survey = new education_survey();
		$this->Question = new education_survey_question();
		$this->Survey_Entry = new education_survey_entry();
		$this->Survey_Entry_Anwer = new education_survey_entry_answer();
		$this->Customer = new client_customer();
		$this->Store = new ecommerce_store();
		$this->Taxonomy_Tree = new common_taxonomy_tree();
		
		/**
		 * Get input
		 */
		 
		$survey_id = (int) $this->GET['survey_id'];
		
		/**
		 * Get the list
		 */

		$records = array();

		$questions = $this->Question->listQuestions($survey_id);
		 
		if ($entries_list = $this->Survey_Entry->getListForSurveyId($survey_id)) {
		
			foreach ($entries_list as $entry) {
				
				$item = array();
				
				$customer = $this->getCustomerDetail($entry['customer_id']);
				
				// entry
				$item['id'] = $entry['id'];
				$item['created'] = $entry['created'];
				$item['ip_address'] = $entry['ip_address'];
				$item['session_id'] = $entry['session_id'];
				$item['customer_id'] = $entry['customer_id'];
				
				// customer
				$item['first_name'] = $customer['first_name'];
				$item['last_name'] = $customer['last_name'];
				$item['email'] = $customer['email'];
				$item['telephone'] = $customer['telephone'];
				$item['newsletter'] = $customer['newsletter'];
				$item['home_store_id'] = $customer['store_id'];
				$item['home_store_name'] = $customer['home_store_name'];
				$item['home_store_reference_code'] = $customer['home_store_reference_code'];
				$item['county'] = $customer['county'];

				// make sure all questions are present in the result (even as empty cells)
				foreach ($questions as $question) {
					$question_id = $question['id'];
					$item['question_title_'.$question_id] = "";
					$item['answer_title_'.$question_id] = "";
					$item['answer_value_'.$question_id] = "";
				}
				
				// answer
				$question_and_answers = $this->Survey_Entry->getQuestionsAndAnswersForEntry($entry['id']);

				foreach($question_and_answers as $qa_item) {
					
					$question_id = $qa_item['question_id'];
					$entry_answer_id = $qa_item['entry_answer_id'];
					
					$item['question_title_'.$question_id] = $qa_item['question_title'];
					$item['answer_title_'.$question_id] = $qa_item['answer_title'];
					$item['answer_value_'.$question_id] = $qa_item['answer_value'];
				
					// include uploaded file
					$save_filename = $this->Survey_Entry_Anwer->getFilenameToSave($qa_item['answer_value'], $entry['id'], $question_id, $entry_answer_id);
					$file = "var/surveys/$survey_id/{$save_filename}";
					if (is_file(ONXSHOP_PROJECT_DIR . $file)) $item['answer_value_'.$question_id] = "http://" . $_SERVER['HTTP_HOST'] . "/download/$file";
						
				}
				
				$records[] = $item;	
				
			}
		}

		$this->commonCSVAction($records, "survey-entries-$survey_id");

		return true;
	}
	
	/**
	 * getCustomerDetail
	 */
	 
	public function getCustomerDetail($customer_id) {
		
		if (!is_numeric($customer_id)) return false;
		
		// get customer data
		$customer = $this->Customer->detail($customer_id);
		$customer['other_data'] = unserialize($customer['other_data']);
		
		// get customer's home store
		if ($customer['store_id'] > 0) {
			$store = $this->Store->detail($customer['store_id']);
			$customer['home_store_name'] = $store['title'];
			$customer['home_store_reference_code'] = $store['code'];
		}
		
		// get customer's county
		if ($customer['other_data']['county'] > 0) {
			$taxonomy = $this->Taxonomy_Tree->detailFull($customer['other_data']['county']);
			$customer['county'] = $taxonomy['label']['title'];
		}
		
		return $customer;
	}
}
