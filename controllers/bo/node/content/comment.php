<?php
/** 
 * Copyright (c) 2006-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');

class Onxshop_Controller_Bo_Node_Content_Comment extends Onxshop_Controller_Bo_Node_Content_Default {
	
	/**
	 * pre action
	 */
	 
	function pre() {
		
		parent::pre();
		
		if ($_POST['node']['component']['allow_anonymouse_submit'] == 'on') $_POST['node']['component']['allow_anonymouse_submit'] = 1;
		else $_POST['node']['component']['allow_anonymouse_submit'] = 0;
	}
	
	/**
	 * post action
	 */
	 
	function post() {
	
		parent::post();
		
		$this->node_data['component']['allow_anonymouse_submit'] = ($this->node_data['component']['allow_anonymouse_submit']) ? 'checked="checked"' : '';
	}
}
