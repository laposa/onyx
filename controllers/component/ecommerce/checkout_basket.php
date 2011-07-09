<?php
/**
 * Basket detail for checkout
 *
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
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
		if (is_numeric($_SESSION['basket']['id']) && $_SESSION['client']['customer']['id'] > 0) {
			$_nSite = new nSite("component/ecommerce/basket_detail~id={$_SESSION['basket']['id']}~");
			$this->tpl->assign("BASKET_DETAIL", $_nSite->getContent());
		}

		return true;
	}
}
	

