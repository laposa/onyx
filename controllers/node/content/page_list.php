<?php
/** 
 * Copyright (c) 2015-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');
require_once('models/common/common_node.php');

class Onxshop_Controller_Node_Content_Page_List extends Onxshop_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $this->Node = new common_node();

        $node_data = $this->Node->nodeDetail($this->GET['id']);
        
        /**
         * get image size options
         */
        
        $image_o = $this->getImageSizeOptions($node_data);
        
        /**
         * call controller
         */

        $content = '';
        $node_ids = $node_data['component']['node_ids'];
        $template = $node_data['component']['template'];
        $link_text = $node_data['component']['link_text'];
        
        $_Onxshop_Request = new Onxshop_Request("component/page_list_$template~node_ids={$node_ids}:link_text=$link_text:image_width={$image_o['width']}:image_height={$image_o['height']}:image_fill={$image_o['fill']}~");
        $content = $_Onxshop_Request->getContent();

        /**
         * assign to template
         */
         
        $this->tpl->assign('CONTENT', $content);
        $this->tpl->assign('NODE', $node_data);

        if ($node_data['display_title'])  $this->tpl->parse('content.title');

        return true;
    }


}
