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
		
		$Node = new common_node();
		
		/**
		 * input
		 */
		
		if ($this->GET['fb_username']) $fb_username = $this->GET['fb_username'];
		else $fb_username = 'me';
		
		/**
		 * call shared actions
		 */
		 
		$this->commonAction();
		
		$user = $this->Facebook->getUser();
		
		if ($user) {
			
			$activity_list = $this->getFriendsActivity();
			
			foreach ($activity_list as $item) {
			
				$post_detail = $this->Facebook->api('/' . $item['post_id']);
				
				if (preg_match('/[0-9]*$/', $post_detail['link'], $matches)) {
					
					$node_id = $matches[0];
					$node_detail = $Node->getDetail($node_id);
					
					//cut off long titles
					if (strlen($node_detail['title']) > 32) $node_detail['title'] = substr($node_detail['title'], 0, 32) . '…' ;
					
					$this->tpl->assign('NODE', $node_detail);
					$this->tpl->assign('FACEBOOK_POST', $post_detail);
					$this->tpl->parse('content.item_activity');
					
				}
			
			}
			
			/**
			 * use user list
			 */
			
			$activity_list_count = count($activity_list);
			
			if ($activity_list_count < 3) {
				
				$friend_user_list = $this->getFriendsAppUsers();
				
				$i = 0;
				
				foreach ($friend_user_list as $item) {
					
					if ($i < (3 - $activity_list_count)) {
						$user_detail = $this->Facebook->api("/{$item}");
						
						$this->tpl->assign('FACEBOOK_USER', $user_detail);
						$this->tpl->parse('content.item_friend_user');
						
						$i++;
						
					}
				}
			}
		}
		
		return true;
		
	}
	
	/**
	 * getFriends
	 */
	 
	public function getFriends($facebook_user_id) {
	
		$fql = "SELECT uid, first_name, last_name FROM user WHERE uid in (SELECT uid2 FROM friend where uid1 = $facebook_user_id)";
		
		$response = $this->Facebook->api(array(
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
		
		$response = $this->Facebook->api(array(
			'method' => 'fql.query',
			'query' =>$fql
		));
		
		return $response;
	}
	
	/**
	 * getFriendsAppUsers
	 */
	 
	public function getFriendsAppUsers() {
		
		$response = $this->Facebook->api(array('method' => 'friends.getAppUsers'));
		
		return $response;

	}
 	
}
