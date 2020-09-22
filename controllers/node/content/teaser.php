<?php
/** 
 * Copyright (c) 2006-2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');
require_once('models/common/common_node.php');

class Onxshop_Controller_Node_Content_Teaser extends Onxshop_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $this->Node = new common_node();

        $node_data = $this->Node->nodeDetail($this->GET['id']);

        if (!is_numeric($node_data['component']['node_id'])) {
            msg("Target Id page is not number", "error");
            return false;
        }

        // set default link text if required
        $params = '';
        if (trim($node_data['component']['teaser_text']) != '') $params .= ":teaser_text=" . urlencode($node_data['component']['teaser_text']);
        if (trim($node_data['component']['link_text']) != '') $params .= ":link_text=" . urlencode($node_data['component']['link_text']);

        // check for image override
        $image = $this->Node->getTeaserImageForNodeId($this->GET['id']); // in the actual node/content/teaser (not the target page)
        if ($image) $params .= ":img_src=" . urlencode($image['src']);

        /**
         * call controller
         */

        $_Onxshop_Request = new Onxshop_Request("component/teaser~target_node_id={$node_data['component']['node_id']}{$params}~");
        $this->tpl->assign('CONTENT', $_Onxshop_Request->getContent());

        $this->tpl->assign('NODE', $node_data);

        if ($node_data['display_title'])  $this->tpl->parse('content.title');

        return true;
    }


}
