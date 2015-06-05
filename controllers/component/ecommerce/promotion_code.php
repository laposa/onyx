<?php
/**
 * Copyright (c) 2009-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Ecommerce_Promotion_code extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		/**
		 * find code
		 */
		 
		if ($_SESSION['promotion_code']) $code = $_SESSION['promotion_code'];
		else if (trim($_POST['promotion_code']) != '') $code = trim($_POST['promotion_code']);
		else $code = false;

		/**
		 * Check Actions
		 */

		if ($_POST['promotion_code_add'] && $code) {

			$_SESSION['promotion_code'] = $code;
			onxshopGoTo("/page/{$_SESSION['active_pages'][0]}");
			
		} else if ($_POST['promotion_code_remove']) {

			$_SESSION['promotion_code'] = false;
			onxshopGoTo("/page/{$_SESSION['active_pages'][0]}");
		}
		
		/**
		 * initialize
		 */

		require_once('models/ecommerce/ecommerce_promotion.php');
		$Promotion = new ecommerce_promotion();
		$Promotion->setCacheable(false);
		
		require_once('models/ecommerce/ecommerce_basket.php');
		$Basket = new ecommerce_basket();
		$Basket->setCacheable(false);
		
		/**
		 * basket detail
		 */
		 
		if (is_numeric($_SESSION['basket']['id'])) {
			$basket = $Basket->getFullDetail($_SESSION['basket']['id']);
			$Basket->calculateBasketSubTotals($basket, $this->isVatEligible($basket['customer_id']));
			$Basket->calculateBasketDiscount($basket, $_SESSION['promotion_code']);
			$Basket->saveDiscount($basket);
		}
		else $basket = false;
		
		/**
		 * Allow Guest Checkout
		 */
		if ($basket['customer_id'] == 0 && $_SESSION['client']['customer']['guest'] == 1) {
		 	$customer_email = $_SESSION['client']['customer']['email'];
		} else {
			$customer_email = '';
		}

		/**
		 * Display
		 */
		
		if ($basket && $promotion_code = $Promotion->checkCodeBeforeApply($code, $basket['customer_id'], $basket, $customer_email)) {
		
			$promotion_code['value'] = $code; 
			$this->tpl->assign('PROMOTION_CODE', $promotion_code); 
			if ($promotion_code['discount_percentage_value'] > 0) $this->tpl->parse('content.applied.discount_percentage_value');
			if ($promotion_code['discount_fixed_value'] > 0) $this->tpl->parse('content.applied.discount_fixed_value');
			if ($promotion_code['discount_free_delivery'] == 1) $this->tpl->parse('content.applied.discount_free_delivery');
			if ($promotion_code['free_promo_product']) $this->tpl->parse('content.applied.free_item_info');
			$this->tpl->parse('content.applied');
				
		} else {
			
			//remove code
			$code = false;
			
			$promotion_code = array();
			$promotion_code['value'] = $code;
			$this->tpl->assign('PROMOTION_CODE', $promotion_code);	
			$this->tpl->parse('content.enter');
		}
		
		/**
		 * Save to session
		 */
		 
		$_SESSION['promotion_code'] = $code;

		return true;
	}


	protected function isVatEligible($customer_id)
	{
		$result = true;

		if (is_numeric($_SESSION['client']['customer']['delivery_address_id'])) {
			
			require_once('models/ecommerce/ecommerce_order.php');
			$Order = new ecommerce_order();
			return $Order->isVatEligible($_SESSION['client']['customer']['delivery_address_id'], $customer_id);

		}

		return $result;
	}


}
