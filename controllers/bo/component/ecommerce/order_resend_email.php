<?php
/**
 * Resend Order Confirmation Email
 *
 * Copyright (c) 2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */
require_once('models/ecommerce/ecommerce_order.php');

class Onxshop_Controller_Bo_Component_Ecommerce_Order_Resend_Email extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {

        return true;


        $order_id = (int) $this->GET['order_id'];
        if ($order_id == 0) return true;

        $Order = new ecommerce_order();
        $Order->setCacheable(false);
        $order_data = $Order->getFullDetail($order_id);

        if ($order_data['transaction']['id'] > 0) {

            $this->tpl->parse('content.button');

            if ($this->GET['resend_email'] == 'yes') {

                // implement in your installation

                onxshopGoto("/backoffice/orders/{$order_data['id']}/detail");

            }
        }

        return true;
    }

}       
