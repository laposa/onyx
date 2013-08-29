<?php
/** 
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once 'controllers/component/client/facebook.php';

class Onxshop_Controller_Component_Client_Facebook_Pile extends Onxshop_Controller_Component_Client_Facebook {

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
		
		/**
		 * initialize
		 */
		 
		$Node = new common_node();
		
		
		/**
		 * call shared actions
		 */
		 
		$this->commonAction();
		
		$user = $this->Facebook->getUser();
		
		if ($user) {
			
			$activity_list = $this->getFriendsActivity();
			
			$activity_list_count = count($activity_list);
			
			if (is_array($activity_list)) {
			
				$i = 0;
				
				foreach ($activity_list as $item) {
				
					if ($i < $this->show_number_of_items) {
					
						try {
						
							$post_detail = $this->Facebook->api('/' . $item['post_id']);
						
						} catch (FacebookApiException $e) {
						
							msg($e->getMessage(), 'error', 1);
							
						}
						
						if (preg_match('/[0-9]*$/', $post_detail['link'], $matches)) {
							
							$node_id = $matches[0];
							$node_detail = $Node->getDetail($node_id);
							
							//cut off long titles
							if (strlen($node_detail['title']) > 32) $node_detail['title'] = substr($node_detail['title'], 0, 32) . '…' ;
							
							$this->tpl->assign('NODE', $node_detail);
							$this->tpl->assign('FACEBOOK_POST', $post_detail);
							$this->tpl->parse('content.item_activity');
							$i++;
						}
						
					}
				
				}
			}
			
			/**
			 * use user list
			 */
			
			if ($activity_list_count < $this->show_number_of_items) {
				
				$friend_user_list = $this->getFriendsAppUsers();
				
				$i = 0;
				
				if (is_array($friend_user_list)) {
				
					foreach ($friend_user_list as $item) {
						
						if ($i < ($this->show_number_of_items - $activity_list_count)) {
							
							try {
	
								$user_detail = $this->Facebook->api("/{$item}");
	
							} catch (FacebookApiException $e) {
			
								msg($e->getMessage(), 'error', 1);
								
							}
							
							$this->tpl->assign('FACEBOOK_USER', $user_detail);
							$this->tpl->parse('content.item_friend_user');
							
							$i++;
							
						}
					}
				}
			}
			
			/**
			 * show title only if at least one item is listed
			 */
			 
			$friend_user_list_count = count($friend_user_list);
			
			$total_list_count = $activity_list_count + $friend_user_list_count;
			
			if ($total_list_count > 0) $this->tpl->parse('content.title');
			
		}
		
		return true;
		
	}
	
	/**
	 * getFriends
	 */
	 
	public function getFriends($facebook_user_id) {
	
		$fql = "SELECT uid, first_name, last_name FROM user WHERE uid in (SELECT uid2 FROM friend where uid1 = $facebook_user_id)";
		
		$response = $this->makeApiCallCached(array(
				'method' => 'fql.query',
				'query' =>$fql
			));
		
		return $response;
	}
	
	/**
	 * getFriendsActivity
	 */
	 
	public function getFriendsActivity() {
	
		$fql = "SELECT app_id, type, created_time, post_id, actor_id, message, action_links, description, permalink FROM stream WHERE filter_key IN (SELECT filter_key FROM stream_filter WHERE uid = me()) AND actor_id IN (SELECT uid2 FROM friend WHERE uid1 = me()) AND app_id = " . ONXSHOP_FACEBOOK_APP_ID . " LIMIT 3";
		
		$response = $this->makeApiCallCached(array(
				'method' => 'fql.query',
				'query' =>$fql
			));
		
		return $response;
	}
	
	/**
	 * getFriendsAppUsers
	 */
	 
	public function getFriendsAppUsers() {
		
		$response = $this->makeApiCallCached(array('method' => 'friends.getAppUsers'));
		
		return $response;
	}
 	
}
