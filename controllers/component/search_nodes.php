<?php
/** 
 * Copyright (c) 2006-2012 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Search_Nodes extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		if (isset($this->GET['search_query'])) {
			
			$searchQuery = $this->GET['search_query'];
			$count = strlen(trim($searchQuery));
			
			if ($count > 2) {
				require_once('models/common/common_node.php');
			
				$Node = new common_node();
				
				$result = $Node->search($searchQuery);
			
				$added = array();
			
				foreach ($result as $r) {
				
					if ($r['node_group'] != 'page') {
						$active_pages = $Node->getActivePages($r['id']);
						$r = $Node->detail($active_pages[0]);
					}
				
					if (!in_array($r['id'], $added) && $r['node_group'] == 'page') {
						$this->tpl->assign('RESULT', $r);
						$this->tpl->parse('content.result.item');
						$added[] = $r['id'];
					}
				}
			
				$this->tpl->parse('content.result');
			
			} else {
			
				msg("Please specify at least 3 characters", "error");
			
			}
		}

		return true;
	}
}
