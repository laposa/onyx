<?php
/**
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/comment_list.php');

class Onxshop_Controller_Bo_Component_Ecommerce_Recipe_Review_List extends Onxshop_Controller_Bo_Component_Comment_List {
	
	/**
	 * get list
	 */
	 
	public function getList() {
	
		require_once('models/ecommerce/ecommerce_recipe_review.php');
		$Review = new ecommerce_recipe_review();
		
		$list = $Review->getCommentList(false, 'id DESC');

		return $list;
	}
}

