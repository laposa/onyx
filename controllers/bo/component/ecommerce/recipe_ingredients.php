<?php
/** 
 * Copyright (c) 2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/ecommerce/ecommerce_recipe_ingredients.php');
require_once('models/ecommerce/ecommerce_product.php');

class Onxshop_Controller_Bo_Component_Ecommerce_Recipe_Ingredients extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		$Ingredients = new ecommerce_recipe_ingredients();
		$Product = new ecommerce_product();
		
		$recipe_id = $this->GET['recipe_id'];
		
		$ingredients = array();
		$ingredients['recipe_id'] = $recipe_id;
		
		/**
		 * saving
		 */
		 
		if (is_array($_POST['ingredients'])) {

			$current = $Ingredients->listing("recipe_id = $recipe_id");
			$keep = array_keys($_POST['ingredients']);

			foreach ($current as $c) {
				if (!in_array($c['id'], $keep)) $Ingredients->delete($c['id']);
			}
		
			foreach ($_POST['ingredients'] as $ingredient_id => $item) {
				if (is_numeric($ingredient_id)) {
					$ingredients['id'] = $ingredient_id;
					$ingredients['product_variety_id'] = $item['product_variety_id'];
					$ingredients['quantity'] = $item['quantity'];
					$ingredients['units'] = $item['units'];
					$ingredients['notes'] = $item['notes'];
					$ingredients['group_title'] = $item['group_title'];
					$Ingredients->update($ingredients);
				} else {
					unset($ingredients['id']);
					$ingredients['product_variety_id'] = $item['product_variety_id'];
					$ingredients['quantity'] = $item['quantity'];
					$ingredients['units'] = $item['units'];
					$ingredients['notes'] = $item['notes'];
					$ingredients['group_title'] = $item['group_title'];
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
		 * get ingredient list (products)
		 */
		
		$products = $Product->getProductListForDropdown();
		$this->parseIngredients($products);

		/**
		 * listing
		 */
		 
		$current = $Ingredients->listing("recipe_id = $recipe_id");

		foreach ($current as $ingredient) {
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

	public function parseIngredients(&$products) {

		$max = count($products) - 1;

		foreach ($products as $i => $product) {

			$json = json_encode(array(
				'id' => $product['id'],
				'sku' => $product['sku'],
				'name' => $product['product_name'] . " - " . $product['variety_name'],
				'publish' => ($product['variety_publish'] == 0 || $product['variety_publish'] == 0) ? 0: 1
			));
			if ($i < $max) $json .= ",";
			$this->tpl->assign("JSON", $json);
			$this->tpl->parse("head.product");

		}
	}

}

