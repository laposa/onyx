<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Price_Add extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/ecommerce/ecommerce_price.php');
		$Price = new ecommerce_price();
		
		if ($Price->conf['backoffice_with_vat']) $this->tpl->assign('VAT_NOTE', I18N_PRICE_INC_VAT);
		else $this->tpl->assign('VAT_NOTE', I18N_PRICE_EX_VAT);
		
		// type
		$types = $Price->getTypes();
		
		foreach ($types as $type) {
			$this->tpl->assign('TYPE', $type);
			if ($type == $_POST['price']['type']) $this->tpl->assign('SELECTED', "selected='selected'");
			else $this->tpl->assign('SELECTED', "");
			$this->tpl->parse('content.type');
		}
		
		
		if ($_POST['save']) {
			$price_data = $_POST['price'];
			
			// FIXME: form_currency_inline hack
			$price_data['currency_code'] = $_POST['client']['customer']['currency_code'];
			
			if($id = $Price->priceInsert($price_data)) {
				msg("Price added.");
				require_once('models/ecommerce/ecommerce_product_variety.php');
				$Product_variety = new ecommerce_product_variety();
				$pd = $Product_variety->detail($price_data['product_variety_id']);
			} else {
				return false;
			}
		}
		
		$this->tpl->assign('PRICE', $_POST['price']);

		return true;
	}
}
