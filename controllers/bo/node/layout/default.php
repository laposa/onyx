<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/default.php');

class Onxshop_Controller_Bo_Node_Layout_Default extends Onxshop_Controller_Bo_Node_Default {

	/**
	 * pre action
	 */
	 
	function pre() {
		if ($_POST['component']['display_title'] == 'on') $_POST['component']['display_title'] = 1;
		else $_POST['component']['display_title'] = 0;
	}
	
	/**
	 * post action
	 */
	 
	function post() {
		$this->component_data['display_title']        = ($this->component_data['display_title']) ? 'checked="checked"'      : '';
	}
}

