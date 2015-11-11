<?php
/**
 * Copyright (c) 2010-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/common/common_comment.php');
require_once('models/common/common_node.php');

class Onxshop_Controller_Bo_Component_Comment_List extends Onxshop_Controller {

	protected $pages = array();

	/**
	 * main action
	 */
	 
	public function mainAction() {

		/**
		 * Initialize model
		 */
		$Comment = new common_comment();
		$this->Node = new common_node();

		/**
		 * Store submited data to the SESSION
		 */
		
		if (isset($_POST['comments-filter'])) {
			$_SESSION['bo']['comments-filter'] = $_POST['comments-filter'];
		}
			
		/**
		 * Initialize pagination variables
		 */
		
		if  (is_numeric($this->GET['limit_from'])) $from = $this->GET['limit_from'];
		else $from = 0;
		if (is_numeric($this->GET['limit_per_page'])) $per_page = $this->GET['limit_per_page'];
		else $per_page = 25;

		$limit = "$from,$per_page";

		/**
		 * Prepare filter
		 */
		$filter = array();
		$s = $_SESSION['bo']['comments-filter'];
		if ($s['location'] > 0) $filter['node_id'] = $s['location']; 
		if (!empty($s['query'])) $filter['query'] = $s['query']; 

		/**
		 * Load List
		 */
		$list = $Comment->getCommentList($filter, 'id DESC', $limit);
		$count = $Comment->getCommentCount($filter);

		/**
		 * Display pagination
		 */
		$_Onxshop_Request = new Onxshop_Request("component/pagination~limit_from=$from:limit_per_page=$per_page:count=$count~");
		$this->tpl->assign('PAGINATION', $_Onxshop_Request->getContent());

		/**
		 * Get used node ids and corresponding pages
		 */
		$node_ids = $Comment->getUsedNodeIds();
		foreach ($node_ids as $node_id) $this->getPageForNode($node_id);

		/**
		 * Display commend
		 */

		$this->parseList($list);

		/**
		 * Display page filter dropdown
		 */
		
		$this->parsePageSelect();

		return true;
	}
	

	/**
	 * parse
	 */
	
	public function parseList($list) {
	
		if (count($list) > 0) {

			foreach ($list as $item) {

				switch ($item['publish']) {
					case 0: $item['publish'] = 'new'; break;
					case 1: $item['publish'] = 'approved'; break;
					case -1: $item['publish'] = 'rejected'; break;
				}
				
				if (trim($item['title']) == '') $item['title'] = 'no title';
				if (trim($item['author_name']) == '') $item['author_name'] = 'n/a';

				$item['page'] = $this->getPageForNode($item['node_id']);

				$this->tpl->assign('ITEM', $item);
				$this->tpl->parse('content.item');
			}

		} else {

			$this->tpl->parse('content.empty');

		}

	}

	/**
	 * get page for node
	 */
	function getPageForNode($node_id)
	{
		if (!isset($this->pages[$node_id])) {

			$node = $this->Node->getDetail($node_id);
			if (!$node) $node = array("id" => 0, "title" => "-", "parent" => 0);
			if ($node['node_group'] == 'page') $this->pages[$node_id] = $node;
			$path = empty($node['page_title']) ? $node['title'] : $node['page_title'];

			while ($node['parent'] && $node['node_group'] != 'page') {
				$node = $this->Node->getDetail($node['parent']);
				$title = empty($node['page_title']) ? $node['title'] : $node['page_title'];
				$path = $title . " » " . $path;
				if ($node['node_group'] == 'page') $this->pages[$node_id] = $node;
			}

			$this->pages[$node_id]['path'] = $path;			
		}

		return $this->pages[$node_id];
	}

	/**
	 * parsePageSelect
	 */

	public function parsePageSelect()
	{
		foreach ($this->pages as $node_id => $page) {
			$page['node_id'] = $node_id;
			$pages[$page['path']] = $page;
		}
		ksort($pages);
		foreach ($pages as $page) {
			if ($_SESSION['bo']['comments-filter']['location'] == $page['node_id']) $this->tpl->assign("SELECTED", 'selected="selected"');
			else $this->tpl->assign("SELECTED", '');
			$this->tpl->assign("ITEM", $page);
			$this->tpl->parse("content.page_item");
		}
	}

}

