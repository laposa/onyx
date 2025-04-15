<?php
/** 
 * Copyright (c) 2005-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Component_Ecommerce_Order_List extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
        
        if (!empty($_SESSION['client']['customer']['id'])) {
            $customer_id = $_SESSION['client']['customer']['id'];
        } else if (Onyx_Bo_Authentication::getInstance()->isAuthenticated()) {
            $customer_id = $this->GET['customer_id'];
        } else {
            msg('orders: You must be logged in first.', 'error');
            onyxGoTo("/");
        }
        
        /**
         * include node configuration
         */
                
        require_once('models/common/common_node.php');
        $node_conf = common_node::initConfiguration();
        $this->tpl->assign('NODE_CONF', $node_conf);
        
        /**
         * Get the list
         */
        require_once('models/ecommerce/ecommerce_order.php');
        $Order = new ecommerce_order();
        $Order->setCacheable(false);
        
        $records = $Order->getOrderList($customer_id);
        
        /**
         * parse output
         */
        
        if (count($records) > 0) {
            
            foreach ($records as $item) {
                $item['order_created'] = strftime('%d/%m/%Y&nbsp;%H:%M', strtotime($item['order_created']));
                $item['status_title'] = $Order->getStatusTitle($item['order_status']);
                
                $this->tpl->assign('ITEM', $item);
                if ($Order->checkOrderStatusValidForPayment($item['order_status'])) $this->tpl->parse('content.orders.item.make_payment');
                $this->tpl->parse('content.orders.item');
            }
            $this->tpl->parse('content.orders');
        } else {
            $this->tpl->parse('content.noorders');
        }

        return true;
    }
}
