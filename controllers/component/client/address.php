<?php
/** 
 * Copyright (c) 2005-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Component_Client_Address extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * include node configuration
         */
                
        require_once('models/common/common_node.php');
        $node_conf = common_node::initConfiguration();
        $this->tpl->assign('NODE_CONF', $node_conf);
        
        /**
         * client
         */
         
        require_once('models/client/client_address.php');
        $Address = new client_address();
        
        $invoices_address_id = (is_numeric($this->GET['invoices_address_id'] ?? null)) ? $this->GET['invoices_address_id'] : null;
        $delivery_address_id = (is_numeric($this->GET['delivery_address_id'] ?? null)) ? $this->GET['delivery_address_id'] : null;
        
        //if we have not address_ids, we'll use session data
        if (!is_numeric($invoices_address_id) && !is_numeric($delivery_address_id)) {
            $invoices_address_id = $_SESSION['client']['customer']['invoices_address_id'];
            $delivery_address_id = $_SESSION['client']['customer']['delivery_address_id'];
        }
        
        if (is_numeric($invoices_address_id)) {
            $invoices = $Address->getDetail($invoices_address_id);
        } else {
            $invoices = false;
        }
        
        if (is_numeric($delivery_address_id)) {
            $delivery = $Address->getDetail($delivery_address_id);
        } else {
            $delivery = false;
        }
        
        
        $addr['invoices'] = $invoices;
        $addr['delivery'] = $delivery;
        $this->tpl->assign('ADDRESS', $addr);
        
        if (is_array($addr['invoices'])) {
            if ($addr['invoices']['line_2'] != '') $this->tpl->parse('content.invoices.line_2');
            if ($addr['invoices']['line_3'] != '') $this->tpl->parse('content.invoices.line_3');
            if ($this->GET['hide_button'] == 0) $this->tpl->parse('content.invoices.button');
            $this->tpl->parse('content.invoices');
        } else {
            if (isset($this->GET['hide_button']) && $this->GET['hide_button'] == 0) $this->tpl->parse('content.invoices_add_button');
        }
        
        if (is_array($addr['delivery'])) {
            if ($addr['delivery']['line_2'] != '') $this->tpl->parse('content.delivery.line_2');
            if ($addr['delivery']['line_3'] != '') $this->tpl->parse('content.delivery.line_3');
            if ($this->GET['hide_button'] == 0) $this->tpl->parse('content.delivery.button');
            $this->tpl->parse('content.delivery');
        } else {
            if (isset($this->GET['hide_button']) && $this->GET['hide_button'] == 0) $this->tpl->parse('content.delivery_add_button');
        }

        return true;
    }
}
