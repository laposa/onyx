<?php
/** 
 * Copyright (c) 2024 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once(ONYX_DIR . "conf/global.php");
class Onyx_Controller_Bo_Component_Deeplink extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {

        require_once('models/common/common_node.php');
        $Node = new common_node();

        if(ONYX_MOBILE_APP_URL) {
            $pattern = '^' . str_replace('/', '\/',ONYX_MOBILE_APP_URL) . '.*';

            $this->tpl->assign('NODE', $Node->nodeDetail($this->GET['node_id']));
            $this->tpl->assign('PATTERN', $pattern);
            $this->tpl->assign('PLACEHOLDER', ONYX_MOBILE_APP_URL . '...');
        } else {
            return false;
        }
    }
}
