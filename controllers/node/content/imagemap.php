<?php
/**
 * Copyright (c) 2006-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/node/content/default.php');

class Onyx_Controller_Node_Content_Imagemap extends Onyx_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_node.php');
        require_once('models/common/common_image.php');
        $Image = new common_image();
        
        $Node = new common_node();
        
        $node_data = $Node->nodeDetail($this->GET['id']);
        
        $map = $node_data['component'];
        
        $this->tpl->assign("MAP", $map);
        
        $images = $Image->listing("role='imagemap' AND node_id=".$this->GET['id'], "priority DESC, id ASC");
        $this->tpl->assign("IMAGE", $images['0']);
        
        $this->tpl->assign('NODE', $node_data);
        
        if ($node_data['display_title'])  $this->tpl->parse('content.title');

        return true;
    }
}
