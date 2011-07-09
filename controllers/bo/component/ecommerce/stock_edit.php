<?php
/** 
 * Copyright (c) 2008-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Stock_Edit extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/ecommerce/ecommerce_product_variety.php');
		require_once('models/ecommerce/ecommerce_product.php');
		$Product_variety = new ecommerce_product_variety();
		$Product = new ecommerce_product();
		
		$this->tpl->assign('VARIETY_CONF',$Product_variety->conf);
		
		$Product_variety->set('id', $this->GET['id']);
		
		if ($_POST['save'] == 'variety') {
			
			if($id = $Product_variety->updateVariety($_POST['product']['variety'])) {
				msg("Product variety updated.");
				/*onxshopGoTo($_SESSION['last_diff'], 2);*/
			} else {
				msg ("Can't add the product variety, is you product SKU unique?");
			}
		}
		
		$variety = $Product_variety->getVarietyDetail($this->GET['id']);
		$variety['publish'] = ($variety['publish'] == 1) ? 'checked="checked" ' : '';
		
		$p = $Product->detail($variety['product_id']);
		
		$p['variety'] = $variety;
		
		$this->tpl->assign('PRODUCT', $p);

		return true;
	}
}
