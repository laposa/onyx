<?php
/**
 * Copyright (c) 2010-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/common/common_comment.php');
require_once('models/common/common_node.php');

class Onxshop_Controller_Bo_Component_Comment_List extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		$this->initModels();

		/**
		 * Store submited data to the SESSION
		 */
		
		if (isset($_POST['comments-filter'])) {
			$_SESSION['bo'][$this->key]['comments-filter'] = $_POST['comments-filter'];
			onxshopGoTo($this->GET["translate"]);
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
		$s = $_SESSION['bo'][$this->key]['comments-filter'];
		if ($s['location'] > 0) $filter['node_id'] = $s['location']; 
		if (!empty($s['query'])) $filter['query'] = $s['query']; 
		$this->tpl->assign("QUERY", $s['query']);

		/**
		 * Load List
		 */
		$list = $this->Comment->getCommentList($filter, 'id DESC', $limit);
		$count = $this->Comment->getCommentCount($filter);

		/**
		 * Display pagination
		 */
		$_Onxshop_Request = new Onxshop_Request("component/pagination~limit_from=$from:limit_per_page=$per_page:count=$count~");
		$this->tpl->assign('PAGINATION', $_Onxshop_Request->getContent());

		/**
		 * Display comments
		 */

		$this->parseList($list);

		/**
		 * Get used node ids and corresponding names
		 */
		$nodes = $this->Comment->getUsedNodes();

		/**
		 * Display location filter dropdown
		 */
		$this->parseLocationSelect($nodes);

		return true;
	}
	
	/**
	 * Initialize models
	 */

	public function initModels()
	{
		$this->key = "comment";
		$this->Comment = new common_comment();
		$this->Node = new common_node();
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

				$item['node'] = $this->Node->getDetail($item['node_id']);

				$this->tpl->assign('ITEM', $item);
				$this->tpl->parse('content.item');
			}

		} else {

			$this->tpl->parse('content.empty');

		}

	}

	/**
	 * parsePageSelect
	 */

	public function parseLocationSelect($nodes)
	{
		foreach ($nodes as $node) {
			$l = $_SESSION['bo'][$this->key]['comments-filter']['location'];
			if ($l == $node['node_id'] && $l > 0) $this->tpl->assign("SELECTED", 'selected="selected"');
			else $this->tpl->assign("SELECTED", '');
			$this->tpl->assign("ITEM", $node);
			$this->tpl->parse("content.location_item");
		}
	}

}

