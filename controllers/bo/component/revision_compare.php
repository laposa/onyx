<?php
/**
 * Copyright (c) 2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_Revision_Compare extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_revision.php');
        $Revision = new common_revision();
        $id = 0;

        if (in_array($this->GET['object'], common_revision::getAllowedRevisionObjects())) $object = $this->GET['object'];
        $selected_id = $this->GET['id'];
        $selected_id = explode(" ", $selected_id);

        $this->parseList($selected_id);

        return true;
    }

    /**
     * parse
     */
    
    public function parseList($list) {
    
        if (count($list) > 0) {
            require_once('models/client/client_customer.php');
            require_once('models/common/common_revision.php');
            $Client_Customer = new client_customer();
            $Common_Revision = new common_revision();
            $prev = false;

            foreach ($list as $id) {
                $item['details'] = $Common_Revision->getRevisionById($id);
                $item['details']['content'] = unserialize($item['details']['content']);
                if($prev != false) {
                    // LOOK FOR NEW AND MODIFIED KEYS
                    foreach($item['details']['content'] as $key=>$row) {
                        $item['details']['content'][$key] = $row;
                        if(!isset($prev[$key])) {
                            $item['details']['content'][$key] .= " *NEW*";
                        } elseif($item['details']['content'][$key] != $prev[$key]) {
                            $item['details']['content'][$key] .= " *MODIFIED*";
                        }
                    }
                    // LOOK FOR REMOVED
                    foreach($prev as $key=>$row) {
                        if(!isset($item['details']['content'][$key])) {
                            $item['details']['content'][$key] = $prev[$key]." *REMOVED*";
                        }
                    }
                }
                $prev = $item['details']['content'];

                $item['client_customer'] = $Client_Customer->getDetail($item['details']['customer_id']);
                $this->tpl->assign('ITEM', $item);
                $this->tpl->assign('WIDTH', (100 / count($list)));
                $this->tpl->parse('content.item');
            }
        } else {
            $this->tpl->parse('content.empty');
        }
    }
    
}

