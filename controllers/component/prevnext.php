<?php
/** 
 * Copyright (c) 2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Prevnext extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * node_id is mandatory input
		 */
		 
		if (is_numeric($this->GET['node_id'])) {
			$node_id = $this->GET['node_id'];
		} else {
			msg("menu_prevnext: missing node_id", 'error');
			return false;
		}
		
		/**
		 * get detail and list
		 */
		 
		$Node = new common_node();
		
		$current_node_detail = $Node->getDetail($node_id);
		$parent_node_detail = $Node->getDetail($current_node_detail['parent']);
		
		$siblings = $Node->listing("parent = {$current_node_detail['parent']} AND node_group = 'page' AND publish = 1", 'priority DESC, id ASC');
		
		if (is_array($siblings)) {
			
			/**
			 * find prev/next node
			 */
			 
			foreach ($siblings as $k=>$item) {
			
				if ($item['id'] == $node_id) {
					
					$prev_node = $siblings[$k-1];
					$next_node = $siblings[$k+1];
					
					break;
				}
				
			}
			
			/**
			 * cycle
			 */
			 
			if (!is_array($prev_node)) {
				$count = count($siblings);
				$prev_node = $siblings[$count-1];
			}
			
			if (!is_array($next_node)) {
				$next_node = $siblings[0];
			}
		
		}
		
		/**
		 * assign
		 */
		 
		$this->tpl->assign('PREV', $prev_node);
		$this->tpl->assign('ALL', $parent_node_detail);
		$this->tpl->assign('NEXT', $next_node);
		
		return true;
	}
}
