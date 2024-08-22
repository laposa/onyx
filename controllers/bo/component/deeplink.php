<?php
/** 
 * Copyright (c) 2024 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Bo_Component_Deeplink extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {

        require_once('models/common/common_node.php');
        $Node = new common_node();

        if(ONYX_MOBILE_APP_DEEPLINK_SET_URL) {
            $data = json_decode(file_get_contents(ONYX_MOBILE_APP_DEEPLINK_SET_URL), true);
            foreach($data as $item) {
                $this->tpl->assign('ITEM', $item);
                $this->tpl->parse('content.list.item');
            }
            $this->tpl->parse('content.list');
        }

        if(ONYX_MOBILE_APP_URL) {

            /**
             * make sure latest data are used during form save action
             * this is needed as sub component called at https://github.com/laposa/onyx/blob/master/templates/bo/node/item_deeplink.html#L2
             * is processed before saving data to the database
             *
             * consider passing the node_detail array instead
             */
            if (array_key_exists('node', $_POST) && is_array($_POST['node'])) $node_detail = $_POST['node'];
            else $node_detail = $Node->nodeDetail($this->GET['node_id']);

            $this->tpl->assign('NODE', $node_detail);
            $this->tpl->assign('PATTERN', ONYX_MOBILE_APP_DEEPLINK_VALIDATION_REGEX);
            $this->tpl->assign('PLACEHOLDER', ONYX_MOBILE_APP_URL . '...');
        } else {
            // return empty content
            $this->tpl->blocks = [];
            return true;
        }
    }
}
