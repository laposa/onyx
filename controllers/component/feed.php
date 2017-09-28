<?php
/** 
 * Copyright (c) 2006-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Feed extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $feed_options = array();
        
        /**
         * url must GET option must be provided
         */
         
        if ($this->GET['url']) {
        
            $feed_options['url'] = urldecode(base64_decode($this->GET['url']));
        
        } else {
        
            msg("component/feed: Please provide feed URL", 'error');
            //exit
            return true;
            
        }
        
        //msg("error test", 'error', 1);
        
        /**
         * if path is relative on server, prepend SERVER_NAME
         */
         
        if (!preg_match('/https?:\/\//', $feed_options['url'])) {
        
            if ($_SERVER['HTTPS']) $protocol = 'https';
            else $protocol = 'http';
        
            $feed_options['url'] = "$protocol://{$_SERVER['SERVER_NAME']}{$feed_options['url']}";
        }
        
        /**
         * other feed options
         */
         
        if (is_numeric($this->GET['items_limit'])) $feed_options['items_limit'] = $this->GET['items_limit'];
        else $feed_options['items_limit'] = 0;
        
        $feed_options['channel_title'] = $this->GET['channel_title'];
        $feed_options['image'] = $this->GET['image'];
        $feed_options['copyright'] = $this->GET['copyright'];
        $feed_options['description'] = $this->GET['description'];
        $feed_options['content'] = $this->GET['content'];
        $feed_options['pubdate'] = $this->GET['pubdate'];
        
        return $this->processFeed($feed_options);
        
    }
    
    /**
     * process feed
     */
     
    public function processFeed($feed_options) {
    
        /**
         * include required libraries
         */
         
        require_once('Zend/Cache.php');
        require_once('Zend/Feed/Reader.php');
        
        
        /**
         * cache init
         */
        $frontendOptions = array('lifetime' => 60*5,'automatic_serialization' => true);
        $backendOptions = array('cache_dir' => ONXSHOP_PROJECT_DIR . 'var/cache/');
 
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        
        Zend_Feed_Reader::setCache($cache);
        Zend_Feed_Reader::useHttpConditionalGet();

        // Fetch the feed
        try {
        
            $feed = Zend_Feed_Reader::import($feed_options['url']);
        
        } catch (Zend_Exception $e) {
        
            // feed import failed
            msg("Exception caught importing feed: {$e->getMessage()}", 'error');
            
            //delete downloaded from cache (probably invalid)
            // TODO: how to delete feed cache?
            
            //dont cache actual controler
            return false;
        
        }
        
        
        // Initialize the channel data array
        $channel = array(
            'title'       => $feed->getTitle(),
            'link'        => $feed->getLink(),
            'dateModified' => $feed->getDateModified(),
            'description' => $feed->getDescription(),
            'language'     => $feed->getLanguage(),
            'entries'       => array()
            );
        
        // Loop over each channel item and store relevant data
        //check if 
        foreach ($feed as $entry) {
            $channel['entries'][] = array(
                'title'       => $entry->getTitle(),
                'link'        => $entry->getLink(),
                'author'      => $entry->getAuthor(),
                'description' => $entry->getDescription(),
                'content'     => $entry->getContent(),
                'pubDate'     => $entry->getDateModified()->get(),//"dd MMMM HH:mm"
                'image'       => $entry->getElement()->getElementsByTagName("image")->item(0)->textContent
            );

        }
        
        $channel['encoding'] = 'utf8';
        
        if (is_array($channel['entries'])) {
        
            $this->tpl->assign('CHANNEL', $channel);
            
            if ($channel['title'] != '' && $channel_options['channel_title'])  $this->tpl->parse('content.channel_title');
            if ($channel['image_url'] != '' && $channel_options['image'])  $this->tpl->parse('content.image');
            if ($channel['copyright'] != '' && $channel_options['copyright'])  $this->tpl->parse('content.copyright');
        
            //locale
            $channel['copyright'] = recode_string("{$channel['encoding']}..utf8", $channel['copyright']);
        
        
            //reverse order
            //$rs = array_reverse($rs);
        
            $i = 0;

            foreach ($channel['entries'] as $item) {
            
                if ($feed_options['items_limit'] == 0 || $i < $feed_options['items_limit']) {
                
                    $item['description'] = html_entity_decode($item['description']);
                    
                    //problem with <p>
                    //$item['description'] = preg_replace("/<p>(.*)<\/p>/i", "$1", $item['description']);
                    
                    //locale
                    $item['description'] = recode_string("{$channel['encoding']}..utf8", $item['description']);
                    $item['title'] = recode_string("{$channel['encoding']}..utf8", $item['title']);
                    
                    
            
                    //odd or even
                    if ($i%2 == 0) $item['odd_even_class'] = "even";
                    else $item['odd_even_class'] = "odd";
            
                    //prepare item
                    $item = $this->prepareItem($item);
                    
                    //assign
                    $this->tpl->assign('ITEM', $item);
                    
                    //parse
                    if ($item['description'] != '' && $feed_options['description'])  $this->tpl->parse('content.item.description');
                    if ($item['content'] != '' && $feed_options['content'])  $this->tpl->parse('content.item.content');
                    if ($item['pubDate'] != '' && $feed_options['pubdate'])  $this->tpl->parse('content.item.pubdate');
                    if ($item['image'] != '')  $this->tpl->parse('content.item.image');
                    $this->tpl->parse('content.item');
                    
                }
                
                $i++;
            }
            
        } else {
        
            msg ("Feed '{$feed_options['url']}' not found...", 'error');
        
        }

        return true;
    
    }
    
    /**
     * prepare item
     */
     
    public function prepareItem($item) {
        
        return $item;
    }
}