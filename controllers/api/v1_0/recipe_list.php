<?php
/** 
 * Copyright (c) 2012-2015 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api.php');
require_once('controllers/api/v1_0/recipe_detail.php');

class Onxshop_Controller_Api_v1_0_Recipe_List extends Onxshop_Controller_Api {

	/**
	 * get data
	 */
	
	public function getData() {
		
		/**
		 * initialize
		 */
		 
		require_once('models/ecommerce/ecommerce_recipe.php');
		$Recipe = new ecommerce_recipe();
		
		/**
		 * get recipe page posts
		 */
		
		$list = $Recipe->getFilteredRecipeList(false, false, false, false, 10000);

		$data = array();
		
		foreach($list as $item ) {
			$item = $this->formatItem($item);
			$data[] = $item;
		}
		
		return $data;
		
	}
	
	/**
	 * formatItem
	 */
	 
	public function formatItem($item) {
		
		return Onxshop_Controller_Api_v1_0_Recipe_Detail::formatItem($item);
		
	}
	
}
