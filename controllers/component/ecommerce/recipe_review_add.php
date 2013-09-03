<?php
/** 
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/ecommerce/recipe_review.php');
require_once('models/client/client_action.php');

class Onxshop_Controller_Component_Ecommerce_Recipe_Review_Add extends Onxshop_Controller_Component_Ecommerce_Recipe_Review {

	/**
	 * custom comment action
	 */
	 
	public function customCommentAction($data, $options) {
		
		$this->displaySubmitForm($data, $options);
	}

	public function insertComment($data, $options = false) {

		$result = parent::insertComment($data, $options);

		if ($result && client_action::hasOpenGraphStory('comment_on', 'recipe')) {
			$request = new Onxshop_Request("component/client/facebook_story_create~" 
				. "action=comment_on"
				. ":object=recipe"
				. ":node_id=" . $_SESSION['active_pages'][0]
				. "~");
		}

		return $result;
	}

}
