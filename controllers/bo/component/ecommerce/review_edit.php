<?php
/**
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/comment_edit.php');

class Onxshop_Controller_Bo_Component_Ecommerce_Review_Edit extends Onxshop_Controller_Bo_Component_Comment_Edit {

	/**
	 * initialize comment
	 */
	 
	public function initializeComment() {
		
		require_once('models/ecommerce/ecommerce_product_review.php');
		$Comment = new ecommerce_product_review();
		
		return $Comment;
	}

	/**
	 * save
	 */
	 
	public function saveComment($comment_data) {
	
		if ($this->Comment->updateComment($comment_data)) msg("Review id={$comment_data['id']} updated");
		else msg("Review id={$comment_data['id']} Update failed", 'error');

	}
	
}

