<?php
/**
 * Copyright (c) 2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onyx_Controller_Node_Content_Video extends Onyx_Controller_Node_Content_Default {
    
        /**
     * main action
     */
     
    public function mainAction() {
        
        /**
         * initialise node and get detail
         */
         
        require_once('models/common/common_node.php');
        
        $Node = new common_node();
        
        $node_data = $Node->nodeDetail($this->GET['id']);
        
        /**
         * get node component options
         */
         
        if ($node_data['component']['provider'] == '') $node_data['component']['provider'] = 'vimeo';
        
        if ($node_data['component']['autoplay']) $autoplay_param = ":autoplay=1";
        else $autoplay_param = '';
        
        /**
         * pass to menu component
         */
         
        $Onyx_Request = new Onyx_Request("component/video_{$node_data['component']['provider']}~video_id={$node_data['component']['video_id']}{$autoplay_param}~");
        $this->tpl->assign("VIDEO", $Onyx_Request->getContent());
        $this->tpl->assign("NODE", $node_data);
        
        if ($node_data['display_title'])  $this->tpl->parse('content.title');

        return true;
    }
    
}
