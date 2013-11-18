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
			
			if ($promotion_data['limit_to_first_order'] == 'on' || $promotion_data['limit_to_first_order'] == 1) $promotion_data['limit_to_first_order'] = 1;
			else $promotion_data['limit_to_first_order'] = 0;

			if ($promotion_data['discount_free_delivery'] == 'on' || $promotion_data['discount_free_delivery'] == 1) $promotion_data['discount_free_delivery'] = 1;
			else $promotion_data['discount_free_delivery'] = 0;

			if (!is_numeric($promotion_data['limit_cumulative_discount'])) $promotion_data['limit_cumulative_discount'] = 0;

			if (is_array($promotion_data['limit_list_products'])) {
				foreach ($promotion_data['limit_list_products'] as $product_id) {
					if (is_numeric($product_id)) $limited_ids[] = $product_id;
				}
				$promotion_data['limit_list_products'] = implode(",", $limited_ids);
			}
		
			if ($Promotion->updatePromotion($promotion_data)) msg("Promotion id={$promotion_data['id']} updated");
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

			if ($promotion_detail['limit_to_first_order'] == 1) $promotion_detail['limit_to_first_order_check'] = 'checked="checked"';
			else $promotion_detail['limit_to_first_order_check'] = '';

			//find product in the node
			$limited_ids = explode(",", $promotion_detail['limit_list_products']);
			if (is_array($limited_ids)) {

				require_once('models/ecommerce/ecommerce_product.php');
				$Product = new ecommerce_product();
				foreach ($limited_ids as $product_id) {
					//find product in the node
					if (is_numeric($product_id)) {
						$detail = $Product->detail($product_id);
						if ($detail['publish'] == 0) $detail['class'] = 'notpublic';
						$this->tpl->assign('CURRENT', $detail);
						$this->tpl->parse('content.item');
					}
				}
			}
			
			$this->tpl->assign('PROMOTION', $promotion_detail);
		}

		return true;
	}
}

