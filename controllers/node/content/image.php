<?php
/**
 * Copyright (c) 2006-2019 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 * TODO: rename to image_gallery
 */

require_once('controllers/node/content/default.php');

class Onyx_Controller_Node_Content_Image extends Onyx_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {
                
        parent::mainAction();

        if ($this->node_data['link_to_node_id'] > 0) $this->tpl->parse('content.image_with_link');
        else $this->tpl->parse('content.image_with_no_link');
        if ($this->node_data['component']['show_caption']) $this->tpl->parse('content.caption');

        return true;
    }
}
