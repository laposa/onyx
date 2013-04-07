<?php
/** 
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onxshop_Controller_Node_Content_Recipe_List extends Onxshop_Controller_Node_Content_Default {

	/**
	 * main action
	 */

	public function mainAction() {
		
		/**
		 * initialize node
		 */
		 
		require_once('models/common/common_node.php');
		$Node = new common_node();
		
		$node_data = $Node->nodeDetail($this->GET['id']);
		
		/**
		 * detect controller for recipe list
		 */

		switch ($node_data['component']['template']) {
		
			case 'shelf':
				$controller = 'recipe_list_shelf';
				break;
				
			case '4col':
			default:
				$controller = 'recipe_list_4columns';
				break;
		}
		
		/**
		 * get related categories
		 */
		require_once('models/common/common_node_taxonomy.php');
		$Node_Taxonomy = new common_node_taxonomy();
		$taxonomy_ids = $Node_Taxonomy->getRelationsToNode($node_data['id']);
		$taxonomy_ids = implode(",", $taxonomy_ids);

		/**
		 * call controller
		 */
		
		$_nSite = new nSite("component/ecommerce/$controller~taxonomy_ids=$taxonomy_ids~");
		$this->tpl->assign('RECIPE_LIST', $_nSite->getContent());
			
		$this->tpl->assign('NODE', $node_data);

		if ($node_data['display_title'])  $this->tpl->parse('content.title');

		return true;
	}
}
