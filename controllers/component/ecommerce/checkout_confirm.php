<?php
/**
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */
 
require_once('controllers/component/ecommerce/checkout.php');

class Onxshop_Controller_Component_Ecommerce_Checkout_Confirm extends Onxshop_Controller_Component_Ecommerce_Checkout {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * get input
		 */
		 
		if (is_array($_POST['order'])) $order_data = $_POST['order'];
		else $order_data = array();
		
		/**
		 * get node configuration
		 */
		 
		require_once('models/common/common_node.php');
		$node_conf = common_node::initConfiguration();
		$this->tpl->assign('NODE_CONF', $node_conf);
		
		/**
		 * init basket
		 */
		 
		require_once('models/ecommerce/ecommerce_order.php');
		require_once('models/ecommerce/ecommerce_basket.php');
		$Order = new ecommerce_order();
		$Basket = new ecommerce_basket();
		
		$Order->setCacheable(false);
		$Basket->setCacheable(false);
		
		//temp
		if ($_POST['client']['customer']['currency_code']) {
			$currency_code = $_POST['client']['customer']['currency_code'];
		} else {
			$currency_code = $_SESSION['client']['customer']['currency_code'];
		}
				
		if ($_SESSION['client']['customer']['id'] > 0) {
		
			if (is_numeric($basket_id = $_SESSION['basket']['id'])) {
		
				//update basket
				$basket_detail = $Basket->detail($basket_id);
				$basket_detail['customer_id'] = $_SESSION['client']['customer']['id'];
				$Basket->update($basket_detail);
			
				//insert order
				if (isset($_POST['confirm'])) {	
		
					if ($_POST['order_terms_agreed'] == 'on') {		
						//insert only orders with some items in the basket :)
						$basket_content = $Basket->getContent($basket_id);
						if (count($basket_content['items']) > 0) {
							$order_data = $_POST['order'];

							$order_data['basket_id'] = $_SESSION['basket']['id'];
							$order_data['invoices_address_id'] = $_SESSION['client']['customer']['invoices_address_id'];
							$order_data['delivery_address_id'] = $_SESSION['client']['customer']['delivery_address_id'];
							$order_data['other_data']['delivery_options'] = $_SESSION['delivery_options'];
							$order_data['other_data']['promotion_code'] = $_SESSION['promotion_code'];
							$order_data['php_session_id'] = session_id();
							
							if ($inserted_order_id = $Order->insertOrder($order_data)) {
								$_SESSION['promotion_code'] = null;
								$_SESSION['basket']['id'] = null;
								
								//forward to payment page with pre-selected payment method
								//onxshopGoTo("page/" . $node_conf['id_map-payment'] . "?order_id=$inserted_order_id&selected_poyment_type={$order_data['payment_type']}");
								onxshopGoTo("page/" . $node_conf['id_map-payment'] . "?order_id=$inserted_order_id");
							}
						} else {
							msg("Can't insert an empty order.", 'error');
						}
					} else {
						msg("You must agree with our Terms & Conditions", 'error');
					}
				}
			}
		
			/**
			 * prepare list of payment options
			 */
			 
			require_once('models/ecommerce/ecommerce_transaction.php');
			$Transaction = new ecommerce_transaction();
			$transaction_type_allowed = $Transaction->conf['allowed_types'];
			foreach ($transaction_type_allowed as $type) {
				$this->tpl->parse("content.$type");
			}
		
			/**
			 * gift option
			 */

			if ($_SESSION['gift'] == 1) {
				$this->tpl->assign("GIFT", 1);
				$this->tpl->parse('content.gift');
			} else {
				$this->tpl->assign('GIFT', 0);
			}
			
			/**
			 * gift message
			 */
			
			if ($_SESSION['gift_message'] != '') {
				$this->tpl->assign("GIFT_MESSAGE", $_SESSION['gift_message']);
				$this->tpl->parse('content.gift_message');
			} else {
				$this->tpl->assign("GIFT_MESSAGE", '');
			}
			
			
		} else {
			//msg('You must be logged in first.', 'error');
			$_SESSION['to'] = "page/" . $node_conf['id_map-checkout'];
			onxshopGoTo("page/" . $node_conf['id_map-login']);
		}
		
		$this->tpl->assign("ORDER", $_POST['order']);

		return true;
	}
}
