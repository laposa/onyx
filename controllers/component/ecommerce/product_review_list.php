<?php
/** 
 * Copyright (c) 2010-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/ecommerce/product_review.php');

class Onxshop_Controller_Component_Ecommerce_Product_Review_List extends Onxshop_Controller_Component_Ecommerce_Product_Review {

	/**
	 * custom comment action
	 */
	 
	public function customCommentAction($data, $options) {
		
		/**
		 * list comments
		 */
		
		$this->listComments($data['node_id'], $options);
		
	}
		
}
