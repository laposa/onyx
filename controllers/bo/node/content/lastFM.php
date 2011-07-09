<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/default.php');

class Onxshop_Controller_Bo_Node_Content_LastFM extends Onxshop_Controller_Bo_Node_Default {

	/**
	 * pre action
	 */

	function pre() {
		$_POST['component']['fm_user'] = trim($_POST['component']['fm_user']);
	}
}

