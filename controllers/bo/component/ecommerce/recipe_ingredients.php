<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Recipe_Ingredients extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/ecommerce/ecommerce_recipe_ingredients.php');
		require_once('models/ecommerce/ecommerce_product.php');
		
		$Ingredients = new ecommerce_recipe_ingredients();
		$Product = new ecommerce_product();
		
		$recipe_id = $this->GET['node_id'];
		
		$ingredients = array();
		$ingredients['recipe_id'] = $recipe_id;
		
		/**
		 * saving
		 */
		 
		if (is_array($_POST['ingredients'])) {

			$current = $Ingredients->listing("recipe_id = $recipe_id");

			foreach ($current as $c) {
				$Ingredients->delete($c['id']);
			}
		
			foreach ($_POST['ingredients'] as $product_id => $item) {
				if (is_numeric($product_id)) {
					$ingredients['product_id'] = $product_id;
					$ingredients['quantity'] = $item['quantity'];
					$ingredients['units'] = $item['units'];
					$ingredients['notes'] = $item['notes'];
					$Ingredients->insert($ingredients);
				}
			}
		}
		
		/**
		 * get units
		 */
		$units = $Ingredients->getUnits();
		$this->parseUnits($units, false, 'head.unit');

		/**
		 * listing
		 */
		 
		$current = $Ingredients->listing("recipe_id = $recipe_id");
		foreach ($current as $ingredient) {
			$detail = $Product->detail($ingredient['product_id']);
			if ($detail['publish'] == 0) $detail['class'] = "class='disabled'";
			$this->tpl->assign("PRODUCT", $detail);
			$this->tpl->assign("ITEM", $ingredient);
			$this->parseUnits($units, $ingredient['units']);
			$this->tpl->parse("content.item");
		}

		return true;
	}

	public function parseUnits(&$units, $active, $block = 'content.item.unit') {
		foreach ($units as $unit) {
			if ($active == $unit['id']) $unit['selected'] = 'selected="selected"';
			$this->tpl->assign("UNIT", $unit);
			$this->tpl->parse($block);
		}
	}
}

