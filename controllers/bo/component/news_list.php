<?php
/**
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/news_list.php');

class Onxshop_Controller_Bo_Component_News_List extends Onxshop_Controller_Component_News_List {

	/**
	 * getNewsListAll
	 */
	 
	public function getNewsListAll($filter, $sorting = 'common_node.created DESC, id DESC') {
		
		//remove filter on parent when blog_node_id is not provided - show all
		if (!is_numeric($this->GET['blog_node_id'])) $filter['parent'] = false;
		
		$news_list = $this->Node->getNodeList($filter, $sorting);
		
		return $news_list;
		
	}
	
}
		
