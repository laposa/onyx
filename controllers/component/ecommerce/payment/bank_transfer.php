<?php
/** 
 * Copyright (c) 2012 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/payment.php');

class Onyx_Controller_Component_Ecommerce_Payment_Bank_Transfer extends Onyx_Controller_Component_Ecommerce_Payment {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('conf/payment/on_delivery.php');
        $this->transactionPrepare();
        
        if (is_numeric($this->GET['order_id'])) $order_id = $this->GET['order_id'];
        else return false;
        
        $order_data = $this->Transaction->getOrderDetail($order_id);
    
        if (!is_array($order_data)) {
            msg("Cannot find order detail", 'error');
            return false;
        }
        
        return true;
    }
    
    
}