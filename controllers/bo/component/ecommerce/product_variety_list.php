<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Product_Variety_List extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * include variety confg
		 */
		require_once('models/ecommerce/ecommerce_product_variety.php');
		$variety_conf = ecommerce_product_variety::initConfiguration();
		$this->tpl->assign('VARIETY_CONF',$variety_conf);
		
		/**
		 * product
		 */
		require_once('models/ecommerce/ecommerce_product.php');
		$Product = new ecommerce_product();
		
		$product = $Product->getProductDetail($this->GET['id']);
		
		if (is_array($product['variety'])) {
			foreach ($product['variety'] as $variety) {
				if  ($variety['publish'] == 0) $this->tpl->assign('DISABLED', 'disabled');
				else $this->tpl->assign('DISABLED', '');
				
				$Image = new Onxshop_Request("component/image&relation=product_variety&node_id={$variety['id']}");
				$this->tpl->assign('IMAGE', $Image->getContent());
				$this->tpl->assign('VARIETY', $variety);
				$this->tpl->parse('content.variety');
			}
		} else {
			msg('This product has no variety.');
		}

		return true;
	}
}
