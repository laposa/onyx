<?php
/** 
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once 'controllers/component/client/twitter.php';

class Onxshop_Controller_Component_Client_Twitter_Timeline extends Onxshop_Controller_Component_Client_Twitter {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * input
		 */
		 
		$username = ONXSHOP_TWITTER_USERNAME;
		$hashtag = ONXSHOP_TWITTER_HASHTAG;
		
		/**
		 * init twitter
		 */
		 
		$token = array(
			'token' => ONXSHOP_TWITTER_ACCESS_TOKEN,
			'secret' => ONXSHOP_TWITTER_ACCESS_TOKEN_SECRET,
		);
			
		$this->initTwitter($username, $token);
		
		/**
		 * get user timeline
		 */
		
		if ($token) {
			
			$timeline = $this->twitterCallExtend('statuses', 'userTimeline', array("screen_name"   => $username));
			
			if (is_array($timeline)) {
				
				$index = 0;
				
				foreach ($timeline as $k=>$item) {
					
					// hashtag 
					if (preg_match('/#'.$hashtag.'/i', $item->text)) {
						
						$this->tpl->assign('INDEX', $index);
						$this->tpl->assign('ITEM', $item);
						$this->tpl->parse('content.item');
						$index++;
						
					}
					
				}
			}
			
		}
		
		return true;
		
	}
	
}
