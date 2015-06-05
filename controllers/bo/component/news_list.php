<?php
/**
 * Copyright (c) 2006-2013 Onxshop Ltd (https://onxshop.com)
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
		
		if ($this->GET['sorting'] == 'modified') $sorting = 'common_node.modified DESC, id DESC';
		
		$news_list = $this->Node->getNodeList($filter, $sorting);
		
		foreach ($news_list as $k=>$item) {
			$relations = unserialize($item['relations']);
			$news_list[$k]['relations'] = $relations;
		}
		
		return $news_list;
		
	}
	
}
		
