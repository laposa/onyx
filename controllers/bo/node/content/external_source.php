<?php
/**
 * Copyright (c) 2007-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/default.php');

class Onxshop_Controller_Bo_Node_Content_External_source extends Onxshop_Controller_Bo_Node_Default {

	/**
	 * pre action
	 */

	function pre() {
		if ($_POST['component']['image'] == 'on') $_POST['component']['image'] = 1;
		else $_POST['component']['image'] = 0;
	}

	/**
	 * post action
	 */

	function post() {
		$this->node_data['component']['image']        = ($this->node_data['component']['image']) ? 'checked="checked"'      : '';
	}
}

