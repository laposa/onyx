<?php
/** 
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
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
		
		$products = $Product->getProductList();
		$products = php_multisort($products, array(array("key" => "name", "sort" => "asc")));
		$this->parseIngredients($products, false, 'head.product');

		/**
		 * render template
		 */
		
		foreach ($products as $product) {
			$this->tpl->assign("PRODUCT", $product);
			foreach ($product['variety'] as $variety) {
				$variety['selected'] = $variety['id'] == $ingredient['product_variety_id'] ? 'selected="selected"' : '';
				$this->tpl->assign("VARIETY", $variety);
				$this->tpl->parse("content.template.product");
			}
		}
		$this->tpl->parse("content.template");

		/**
		 * listing
		 */
		 
		$current = $Ingredients->listing("recipe_id = $recipe_id");

		foreach ($current as $ingredient) {
			if ($detail['publish'] == 0) $detail['class'] = "class='disabled'";
			$this->tpl->assign("ITEM", $ingredient);
			$this->parseIngredients($products, $ingredient['product_variety_id']);
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

	public function parseIngredients(&$products, $active, $block = 'content.item.product') {

		foreach ($products as $product) {
			$this->tpl->assign("PRODUCT", $product);
			foreach ($product['variety'] as $variety) {
			
				if ($product['publish'] == 0 || $variety['publish'] == 0) $this->tpl->assign('CSS_CLASS', 'disabled');
				else $this->tpl->assign('CSS_CLASS', '');
				
				$variety['selected'] = $variety['id'] == $active ? 'selected="selected"' : '';
				$this->tpl->assign("VARIETY", $variety);
				$this->tpl->parse($block);
			
			}
		}
	}

}

