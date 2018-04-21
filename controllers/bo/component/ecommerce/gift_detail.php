<?php
/**
 * Gift Detail
 *
 * Copyright (c) 2009-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Gift_Detail extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * Input data
         */
        
        if (is_numeric($this->GET['order_id'])) $order_id = $this->GET['order_id'];
        else return false;
        
        /**
         * Create objects
         */
         
        require_once('models/ecommerce/ecommerce_order.php');
        $Order = new ecommerce_order();
        
        /**
         * Get details for order
         */
         
        if (is_numeric($order_id)) $order_data = $Order->getOrder($order_id);
        
        if ($order_data['other_data']['gift'] == 1 || 
            strlen($order_data['other_data']['gift_message']) > 0) {

            $this->tpl->assign("ORDER", $order_data);
            $this->tpl->parse('content.option');
        } else {
            $this->tpl->parse('content.nooption');
        }

        return true;
    }
}
