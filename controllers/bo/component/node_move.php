<?php
/** 
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */


class Onxshop_Controller_Bo_Component_Node_Move extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
			
		/**
		 * check input variables
		 */
		 
		if (is_numeric($this->GET['source_node_id'])) $source_node_id = $this->GET['source_node_id'];
		else {
			msg("taxonomy_move: source_node_id is not numeric", 'error');
			return false;
		}
		
		if (is_numeric($this->GET['destination_node_id'])) $destination_node_id = $this->GET['destination_node_id'];
		else {
			msg("taxonomy_move: destination_node_id is not numeric", 'error');
			return false;
		}
		
		if (is_numeric($this->GET['position'])) $position = $this->GET['position'];
		else {
			msg("taxonomy_move: position is not numeric", 'error');
			return false;
		}
		
		if (is_numeric($this->GET['container'])) $container = $this->GET['container'];
		else $container = 0;
		
		require_once('models/common/common_node.php');
		$Node = new common_node();
		
		if ($Node->moveItem($source_node_id, $destination_node_id, $position, $container)) {
			msg("Node (id=$source_node_id) moved to (parent: $destination_node_id, container: $container, position: $position)", 'ok', 1);
		}
		
		return true;
	}
}

