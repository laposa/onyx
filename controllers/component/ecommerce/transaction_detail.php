<?php
/**
 * Transaction Detail
 *
 * Copyright (c) 2008-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Ecommerce_Transaction_Detail extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
        /**
         * Input data
         */
        
        if (is_numeric($this->GET['id'])) $order_id = $this->GET['id'];
        else return false;
        
        /**
         * Initialize objects
         */
        require_once('models/ecommerce/ecommerce_order.php');
        $Order = new ecommerce_order();
        $Order->setCacheable(false);
        
        require_once('models/ecommerce/ecommerce_transaction.php');
        $Transaction = new ecommerce_transaction();
        
        /**
         * Get details for order to be able make a security check
         */
        if (is_numeric($order_id)) $order_data = $Order->getOrder($order_id);
        
        //security check of owner
        if ($order_data['basket']['customer_id'] !== $_SESSION['client']['customer']['id'] &&  !Onxshop_Bo_Authentication::getInstance()->isAuthenticated()) {
            msg('unauthorized access to view transaction detail', 'error');
        } else {
            $transaction_list = $Transaction->getListForOrderId($order_id);
            
            //print_r($transaction_list);
            if (is_array($transaction_list)) {
                foreach ($transaction_list as $transaction_detail) {
                    $this->tpl->assign('TRANSACTION', $transaction_detail);
                    $this->tpl->parse('content.transaction');
                }
            } else {
                msg("Order id $order_id has no transactions");
            }
        }

        return true;
    }
}
