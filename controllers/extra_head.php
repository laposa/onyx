<?php
/** 
 * Copyright (c) 2013-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Extra_Head extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {

        $Node = new common_node();
        $node_data = $Node->nodeDetail($this->GET['id']);
        
        /**
         * process app deeplink tags
         */

        $this->processDeeplink($node_data);
        
        return true;
        
    }

    /**
     * Process Deeplink for page
     */
    public function processDeeplink($node_data) {

        if ($node_data['custom_fields']->deeplink) {
            $this->tpl->assign('DEEPLINK_URL', $node_data['custom_fields']->deeplink);
            $this->tpl->parse('head.deeplink');
        }
    }
}
