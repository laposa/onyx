<?php
/**
 * Blog controller
 *
 * Copyright (c) 2008-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Pages_News extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {

		/**
		 * initialise
		 */
		 
		require_once('models/common/common_node.php');
		$this->Node = new common_node();

		/**
		 * basic input data
		 */
		 
		if (is_numeric($this->GET['blog_node_id'])) $blog_node_id = $this->GET['blog_node_id'];
		else $blog_node_id = $this->Node->conf['id_map-blog'];

		/**
		 * get detail of blog container node
		 */
		 		
		$news_list_detail = $this->Node->getDetail($blog_node_id);
		$this->tpl->assign('NEWS_LIST', $news_list_detail);

		return true;
	}
}

