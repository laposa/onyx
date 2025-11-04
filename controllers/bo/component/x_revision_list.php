<?php
/**
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/x.php');

class Onyx_Controller_Bo_Component_X_Revision_List extends Onyx_Controller_Bo_Component_X {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_revision.php');
        $Revision = new common_revision();
                
        if (in_array($this->GET['object'], common_revision::getAllowedRevisionObjects())) $object = $this->GET['object'];
        if (is_numeric($this->GET['node_id'])) $node_id = $this->GET['node_id'];
        
        $list = $Revision->getList($object, $node_id);
        
        $this->parseList($list);

        parent::parseTemplate();

        return true;
    }

    /**
     * parse
     */
    
    public function parseList($list) {
    
        if (count($list) > 0) {
            require_once('models/client/client_customer.php');
            $Client_Customer = new client_customer();

            $_cache = [];

            foreach ($list as $item) {
                
                if (!array_key_exists($item['customer_id'], $_cache)) $_cache[$item['customer_id']] = $Client_Customer->detail($item['customer_id']);
                $item['customer'] = $_cache[$item['customer_id']];

                $this->tpl->assign('ITEM', $item);
                $this->tpl->parse('content.item');
            }
        } else {
            $this->tpl->parse('content.empty');
        }
    }
    
}

