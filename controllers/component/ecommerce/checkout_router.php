<?php
/**
 * Copyright (c) 2010-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/checkout.php');

class Onxshop_Controller_Component_Ecommerce_Checkout_Router extends Onxshop_Controller_Component_Ecommerce_Checkout {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_node.php');
        $node_conf = common_node::initConfiguration();
        
        if ($_SESSION['client']['customer']['id'] > 0) {
            onxshopGoTo("page/{$node_conf['id_map-checkout_delivery_options']}");
        } else {
            onxshopGoTo("page/{$node_conf['id_map-checkout_login']}");
        }
        
        return true;
    }
    
}
