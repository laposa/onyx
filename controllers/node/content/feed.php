<?php
/**
 * Copyright (c) 2006-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onxshop_Controller_Node_Content_Feed extends Onxshop_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {
        
        require_once('models/common/common_node.php');
        
        $Node = new common_node();
        
        //get data
        $node_data = $Node->nodeDetail($this->GET['id']);
        $rss_options = $node_data['component'];
        
        //detect feed specific controller
        if (preg_match('/api.twitter.com/', $rss_options['url'])) $feed_controller = 'feed_twitter';
        else $feed_controller = 'feed';
        
        $this->tpl->assign('FEED_CONTROLLER', $feed_controller);
        
        //prepare data
        $rss_options['url'] = base64_encode(urlencode(trim($rss_options['url'])));
        
        //get RSS_RESULT
        $nsite_request = "component/{$feed_controller}~url={$rss_options['url']}:items_limit={$rss_options['items_limit']}:channel_title={$rss_options['channel_title']}:image={$rss_options['image']}:copyright={$rss_options['copyright']}:description={$rss_options['description']}:content={$rss_options['content']}:pubdate={$rss_options['pubdate']}~";
        $_Onxshop_Request = new Onxshop_Request($nsite_request);
        $this->tpl->assign("RSS_RESULT", $_Onxshop_Request->getContent());
            
        //if ajax option enable, allow to update dynamicaly
        if ($rss_options['ajax'] == 1) {
            //AJAX METHOD
            $this->tpl->assign("RSS_OPTIONS", $rss_options);
            $this->tpl->parse('content.ajax');
        }
        
        $this->tpl->assign('NODE', $node_data);
        
        if ($node_data['display_title'])  $this->tpl->parse('content.title');

        return true;
    }
}
