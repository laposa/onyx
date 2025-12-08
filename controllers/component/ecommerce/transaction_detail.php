<?php
/**
 * Transaction Detail
 *
 * Copyright (c) 2008-2020 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Component_Ecommerce_Transaction_Detail extends Onyx_Controller {
    
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
        if ($order_data['basket']['customer_id'] !== $_SESSION['client']['customer']['id'] &&  !Onyx_Bo_Authentication::getInstance()->isAuthenticated()) {
            msg('unauthorized access to view transaction detail', 'error');
        } else {
    
            if (isset($order_data['other_data']['po_number'])) {
                $this->tpl->assign('ORDER', $order_data);
                $this->tpl->parse('content.purchase_order');
                return true;
            }

            $transaction_list = $Transaction->getListForOrderId($order_id);

            if (is_array($transaction_list)) {
                foreach ($transaction_list as $transaction_detail) {

                    $type = 'default';	
                    if ($transaction_detail['type'] == 'stripe') {
                        $transaction_detail['pg_data']['id_truncated'] = substr($transaction_detail['pg_data']['id'], 0, 8) . '...' . substr($transaction_detail['pg_data']['id'], -8);
                        $transaction_detail['pg_data']['amount_total'] = $transaction_detail['pg_data']['amount_total'] / 100;
                        $type = 'stripe';
                    }

                    $this->tpl->assign('TRANSACTION', $transaction_detail);
                    $this->tpl->parse("content.transaction.$type");
                    $this->tpl->parse('content.transaction');
                }
            } else {
                msg("Order id $order_id has no transactions");
            }
        }

        return true;
    }
}
