<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/default.php');

class Onxshop_Controller_Bo_Node_Content_Comment extends Onxshop_Controller_Bo_Node_Default {
	
	/**
	 * pre action
	 */
	 
	function pre() {
		if ($_POST['node']['component']['allow_anonymouse_submit'] == 'on') $_POST['node']['component']['allow_anonymouse_submit'] = 1;
		else $_POST['node']['component']['allow_anonymouse_submit'] = 0;
	}
	
	/**
	 * post action
	 */
	 
	function post() {
		$this->node_data['component']['allow_anonymouse_submit'] = ($this->node_data['component']['allow_anonymouse_submit']) ? 'checked="checked"' : '';
	}
}
