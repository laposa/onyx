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
		 
		//$blog_node_id = $this->GET['blog_node_id'];
		//$node_id = $this->GET['id'];
		
		require_once('models/common/common_node.php');
		$Node = new common_node();
		
		$blog_node_id = CMS_BLOG_ID;
		
		if ($article_archive = $Node->getArticlesCategories($blog_node_id)) {
		
			foreach ($article_archive as $item) {
				if (!$item['title']) {
					$item['title'] = I18N_NEWS_CATEGORY_UNCATEGORIZED;
					$item['taxonomy_tree_id'] = 0;
				}
				$this->tpl->assign('ITEM', $item);
				$this->tpl->parse('content.list.item');
				
			}
			
			$this->tpl->parse('content.list');
			
		}
		
		
		return true;
	}

}
