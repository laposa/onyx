<?php
/** 
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/ecommerce/review.php');

class Onxshop_Controller_Component_Ecommerce_Review_Add extends Onxshop_Controller_Component_Ecommerce_Review {

	/**
	 * custom comment action
	 */
	 
	public function customCommentAction($data, $options) {
		
		$this->displaySubmitForm($data, $options);
				
	}
	
	

}
