<?php
/** 
 * Copyright (c) 2013-2014 Laposa Limited (https://laposa.ie)
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
         
        $username = ltrim(ONXSHOP_TWITTER_USERNAME, "@"); // allow to use leading "at" sign i.e. @onxshop
        $hashtag = ltrim(ONXSHOP_TWITTER_HASHTAG, "#"); // allow to use leading "hash" sign i.e. #cms
        
        if (is_numeric($this->GET['limit_from'])) $limit_from = $this->GET['limit_from'];
        if (is_numeric($this->GET['limit_per_page'])) $limit_per_page = $this->GET['limit_per_page'];

        if (is_numeric($this->GET['limit_fetch'])) $limit_fetch = $this->GET['limit_fetch']; else $limit_fetch = 50;
        $limit_fetch = max(min($limit_fetch, 1000), 1); // make sure the value is between 1 and 1000

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
                
                $timeline = $this->twitterCallExtend('statuses', 'user_timeline', array("screen_name" => $username, "count" => $limit_fetch));
                $cache->save($timeline);
                
            } else {
                
                $timeline = $cached_data;
                
            }
            
            /**
             * we should have timeline feed at this stage
             */
             
            if (is_array($timeline)) {
            
                $index = 0;
                $counter = 0;
                
                foreach ($timeline as $k=>$item) {
                    
                    // hashtag starts with
                    if (empty($hashtag) || preg_match('/#'.$hashtag.'/i', $item->text)) {
                        
                        if (!isset($limit_from) || $counter >= $limit_from) {
                            
                            $item->text = $this->highlightLinks($item->text);
                        
                            $this->tpl->assign('INDEX', $index);
                            $this->tpl->assign('ITEM', $item);
                            $this->tpl->parse('content.item');
                            $index++;
                        
                            if (isset($limit_per_page) && $index == $limit_per_page) break;
                        }
                    
                        $counter++;
                            
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
