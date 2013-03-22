<?php
/** 
 * Copyright (c) 2009-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Component_Comment extends Onxshop_Controller {

	/** 
	 * main action 
	 */
	 
	public function mainAction() {
	
		/**
		 * set variables
		 */
		
		$options = array();
		$options['allow_anonymouse_submit'] = $this->GET['allow_anonymouse_submit'];
		$options['allow_anonymouse_view'] = $this->GET['allow_anonymouse_view'];
		
		if (is_array($_POST['comment'])) $data = $_POST['comment'];
		else $data = array();
		if (is_numeric($this->GET['node_id'])) $data['node_id'] = $this->GET['node_id'];
		else $data['node_id'] = 0;
		
		
		/**
		 * initialize object
		 */
		 
		$this->Comment = $this->initializeComment();
		
		/**
		 * custom action
		 */
		
		
		$this->customCommentAction($data, $options);
		
		
		/**
		 * destroy object
		 */

		$this->Comment = false;
		
		return true;
	}
	
	/**
	 * initialize comment
	 */
	 
	public function initializeComment() {
	
		require_once('models/common/common_comment.php');
		return new common_comment();
	}
	
	/**
	 * custom comment action
	 */
	 
	public function customCommentAction($data, $options) {
	
		$_Onxshop_Request = new Onxshop_Request("component/comment_list~node_id={$data['node_id']}:allow_anonymouse_submit={$options['allow_anonymouse_submit']}~");
		$this->tpl->assign('COMMENT_LIST', $_Onxshop_Request->getContent());
		
		$_Onxshop_Request = new Onxshop_Request("component/comment_add~node_id={$data['node_id']}:allow_anonymouse_submit={$options['allow_anonymouse_submit']}~");
		$this->tpl->assign('COMMENT_ADD', $_Onxshop_Request->getContent());
		
	}
	
	
	/**
	 * list comments
	 */
	 
	function listComments($node_id, $options = false) {

		$filter = array();
		$filter['node_id'] = $node_id;
		if (is_numeric($this->GET['parent'])) $filter['parent'] = $this->GET['parent'];
		else $filter['parent'] = null;
		$filter['relation_subject'] = $this->getRelationSubject();
		
		$list = $this->Comment->getCommentList($filter, 'id DESC');
		
		$published_comments_count = 0;
		
		foreach ($list as $item) {	
			
			//display only published items, or inserted by active customer, or admin is logged in
			if ($item['publish'] == 1 || $this->checkViewPermission($item)) {
			
				/**
				 * odd_even_class
				 */
				 
				$odd_even = ( $odd_even == 'odd' ) ? 'even' : 'odd';
				$item['odd_even_class'] = $odd_even;
					
				/**
				 * assign
				 */
				 
				$this->tpl->assign('ITEM', $item);
				
				/**
				 * check edit permission
				 */
				 
				if ($this->checkEditPermission($item)) {
					
					/**
					 * display status
					 */
					 
					if ($item['publish'] == 0) $this->tpl->parse('content.comment_list.item.edit.publish_awaiting');
					else if ($item['publish'] == 1) $this->tpl->parse('content.comment_list.item.edit.publish_approved');
					else if ($item['publish'] == -1) $this->tpl->parse('content.comment_list.item.edit.publish_rejected');
					
					if ($filter['parent'] == null) $this->tpl->parse('content.comment_list.item.edit.reply');
					
					$this->tpl->parse('content.comment_list.item.edit');
					
				} else {
				
					if ($item['publish'] == 0) $this->tpl->parse('content.comment_list.item.awaiting');
					else if ($item['publish'] == -1) $this->tpl->parse('content.comment_list.item.rejected');
				}
				
				/**
				 * rating
				 */
				 
				if ($item['rating'] > 0) {
					$rating = round($item['rating']);
					$_Onxshop_Request = new Onxshop_Request("component/rating_stars~rating={$rating}~");
					$this->tpl->assign('RATING_STARS', $_Onxshop_Request->getContent());
				} else {
					$this->tpl->assign('RATING_STARS', '');
				}
				
				//sub comments
				$_Onxshop_Request = new Onxshop_Request("component/comment_list~node_id={$this->GET['node_id']}:parent={$item['id']}~");
				$this->tpl->assign("SUB_COMMENTS", $_Onxshop_Request->getContent());
				
				//parse item block
				$this->tpl->parse('content.comment_list.item');
				
				$published_comments_count++;
				
			}
		}
		
		if ($published_comments_count > 0) {
			$this->tpl->parse('content.comment_list');
		} else {
		
			if ($filter['parent'] == null) $this->tpl->parse('content.comment_list_empty');
			
		}

	}

	
	
	/**
	 * get relation subject
	 */
	 
	public function getRelationSubject() {
				
		return '';
		
	}
	
	/**
	 * checkViewPermission
	 */
	 
	public function checkViewPermission($item) {
		
		if ($item['customer_id'] == $_SESSION['client']['customer']['id'] && $_SESSION['client']['customer']['id'] > 0 ) return true;
		if ($_SESSION['authentication']['username'] == ONXSHOP_DB_USER) return true;
		
		return false;
		
	}
	
	/**
	 * checkEditPermission
	 */
	
	public function checkEditPermission($item) {
	
		if ($_SESSION['authentication']['username'] == ONXSHOP_DB_USER) return true;
		
		return false;
	}
	
	/**
	 * checkIdentityVisibility
	 */
	 
	public function checkIdentityVisibility($item) {
		
		//identity input field is visible to everyone
		return true;
		
	}
}
