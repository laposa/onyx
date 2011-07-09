<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/default.php');

class Onxshop_Controller_Bo_Node_Content_HTML extends Onxshop_Controller_Bo_Node_Default {

	/**
	 * post action
	 */

	function post() {
		$this->node_data['body_attributes'] = htmlspecialchars($this->node_data['body_attributes'], ENT_QUOTES, 'UTF-8');
		
		if ($this->node_data['content'] == '' && !$_POST['save']) $this->tpl->parse('content.empty');
	}
}

