<?php
/** 
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/comment.php');

class Onxshop_Controller_Component_Comment_Add extends Onxshop_Controller_Component_Comment {

	/**
	 * custom comment action
	 */
	 
	public function customCommentAction($data, $options) {
	
		$data['rating'] = 0;
		$this->displaySubmitForm($data, $options);
		
	}
	
}
