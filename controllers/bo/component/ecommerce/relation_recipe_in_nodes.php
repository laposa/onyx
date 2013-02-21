<?php
/** 
 * DEPRICATED, modified for transition to single product_in_node and categories
 *
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Relation_Recipe_In_Nodes extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/common/common_node.php');
		require_once('models/ecommerce/ecommerce_recipe.php');
		
		$Node = new common_node();
		$Recipe = new ecommerce_recipe();
		
		$recipe_id = $this->GET['id'];

		//listing
		if (is_numeric($recipe_id)) {
			$current = $Recipe->findRecipeInNode($recipe_id);
			$this->tpl->assign('NODE', $Node->detail($current[0]['id']));

			foreach ($current as $key=>$node_data) {
				//don't display homepage
				if ($key > 0) {
					if ($node_data['publish'] == 0) $node_data['class'] = "class='disabled'";
					$this->tpl->assign("ITEM", $node_data);
					$_nSite = new nSite("component/breadcrumb~id={$node_data['id']}:create_last_link=1~");
					$this->tpl->assign('BREADCRUMB', $_nSite->getContent());
					$this->tpl->parse("content.multiple.item");
				}
			}
			
			if (count($current) > 1) $this->tpl->parse('content.multiple');
		}

		return true;
	}
}
