<?php
/** 
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/ecommerce/recipe_review.php');

class Onxshop_Controller_Component_Ecommerce_Recipe_Review_Add extends Onxshop_Controller_Component_Ecommerce_Recipe_Review {

	/**
	 * custom comment action
	 */
	 
	public function customCommentAction($data, $options) {
		
		$this->displaySubmitForm($data, $options);
				
	}

}
