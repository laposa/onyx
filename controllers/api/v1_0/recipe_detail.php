<?php
/** 
 * Copyright (c) 2013-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api.php');
require_once('controllers/api/v1_0/recipe_list.php');

class Onxshop_Controller_Api_v1_0_Recipe_Detail extends Onxshop_Controller_Api {

	/**
	 * get data
	 */
	
	public function getData() {
		
		/**
		 * input
		 */
		 
		if (is_numeric($this->GET['recipe_id'])) $recipe_id = $this->GET['recipe_id'];
		else {
			//msg("missing recipe_id", 'error');
			$data = array();
			$data['message'] = "missing recipe_id";
			$data['status'] = 400;
			
			return $data;
			
		}
		
		/**
		 * initialize
		 */
		 
		require_once('models/ecommerce/ecommerce_recipe.php');
		$Recipe = new ecommerce_recipe();
		
		/**
		 * get recipe page posts
		 */
		
		$list = $Recipe->getFilteredRecipeList(false, $recipe_id);
		
		$data = array();
		
		foreach($list[0] as $item ) {
			
			if ($item['publish'] == 1) {
				
				$item = Onxshop_Controller_Api_v1_0_Recipe_List::formatItem($item);
				
				$data[] = $item;
			}
			
		}
		
		return $data;
		
	}
	
}
