<?php
/**
 * Copyright (c) 2010-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/common/common_comment.php');

class Onxshop_Controller_Bo_Component_Comment_List extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		/**
		 * Initialize model
		 */
		$Comment = new common_comment();
			
		/**
		 * Initialize pagination variables
		 */
		
		if  (is_numeric($this->GET['limit_from'])) $from = $this->GET['limit_from'];
		else $from = 0;
		if (is_numeric($this->GET['limit_per_page'])) $per_page = $this->GET['limit_per_page'];
		else $per_page = 25;

		$limit = "$from,$per_page";

		/**
		 * Load List
		 */
		$list = $Comment->getCommentList(false, 'id DESC', $limit);
		$count = $Comment->getCommentCount(false);

		/**
		 * Display pagination
		 */
		$_Onxshop_Request = new Onxshop_Request("component/pagination~limit_from=$from:limit_per_page=$per_page:count=$count~");
		$this->tpl->assign('PAGINATION', $_Onxshop_Request->getContent());

		/**
		 * Display items
		 */

		$this->parseList($list);

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
				
				$this->tpl->assign('ITEM', $item);
				$this->tpl->parse('content.item');
			}
		} else {
			$this->tpl->parse('content.empty');
		}
	}
}

