<?php
/**
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Ecommerce_Promotion_code extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {

		/**
		 * Load from session
		 */

		$basket_id = $_SESSION['basket']['id'];
		
		/**
		 * find code
		 */
		 
		if ($_SESSION['promotion_code']) $code = $_SESSION['promotion_code'];
		else if (trim($_POST['promotion_code']) != '') $code = trim($_POST['promotion_code']);
		else $code = false;
		
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
		 
		if (is_numeric($basket_id)) $basket_data = $Basket->getDetail($basket_id);
		else $basket_data = false;
		
		/**
		 * Check Actions
		 */

		if ($_POST['promotion_code_add'] && $code) {
			
			//update basket
			if ($discount_net = $Promotion->applyPromotionCodeToBasket($code, $basket_data)) {
				
				$Basket->applyDiscount($basket_id, $discount_net);
				msg("Promotion code {$code} applied");
		
			}
		} else if ($_POST['promotion_code_remove']) {
			msg("Code {$code} removed");
			$code = false;
			//update basket
			$Basket->applyDiscount($basket_id, 0);
		}
		
		/**
		 * Display
		 */
		
		if ($basket_data && $promotion_code = $Promotion->checkCodeBeforeApply($code, $basket_data['customer_id'], $basket_data)) {
		
			$promotion_code['value'] = $code; 
			$this->tpl->assign('PROMOTION_CODE', $promotion_code); 
			if ($promotion_code['discount_percentage_value'] > 0) $this->tpl->parse('content.applied.discount_percentage_value');
			if ($promotion_code['discount_fixed_value'] > 0) $this->tpl->parse('content.applied.discount_fixed_value');
			if ($promotion_code['discount_free_delivery'] == 1) $this->tpl->parse('content.applied.discount_free_delivery');
			$this->tpl->parse('content.applied');
				
		} else {
			
			//remove code
			$code = false;
			
			$promotion_code = array();
			$promotion_code['value'] = $code;
		
			$this->tpl->assign('PROMOTION_CODE', $promotion_code);	
		
			$this->tpl->parse('content.enter');
		
			//update basket
			$Basket->applyDiscount($basket_id, 0);
		}
		
		/**
		 * Save to session
		 */
		 
		$_SESSION['promotion_code'] = $code;

		return true;
	}
}
