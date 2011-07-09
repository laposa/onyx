<?php
/**
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/default.php');

class Onxshop_Controller_Bo_Node_Page_News extends Onxshop_Controller_Bo_Node_Default {

	/**
	 * pre
	 */
	 
	function pre() {
	
		if ($_POST['node']['component']['allow_comment'] == 'on') $_POST['node']['component']['allow_comment'] = 1;
		else $_POST['node']['component']['allow_comment'] = 0;
	}
	
	/**
	 * post
	 */
	 
	function post() {
	
		$this->node_data['component']['allow_comment']        = ($this->node_data['component']['allow_comment']) ? 'checked="checked"'      : '';
	}
}
