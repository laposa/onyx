<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Relation_Product_Related extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/ecommerce/ecommerce_product_to_product.php');
		require_once('models/ecommerce/ecommerce_product.php');
		
		$PtP = new ecommerce_product_to_product();
		$Product = new ecommerce_product();
		
		$product_id = $this->GET['id'];
		$PtP->set('product_id', $product_id);
		
		/**
		 * saving
		 */
		 
		if (is_array($_POST['product_related'])) {
		
			$current = $PtP->listing("product_id = $product_id");
		
			foreach ($current as $c) {
				$PtP->delete($c['id']);
			}
		
			foreach ($_POST['product_related'] as $to_id) {
				if (is_numeric($to_id)) {
					$PtP->set("related_product_id", $to_id);
					$PtP->insert();
					//echo $this->GET['id'] . $to_id;
				}
			}
		}
		
		/**
		 * listing
		 */
		 
		$current = $PtP->listing("product_id = $product_id");
		foreach ($current as $c) {
			$detail = $Product->detail($c['related_product_id']);
			if ($detail['publish'] == 0) $detail['class'] = "class='disabled'";
			$this->tpl->assign("CURRENT", $detail);
			$this->tpl->parse("content.ptn");
		}

		return true;
	}
}

