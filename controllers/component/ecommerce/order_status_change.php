<?php
/** 
 * Copyright (c) 2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Ecommerce_Order_Status_Change extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        if (!is_numeric($this->GET['order_id']) || !is_numeric($this->GET['status'])) {
            msg("Onxshop_Controller_Component_Ecommerce_Order_Status_Change: order_id or status isn't numeric");
            return false;
        }
        
        $order_id = $this->GET['order_id'];
        $status = $this->GET['status'];
        
        /**
         * standard action
         */
         
        $this->standardStatusChangeAction($order_id, $status);
        
        /**
         * custom action
         */
        
        $this->customStatusChangeAction($order_id, $status);

        return true;
        
    }
    
    /**
     * standardStatusChangeAction
     */
     
    public function standardStatusChangeAction($order_id, $status) {
        
        if (!is_numeric($order_id) || !is_numeric($status)) return false;
        
        if ($status == 1) {
            
            $this->giftVoucherAction($order_id, $status);
            $this->referrerVoucherAction($order_id, $status);
            
        }
        
        return true;
            
    }
    
    /**
     * customStatusChangeAction
     */
     
    public function customStatusChangeAction($order_id, $status) {
        
        if (!is_numeric($order_id) || !is_numeric($status)) return false;
        
        /**
         * add your action here, e.g. send to warehouse
         */
        /*
        if ($status == 1) {
            $_Onxshop_Request = new Onxshop_Request("component/ecommerce/your_warehouse_integration_controller~order_id={$order_id}~");
        }*/
        
        return true;
        
    }
    
    /**
     * giftVoucherAction
     */
     
    public function giftVoucherAction($order_id, $status) {
        
        if (!is_numeric($order_id) || !is_numeric($status)) return false;

        /**
         * try to generate gift voucher
         */
         
        $_Onxshop_Request = new Onxshop_Request("component/ecommerce/gift_voucher_generate~order_id={$order_id}~");
        
        return true;         
        
    }
    /**
     * Reward inviting user if referrer voucher code has been used
     */
     
    public function referrerVoucherAction($order_id, $status) {
        
        if (!is_numeric($order_id) || !is_numeric($status)) return false;

        require_once "models/ecommerce/ecommerce_promotion.php";

        $Promotion = new ecommerce_promotion();
        $Promotion->rewardInvitingUser($order_id);

        return true;         
        
    }
}
