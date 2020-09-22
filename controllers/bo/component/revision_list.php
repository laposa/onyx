<?php
/**
 * Copyright (c) 2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Bo_Component_Revision_List extends Onyx_Controller {

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

        return true;
    }

    /**
     * parse
     */
    
    public function parseList($list) {
    
        if (count($list) > 0) {
            require_once('models/client/client_customer.php');
            $Client_Customer = new client_customer();

            foreach ($list as $item) {
                $item['customer'] = $Client_Customer->getDetail($item['customer_id']);
                $this->tpl->assign('ITEM', $item);
                $this->tpl->parse('content.item');
            }
        } else {
            $this->tpl->parse('content.empty');
        }
    }
    
}

