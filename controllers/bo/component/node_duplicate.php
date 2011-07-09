<?php
/** 
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Node_Duplicate extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		/*
		 * 1. get old detail
		 * 2. insert new
		 * 3. check for related images on old 
		 * 4. duplicate images
		 */
		require_once('models/common/common_node.php');
		
		if (is_numeric($this->GET['id'])) $original_node_id = $this->GET['id'];
		else return false;
		
		$Node = new common_node();
		
		$original_node_data = $Node->detail($original_node_id);
		$new_node_data = $original_node_data;
		$new_node_data['title'] = "{$new_node_data['title']} (copy)";
		unset($new_node_data['id']);
		
		$new_node_id = $Node->insert($new_node_data);
		if (!is_numeric($new_node_id)) {
			msg('node_duplicate: Cannot create node', 'error');
			return false;
		}
		
		msg("Duplicated. Please note that associated images were not copied over.");
		
		$_nSite = new nSite("node~id=$new_node_id~");
		$this->tpl->assign('NODE_DETAIL', $_nSite->getContent());
		
		return true;
	}
}
