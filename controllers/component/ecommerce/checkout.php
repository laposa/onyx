<?php
/**
 * Checkout controller
 *
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Ecommerce_Checkout extends Onxshop_Controller {

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
					
						//insert only orders having some items in the basket :)
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

			if ($order_data['other_data']['gift'] == 1) {
				$this->tpl->assign("CHECKED_order-other_data-gift", "checked='checked'");
				$this->tpl->assign('GIFT_DISPLAY', 'block');
			} else {
				$this->tpl->assign('GIFT_DISPLAY', 'none');
			}
			
		} else {
			//msg('You must be logged in first.', 'error');
			$_SESSION['to'] = "page/" . $node_conf['id_map-checkout'];
			onxshopGoTo("page/" . $node_conf['id_map-login']);
		}
		
		$this->tpl->assign("ORDER", $_POST['order']);

		return true;
	}
	
	/**
	 * check for virtual product only
	 */
	 
	public function isBasketVirtualProductOnly() {
		
		if ($gift_voucher_product_id = $this->getGiftVoucherProductId()) {
		
			if (is_numeric($basket_id = $_SESSION['basket']['id'])) {
				
				require_once('models/ecommerce/ecommerce_basket.php');
				$Basket = new ecommerce_basket();
				
				$basket_content = $Basket->getContent($basket_id);
				
				$voucher_basket_items = $this->getVoucherBasketItems($basket_content['items'], $gift_voucher_product_id);
				
				if (!$voucher_basket_items) return false;
				if (count($voucher_basket_items) == count($basket_content['items'])) return true;
				return false;
				
			} else {
			
				return false;
			
			}

		} else {
		
			return false;
		
		}
	}
	
	/**
	 * getGiftVoucherProductId
	 */
	 
	public function getGiftVoucherProductId() {
		
		/**
		 * get product conf
		 */
		 
		require_once('models/ecommerce/ecommerce_product.php');
		$ecommerce_product_conf = ecommerce_product::initConfiguration();
		
		/**
		 * check gift voucher product ID is set
		 */
		 
		if (!is_numeric($ecommerce_product_conf['gift_voucher_product_id']) || $ecommerce_product_conf['gift_voucher_product_id']  == 0) {
			
			return false;
		}
		
		return $ecommerce_product_conf['gift_voucher_product_id'];
	}
	
	/**
	 * getVoucherBasketItems
	 */
	 
	public function getVoucherBasketItems($basket_items, $gift_voucher_product_id) {
		
		if (!is_array($basket_items)) return false;
		if (!is_numeric($gift_voucher_product_id)) return false;
		
		$voucher_basket_items = array();
		
		foreach ($basket_items as $basket_item) {
			
			if ($basket_item['product']['id'] == $gift_voucher_product_id) {
				$voucher_basket_items[] = $basket_item;
			}
			
		}
		
		if (count($voucher_basket_items) > 0) return $voucher_basket_items;
		else return false;
		
	}
}
