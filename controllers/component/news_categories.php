<?php
/**
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */


class Onxshop_Controller_Component_News_Categories extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * input data
		 */
		 
		if ($this->GET['blog_node_id']) $blog_node_id = $this->GET['blog_node_id'];
		else $blog_node_id = CMS_BLOG_ID;
		if (is_numeric($this->GET['taxonomy_parent_id'])) $taxonomy_parent_id = $this->GET['taxonomy_parent_id'];
		else $taxonomy_parent_id = false;
		//$node_id = $this->GET['id'];
		
		require_once('models/common/common_node.php');
		$Node = new common_node();
		
		if ($article_archive = $Node->getArticlesCategories($blog_node_id)) {
			
			foreach ($article_archive as $item) {
				
				if (!$item['title']) {
					$item['title'] = I18N_NEWS_CATEGORY_UNCATEGORIZED;
					$item['taxonomy_tree_id'] = 0;
				}
				$this->tpl->assign('ITEM', $item);
				
				if (is_numeric($taxonomy_parent_id)) {
					if ($taxonomy_parent_id == $item['parent_id']) $this->tpl->parse('content.list.item');
				} else {
					$this->tpl->parse('content.list.item');
				}
			}
			
			$this->tpl->parse('content.list');
			
		}
		
		
		return true;
	}

}
