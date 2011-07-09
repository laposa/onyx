<?php
/**
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_Comment_List extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		$list = $this->getList();
		
		$this->parseList($list);

		return true;
	}
	
	/**
	 * get list
	 */
	 
	public function getList() {
	
		require_once('models/common/common_comment.php');
		$Comment = new common_comment();
		
		$list = $Comment->getCommentList(false, 'id DESC');

		return $list;
	}

	/**
	 * parse
	 */
	
	public function parseList($list) {
	
		if (count($list) > 0) {
			foreach ($list as $item) {
				$this->tpl->assign('ITEM', $item);
				$this->tpl->parse('content.item');
			}
		} else {
			$this->tpl->parse('content.empty');
		}
	}
}

