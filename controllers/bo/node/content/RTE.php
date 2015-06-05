<?php
/** 
 * Copyright (c) 2005-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/node/content/default.php');

class Onxshop_Controller_Bo_Node_Content_RTE extends Onxshop_Controller_Bo_Node_Content_Default {

	/**
	 * post action
	 */

	function post() {
		
		parent::post();
		
		/**
		 * check hard link
		 */
		
		$hard_links = $this->Node->findHardLinks($this->GET['id']);
		
		if (count($hard_links) > 0) {
			msg("Hard link detected, please fix.", 'error');
		}
		
		/*
		foreach ($hard_links as $hard_link) {
			$this->tpl->assign('ITEM', $hard_link);
			$this->tpl->parse('content.hard_links.item');
		}
		$this->tpl->parse('content.hard_links');
		*/		
	}
}

