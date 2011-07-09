<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Node_Delete extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		require_once('models/common/common_node.php');
		
		if (is_numeric($this->GET['id'])) $delete_id = $this->GET['id'];
		else return false;
		
		$Node = new common_node();
		
			//delete
			if ($this->GET['delete']) {
			
				$id_map = $this->createIdMapArray($Node->conf);
				
				/**
				 * safety check we are not trying to delete some core page
				 */
				 
				if (!array_search($delete_id, $id_map)) {
				
					$node_data = $Node->detail($delete_id);
		
					if (!is_array($node_data)) {
						msg("Content ID {$delete_id} does not exists", 'error');
						return false;
					}
					
					if ($this->GET['confirm']) {
						if ($Node->delete($delete_id)) {
							msg("{$node_data['node_group']} \"{$node_data['title']}\" (id={$node_data['id']}) has been deleted");
							//if it was a "page", than go to parent page
							if ($this->GET['ajax'] == 0) {
								if ($node_data['node_group'] == 'page') onxshopGoTo("/page/{$node_data['parent']}");
								else onxshopGoTo($_SESSION['last_diff'], 2);
							}
						} else {
							msg("Can't delete!", 'error');
						}
					} else {
						//get children
						$children = $Node->listing("parent = {$delete_id}");
						foreach ($children as $child) {
							$this->tpl->assign("CHILD", $child);
							$this->tpl->parse('content.confirm.children.item');
						}
						if (count($children) > 0) $this->tpl->parse('content.confirm.children');
						
						//get linked as shared content
						$node_data = $Node->detail($delete_id);
						$this->tpl->assign("NODE", $node_data);
						$shared_linked = $Node->getShared($delete_id);
						foreach ($shared_linked as $linked) {
							$this->tpl->assign("LINKED", $linked);
							$this->tpl->parse('content.confirm.linked.item');
						}
						if (count($shared_linked) > 0) $this->tpl->parse('content.confirm.linked');
						
						
						$this->tpl->parse('content.confirm');
					}
				} else {
					msg("This can't be deleted", 'error');
				}
			}

		return true;
	}
	
	/**
	 * create id map array
	 */
	 
	public function createIdMapArray($node_conf) {
	
		if (!is_array($node_conf)) return false;
	
		$id_map = array();
		
		foreach ($node_conf as $key=>$val) {
			if (preg_match("/^id_map/", $key)) {
				$k = preg_replace("/^id_map-/", "", $key);
				$id_map[$k] = $val;
			}
		}
	
		return $id_map;
	}
	
}
