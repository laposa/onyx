<?php
/** 
 * Copyright (c) 2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once 'controllers/component/client/facebook_pile.php';

class Onxshop_Controller_Component_Client_Facebook_Pile_Survey extends Onxshop_Controller_Component_Client_Facebook_Pile {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * input
		 */
		
		if ($this->GET['fb_username']) $fb_username = $this->GET['fb_username'];
		else $fb_username = 'me';
		
		if (is_numeric($this->GET['show_number_of_items'])) $this->show_number_of_items = $this->GET['show_number_of_items'];
		else $this->show_number_of_items = 3;
		
		if (is_numeric($this->GET['survey_id'])) $survey_id = $this->GET['survey_id'];
		else $survey_id = 0;
		
		/**
		 * initialize
		 */
	
		require_once('models/client/client_customer.php');
		require_once('models/education/education_survey.php');
		require_once('models/education/education_survey_entry.php');
		$this->Customer = new client_customer();
		$this->Survey = new education_survey();
		$this->SurveyEntry = new education_survey_entry();
		
		/**
		 * get survey detail
		 */
		 
		$survey_detail = $this->Survey->getDetail($survey_id);
		
		$this->tpl->assign('SURVEY', $survey_detail);
		
		/**
		 * call shared actions
		 */
		 
		$this->commonAction();
		
		$user = $this->Facebook->getUser();
		
		if ($user) {
			
			$friend_user_list = $this->getFriendsAppUsers();
			$friend_entries = $this->getFriendEntries($friend_user_list, $survey_id);
			$users_shown = array();
			
			$i = 0;
			
			if (is_array($friend_entries)) {
			
				foreach ($friend_entries as $item) {
					
					if (!in_array($item['customer_id'], $users_shown)) {
					
						if ($i < $this->show_number_of_items) {
							
							$user_detail = $this->Customer->getDetail($item['customer_id']);
							
							$this->tpl->assign('USER_DETAIL', $user_detail);
							$this->tpl->parse('content.item_activity');
							
							$users_shown[] = $item['customer_id'];
							
							$i++;
							
						}
					}
				}
			}
				
			/**
			 * show title only if at least one item is listed
			 */
			
			if (count($users_shown) > 0) $this->tpl->parse('content.title');
			
		}
		
		return true;
		
	}
	
	/**
	 * getFriendEntries
	 */
	 
	public function getFriendEntries($friend_user_list, $survey_id) {
	
		if (!is_array($friend_user_list)) return false;
		if (count($friend_user_list) == 0) return false;
		if (!is_numeric($survey_id)) return false;
		
		$friend_user_list = implode(',', $friend_user_list);
		
		$friend_entries = $this->SurveyEntry->listing("survey_id = $survey_id AND customer_id IN (SELECT id FROM client_customer WHERE facebook_id IN ($friend_user_list))");
		
		return $friend_entries;
		
	}
	
}
