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
			
			/**
			 * cache init
			 */
			
			require_once('Zend/Cache.php');
			$frontendOptions = array('lifetime' => 60*5,'automatic_serialization' => true);
			$backendOptions = array('cache_dir' => ONXSHOP_PROJECT_DIR . 'var/cache/');
			$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
			$cache_id = "twitter_statuses_userTimeline_$username";
			
			if (!is_array($cached_data = $cache->load($cache_id))) {
				
				$timeline = $this->twitterCallExtend('statuses', 'userTimeline', array("screen_name"   => $username));
				$cache->save($timeline);
				
			} else {
				
				$timeline = $cached_data;
				
			}
			
			/**
			 * we should have timeline feed at this stage
			 */
			 
			if (is_array($timeline)) {
			
				$index = 0;
				
				foreach ($timeline as $k=>$item) {
					
					// hashtag starts with
					if (preg_match('/#'.$hashtag.'/i', $item->text)) {
						
						$item->text = $this->highlightLinks($item->text);
						
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
	
	/**
	 * highlight links
	 */
	 
	public function highlightLinks($text) {
		
		// hashtags
		$text = preg_replace('/(#\w*[a-zA-Z0-9_]+\w*)/', '<span class="link hashtag">\1</span>', $text);
		// usernames
		$text = preg_replace('/(@\w*[a-zA-Z0-9_]+\w*)/', '<span class="link username">\1</span>', $text);
		// links
		$text = preg_replace('/(https?:\/\/[^\s]*)/', '<span class="link url">\1</span>', $text);
		
		return $text;
		
	}
	
}
