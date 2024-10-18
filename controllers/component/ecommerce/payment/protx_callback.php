<?php
/**
 * ProtX aka SagePay
 * 
 * Copyright (c) 2009-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/payment/protx.php');

class Onyx_Controller_Component_Ecommerce_Payment_Protx_Callback extends Onyx_Controller_Component_Ecommerce_Payment_Protx {

    /**
     * main action
     */
     
    public function mainAction() {
        
        if (empty($_SESSION['client']['customer']['id'])) {
            msg('payment_callback_protx: You must be logged in.');
            onyxGoTo("/");
        }
        
        require_once('conf/payment/protx.php');
        $this->transactionPrepare();
        
        if (is_numeric($this->GET['order_id']) && $this->GET['crypt'] != '') {
        
            $this->paymentProcess($this->GET['order_id'], $this->GET['crypt']);
        }

        return true;
    }
    
}
