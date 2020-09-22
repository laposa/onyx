<?php
/**
 * Basket detail for checkout
 *
 * Copyright (c) 2006-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/checkout.php');

class Onxshop_Controller_Component_Ecommerce_Checkout_Basket extends Onxshop_Controller_Component_Ecommerce_Checkout {
    
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
         * basket
         */
        if (is_numeric($_SESSION['basket']['id']) && $this->customerData()) {
            $_Onxshop_Request = new Onxshop_Request("component/ecommerce/basket_detail");
            $this->tpl->assign("BASKET_DETAIL", $_Onxshop_Request->getContent());
        }

        return true;
    }

    protected function customerData() {

        return $_SESSION['client']['customer']['id'] > 0 || $_SESSION['client']['customer']['guest'];
    }
}
    

