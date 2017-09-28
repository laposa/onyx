<?php
/**
 * Copyright (c) 2006-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 * TODO: rename to image_gallery
 */

require_once('controllers/node/content/default.php');

class Onxshop_Controller_Node_Content_Image extends Onxshop_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {
                
        parent::mainAction();

        if ($this->node_data['component']['show_caption']) $this->tpl->parse('content.caption');

        return true;
    }
}
