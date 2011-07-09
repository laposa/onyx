<?php
/** 
 * Copyright (c) 2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/component/breadcrumb.php');

class Onxshop_Controller_Component_Breadcrumb_History extends Onxshop_Controller_Component_Breadcrumb {
	
	
	/**
	 * get node id
	 */
	 
	public function getNodeId() {
	
		if ($node_id = $this->getPreviousNodeIdFromHistory()) return $node_id;
		
		if (is_numeric($this->GET['id'])) {
		
			$node_id = $this->GET['id'];
		
			return $node_id;
			
		} else {
			
			return false;
		}
	}
	
	/**
	 * get previous node id from history
	 */
	 
	public function getPreviousNodeIdFromHistory() {
		
		if (!is_array($_SESSION['history'])) return false;
		
		$history_count = count($_SESSION['history']);
		
		if ($history_count == 0) return false;
		
		$last_node_id = $_SESSION['history'][$history_count - 2]['node_id'];
		
		//exception for the homepage
		if ($last_node_id == $this->Node->conf['id_map-homepage']) return false;
		
		if (is_numeric($last_node_id)) return $last_node_id;
		else return false;
	}
	
}
