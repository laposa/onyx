<?php
/** 
 * Copyright (c) 2010-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Page_Header extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * get node detail
         */
         
        require_once('models/common/common_node.php');
        $Node = new common_node();
        $node_data = $Node->nodeDetail($this->GET['id']);

        /**
         * assign variables
         */
         
        if ($node_data['page_title'] == '') {
            $node_data['page_title'] = $node_data['title'];
        }
        
        $this->tpl->assign("NODE", $node_data);

        return true;
    }
}
