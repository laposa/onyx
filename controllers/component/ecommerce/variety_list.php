<?php
/**
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Ecommerce_Variety_List extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	function mainAction() {
		
		/**
		 * include node configuration
		 */
				
		require_once('models/common/common_node.php');
		$node_conf = common_node::initConfiguration();
		$this->tpl->assign('NODE_CONF', $node_conf);
		
		/**
		 * create product object
		 */
		 
		require_once('models/ecommerce/ecommerce_product.php');
		$Product = new ecommerce_product();
		
		$product_id = $this->GET['product_id'];
		
		if ($variety_list = $Product->getProductVarietyList($product_id)) {
		
			/**
			 * variety list
			 */
			 
			foreach ($variety_list as $key=>$variety) {
			
				if ($variety['publish'] == 1) {
					//mark first variety checked
					if ($key == 0) $variety['checked'] = "checked='checked'";
					$this->tpl->assign('VARIETY', $variety);
					$Price = new nSite("component/ecommerce/price~product_variety_id={$variety['id']}~");
					$this->tpl->assign("PRICE", $Price->getContent());
						
					if ($variety['stock'] > 0 ) {
						//FIXME: ean13 is temporarily used as variety subtitle
						if (trim($variety['ean13']) != '') $this->tpl->parse("content.variety.item.onstock.subtitle");
						$this->tpl->parse("content.variety.item.onstock");
					} else if ($variety['stock'] < 0) {
						$this->tpl->parse("content.variety.item.special");
					} else {
						$this->tpl->parse("content.variety.item.outofstock");
					}
					
					$this->tpl->parse("content.variety.item");
				}			
			}
			
			/**
			 * product options
			 */

			$ProductOptions = new nSite("component/ecommerce/product_options~id={$product_id}~");
			$this->tpl->assign('PRODUCT_OPTIONS', $ProductOptions->getContent());
			
			$this->tpl->parse('content.variety');
			
			return true;
		}
		
	}
}
