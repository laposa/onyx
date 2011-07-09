<?php
/**
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/default.php');

class Onxshop_Controller_Bo_Node_Content_Content_List extends Onxshop_Controller_Bo_Node_Default {

	/**
	 * pre action
	 */

	function pre() {
	
		
	}
	
	/**
	 * post action
	 */
	 
	function post() {
		
		/**
		 * container selected
		 */
		 
		$this->tpl->assign("SELECTED_container_{$this->node_data['component']['container']}", "selected='selected'");
		
	}
}

