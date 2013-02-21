<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * split into: taxonomy_manager, taxonomy_filter - can be used in FE
 * taxonomy_manager_node, taxonomy_manager_product
 */

class Onxshop_Controller_Bo_Component_Relation_Taxonomy extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		/**
		 * initialise
		 */
		 
		require_once('models/common/common_node.php');
		$Node = new common_node();
		
		switch ($this->GET['relation']) {
			case 'product':
				require_once('models/ecommerce/ecommerce_product_taxonomy.php');
				$Taxonomy = new ecommerce_product_taxonomy();
			break;
			case 'variety':
				require_once('models/ecommerce/ecommerce_product_variety_taxonomy.php');
				$Taxonomy = new ecommerce_product_variety_taxonomy();
			break;
			case 'recipe':
				require_once('models/ecommerce/ecommerce_recipe_taxonomy.php');
				$Taxonomy = new ecommerce_recipe_taxonomy();
			break;
			case 'store':
				require_once('models/ecommerce/ecommerce_store_taxonomy.php');
				$Taxonomy = new ecommerce_store_taxonomy();
			case 'customer':
				require_once('models/client/client_customer_taxonomy.php');
				$Taxonomy = new client_customer_taxonomy();
			break;
			
			case 'node':
			default:
				require_once('models/common/common_node_taxonomy.php');
				$Taxonomy = new common_node_taxonomy();
			break;
		}
		
		/**
		 * input variables
		 */
		
		$node_id =  $this->GET['id'];
		
		
		/**
		 * saving
		 */
		
		if (is_array($_POST['relation_taxonomy']) && is_numeric($node_id)) {
		
			$current = $Taxonomy->listing("node_id = " . $node_id);
			
			if (is_array($current)) {
				foreach ($current as $c) {
					$Taxonomy->delete($c['id']);
				}
			}
		
			foreach ($_POST['relation_taxonomy'] as $to_id) {
		
				if (is_numeric($to_id)) {
				
					$taxonomy_data['node_id'] = $node_id;
					$taxonomy_data['taxonomy_tree_id'] = $to_id;
					
					if ($Taxonomy->insert($taxonomy_data)) {
						msg("Relation added to the taxonomy", 'ok', 1);
					}
				}
		
			}
		}
		
		
		/**
		 * listing
		 */
		 
		if (is_numeric($node_id)) {
		
			$current = $Taxonomy->listing("node_id = " . $node_id);
		
			if (is_array($current)) { 
				foreach ($current as $c) {
					$taxonomy_data = $Taxonomy->getLabel($c['taxonomy_tree_id']);
					//print_r($taxonomy_data);
					//check, if there is product_list_container ??
					$this->tpl->assign("CURRENT", $taxonomy_data);
					$_nSite = new nSite("component/breadcrumb_taxonomy~id={$taxonomy_data['id']}~");
					$this->tpl->assign('BREADCRUMB', $_nSite->getContent());
					$this->tpl->parse("content.ptn");
				}
			}
		}

		return true;
	}
}
