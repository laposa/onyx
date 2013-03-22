<?php
/** 
 * Copyright (c) 2009-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/comment.php');

class Onxshop_Controller_Component_Comment_Add extends Onxshop_Controller_Component_Comment {

	/**
	 * custom comment action
	 */
	 
	public function customCommentAction($data, $options) {
	
		$data['rating'] = 0;
		$this->displaySubmitForm($data, $options);
		
	}


	/**
	 * conditional display submit form
	 */
	 
	public function displaySubmitForm($data, $options) {

		/**
		 * display and process insert only when allowed
		 */
		 
		if ($_SESSION['client']['customer']['id'] || $options['allow_anonymouse_submit']) {
			
			if ($_POST['save']) {
			
				/**
				 * insert comment
				 */
				
				if ($this->insertComment($data, $options)) $this->tpl->parse('content.comment_inserted');
				else $this->assignAndParseForm($data);
			
			} else {
				 
				$this->assignAndParseForm($data);
			}
			
		} else {
			
			$_Onxshop_Request = new Onxshop_Request("component/client/login");
			$this->tpl->assign('LOGIN_BOX', $_Onxshop_Request->getContent());
			
			$this->tpl->parse('content.log_to_insert');
		}

	}

	/**
	 * assign data to form and parse
	 */
	 
	public function assignAndParseForm($data) {
		
		/**
		 * prepopulate data
		 */
		 	
		if (is_numeric($_SESSION['client']['customer']['id']) && $_SESSION['client']['customer']['id'] > 0) {
		
			$data['customer_id'] = $_SESSION['client']['customer']['id'];
			$customer_detail = $this->Comment->getCustomerDetail($data['customer_id']);

			$data['author_name'] = "{$customer_detail['customer']['first_name']} {$customer_detail['customer']['last_name']}";
			$data['author_email'] = $customer_detail['customer']['email'];
			
		}
		
		$this->tpl->assign('COMMENT', $data);
		
		/**
		 * check if identity input field is visible
		 */
		 
		if ($this->checkIdentityVisibility($data)) {
			$this->tpl->parse('content.comment_insert.identity_show');
		} else {
			$this->tpl->parse('content.comment_insert.identity_hidden');
		}
		
		/**
		 * display insert form
		 */
		
		$this->tpl->parse('content.comment_insert');
	}



	/**
	 * insert comments
	 */
	 
	function insertComment($data, $options = false) {
		
		if ($_POST['save']) {
		
			if ($this->checkData($data)) {
			
				/**
				 * set customer id
				 */
				 
				if (is_numeric($_SESSION['client']['customer']['id']) && $_SESSION['client']['customer']['id'] > 0) {
		
					$data['customer_id'] = $_SESSION['client']['customer']['id'];
			
				} else if (!is_numeric($data['customer_id']) && $options['allow_anonymouse_submit'])  {
					//anonymous
					$data['customer_id'] = 0;
				}
		
				$data['relation_subject'] = $this->getRelationSubject();
				
				if (is_numeric($data['customer_id'] )) {
					
					if ($this->Comment->insertComment($data)) {
						
						msg('Your comment has been inserted');
						
						return true;
					}
				} else {
					msg("Must be logged in!", 'error');
					return false;
				}
				
			} else {
				
				msg("Please fill in all fields", 'error');
			}
		} else {
		
			return false;
		}
	
	}
	
	/**
	 * check data
	 */
	 
	public function checkData($data) {
	
		if (trim($data['title']) == '' || trim($data['author_name']) == '' || trim($data['author_email']) == '' || trim($data['title']) == '' || !is_numeric($data['rating'])) return false;
		else return true;
	}

	
}
