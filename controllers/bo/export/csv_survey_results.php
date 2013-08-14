<?php
/** 
 * Copyright (c) 2011-2013 Laposa Ltd (http://laposa.co.uk)
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
		require_once('models/education/education_survey_entry.php');
		require_once('models/client/client_customer.php');
		require_once('models/ecommerce/ecommerce_store.php');
		require_once('models/common/common_taxonomy_tree.php');
		
		$Survey = new education_survey();
		$Survey_Entry = new education_survey_entry();
		$Survey_Entry = new education_survey_entry();
		$Customer = new client_customer();
		$Store = new ecommerce_store();
		$Taxonomy_Tree = new common_taxonomy_tree();
		
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

			if (is_array($question['answer_list'])) {

				foreach ($question['answer_list'] as $answer) {
					$item['answer_id'] = $answer['id'];
					$item['answer_title'] = $answer['title'];
					$item['answer_votes'] = (int) $Survey->getAnswerUsage($answer['id']);
					$records[] = $item;
				}

			} else {

				if ($question['type'] == 'text' || $question['type'] == 'file') {

					$answers = $Survey->getAnswersForQuestion($question['id']);

					if (is_array($answers)) {
					
						foreach ($answers as $answer) {

							$entry = $Survey_Entry->detail($answer['survey_entry_id']);

							// get customer data
							$customer = $Customer->detail($entry['customer_id']);
							$customer['other_data'] = unserialize($customer['other_data']);
							// get customer's home store
							if ($customer['other_data']['home_store_id'] > 0) {
								$store = $Store->detail($customer['other_data']['home_store_id']);
								$customer['home_store_name'] = $store['title'];
							}
							// get customer's county
							if ($customer['other_data']['county'] > 0) {
								$taxonomy = $Taxonomy_Tree->detailFull($customer['other_data']['county']);
								$customer['county'] = $taxonomy['label']['title'];
							}

							$item['answer_id'] = $answer['id'];
							$item['survey_entry_id'] = $answer['survey_entry_id'];
							$item['answer_title'] = $answer['value'];
							$item['created'] = $entry['created'];
							$item['ip_address'] = $entry['ip_address'];
							$item['session_id'] = $entry['session_id'];
							$item['customer_id'] = $entry['customer_id'];
							$item['customer_email'] = $customer['email'];
	    					$item['title_before'] = $customer['title_before'];
	    					$item['first_name'] = $customer['first_name'];
	    					$item['last_name'] = $customer['last_name'];
	    					$item['title_after'] = $customer['title_after'];
	    					$item['email'] = $customer['email'];
	    					$item['telephone'] = $customer['telephone'];
	    					$item['home_store_id'] = $customer['other_data']['home_store_id'];
	    					$item['home_store_name'] = $customer['home_store_name'];
	    					$item['city'] = $customer['other_data']['city'];
	    					$item['county'] = $customer['county'];
							
							// include uploaded file
							$file = "var/surveys/{$answer['survey_entry_id']}-{$question['id']}-{$answer['id']}-{$answer['value']}";
							if (file_exists(ONXSHOP_PROJECT_DIR . $file)) $item['file'] = "http://" . $_SERVER['HTTP_HOST'] . "/download/$file";
							else $item['file'] = 'n/a';
							
							$records[] = $item;
						}
					}
				}

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
			$this->sendCSVHeaders('surveys');
			
		} else {
			
			echo "no records"; exit;
		
		}

		return true;
	}
}
