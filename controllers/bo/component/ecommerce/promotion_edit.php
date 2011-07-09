<?php
/**
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Promotion_Edit extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/ecommerce/ecommerce_promotion.php');
		$Promotion = new ecommerce_promotion();
		
		/**
		 * Save on request
		 */
		if ($_POST['save']) {
			$promotion_data = $_POST['promotion'];
			
			if ($promotion_data['publish'] == 'on' || $promotion_data['publish'] == 1) $promotion_data['publish'] = 1;
			else $promotion_data['publish'] = 0;
			
			if ($promotion_data['discount_free_delivery'] == 'on' || $promotion_data['discount_free_delivery'] == 1) $promotion_data['discount_free_delivery'] = 1;
			else $promotion_data['discount_free_delivery'] = 0;
		
			if ($Promotion->updatePromotion($promotion_data)) msg ("Promotion id={$promotion_data['id']} updated");
			else msg('Update failed', 'error');
			
		}
		
		/**
		 * Display Detail
		 */
		$promotion_detail = $Promotion->getDetail($this->GET['id']);

		if (count($promotion_detail) > 0) {
			if ($promotion_detail['publish'] == 1) $promotion_detail['publish_check'] = 'checked="checked"';
			else $promotion_detail['publish_check'] = '';
			
			if ($promotion_detail['discount_free_delivery'] == 1) $promotion_detail['discount_free_delivery_check'] = 'checked="checked"';
			else $promotion_detail['discount_free_delivery_check'] = '';
			
			$this->tpl->assign('PROMOTION', $promotion_detail);
		}

		return true;
	}
}

