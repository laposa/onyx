<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */
 
class Onxshop_Controller_Component_Ecommerce_Product_Other_Data extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/ecommerce/ecommerce_product.php');
		$Product = new ecommerce_product();
		
		
		$product = $Product->detail($this->GET['id']);


		//other data
		$product['other_data'] = unserialize($product['other_data']);
	
		if (is_array($product['other_data'])) {
			foreach ($product['other_data'] as $key=>$value) {
				//format
				$key = preg_replace("/required_/","",$key);
				$key = preg_replace("/_/"," ",$key);
				$key = ucfirst($key);
	
				$note['key'] = $key;
				$note['value'] = nl2br($value);
				
				if (trim($note['value']) != '') {
					$this->tpl->assign('OTHER_DATA', $note);
					$this->tpl->parse('content.other_data.item');
					$show_other_data = 1;
				}
			}
			if (count($product['other_data']) > 0) $this->tpl->parse('content.other_data');
		}

		return true;
	}
}

