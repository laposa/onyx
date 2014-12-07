<?php
/** 
 * Copyright (c) 2009-2014 Laposa Ltd (http://laposa.co.uk)
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
		 
		if (is_numeric($_POST['source_node_id'])) $source_node_id = $_POST['source_node_id'];
		else {
			msg("node_move: source_node_id is not numeric", 'error');
			return false;
		}
		
		if (is_numeric($_POST['destination_node_id'])) $destination_node_id = $_POST['destination_node_id'];
		else {
			msg("node_move: destination_node_id is not numeric", 'error');
			return false;
		}
		
		if (is_numeric($_POST['position'])) $position = $_POST['position'];
		else {
			msg("node_move: position is not numeric", 'error');
			return false;
		}
		
		if (is_numeric($_POST['container'])) $container = $_POST['container'];
		else $container = 0;
		
		/**
		 * the request seems to be valid, try to move
		 */
		 
		require_once('models/common/common_node.php');
		$Node = new common_node();
		
		if ($Node->moveItem($source_node_id, $destination_node_id, $position, $container)) {
			
			msg("Node (id=$source_node_id) moved to (parent: $destination_node_id, container: $container, position: $position)", 'ok', 1);
		
		} else {
			
			msg("Cannot move Node (id=$source_node_id) to (parent: $destination_node_id, container: $container, position: $position)", 'error');
		
		}
		
		return true;
	}
}

