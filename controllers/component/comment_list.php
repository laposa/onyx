<?php
/** 
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/comment.php');

class Onxshop_Controller_Component_Comment_List extends Onxshop_Controller_Component_Comment {
	
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
