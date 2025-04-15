<?php
/**
 * Copyright (c) 2006-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 * TODO: rename to image_gallery
 */

require_once('controllers/node/content/default.php');

class Onyx_Controller_Node_Content_Image_Gallery extends Onyx_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /* we need to include config*/
        require_once('models/common/common_image.php');
        $common_image_conf = common_image::initConfiguration();
        
        require_once('models/common/common_node.php');
        
        $Node = new common_node();
        
        $node_data = $Node->nodeDetail($this->GET['id']);
        
        if ($node_data['component']['template'] == '') $node_data['component']['template'] = 'plain';
        
        /**
         * what controller
         */
         
        if (file_exists(ONYX_PROJECT_DIR . "controllers/component/image_gallery/{$node_data['component']['template']}.php") ||
            file_exists(ONYX_DIR . "controllers/component/image_gallery/{$node_data['component']['template']}.php")) {
        
            $image_controller = 'component/image_gallery/' . $node_data['component']['template'];
        
        } else {
        
            $image_controller = 'component/image_gallery';
        
        }

        /**
         * what template
         */
         
        if (file_exists(ONYX_PROJECT_DIR . "templates/component/image_gallery/{$node_data['component']['template']}.html") ||
            file_exists(ONYX_DIR . "templates/component/image_gallery/{$node_data['component']['template']}.html")) {
        
            $image_template = 'component/image_gallery/' . $node_data['component']['template'];
        
        } else {
        
            $image_template = 'component/image_gallery/plain';
        
        }
        
        /**
         * call controller
         */
        
        $Onyx_Request = new Onyx_Request("{$image_controller}@{$image_template}~relation=node:node_id={$node_data['id']}~");
        $this->tpl->assign("CONTENT", $Onyx_Request->getContent());
        
        $this->tpl->assign('NODE', $node_data);
        $this->tpl->assign('IMAGE_CONF', $common_image_conf);
        
        /**
         * display title
         */
         
        $this->displayTitle($node_data);

        return true;
    }
}
