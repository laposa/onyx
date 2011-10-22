<?php
/** 
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
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
	
		$_nSite = new nSite("component/comment_list~node_id={$data['node_id']}:allow_anonymouse_submit={$options['allow_anonymouse_submit']}~");
		$this->tpl->assign('COMMENT_LIST', $_nSite->getContent());
		
		$_nSite = new nSite("component/comment_add~node_id={$data['node_id']}:allow_anonymouse_submit={$options['allow_anonymouse_submit']}~");
		$this->tpl->assign('COMMENT_ADD', $_nSite->getContent());
		
	}
	
	
	/**
	 * list comments
	 */
	 
	function listComments($node_id, $options = false) {

		$filter = array();
		$filter['node_id'] = $node_id;
		
		$list = $this->Comment->getCommentList($filter, 'id DESC');
		
		foreach ($list as $item) {
		
			//display only published items, or inserted by active customer
			if ($item['publish'] == 1 || ($item['customer_id'] == $_SESSION['client']['customer']['id'] && $_SESSION['client']['customer']['id'] > 0 )) {
			
				/**
				 * odd_even_class
				 */
				 
				$odd_even = ( $odd_even == 'odd' ) ? 'even' : 'odd';
				$item['odd_even_class'] = $odd_even;
					
				/**
				 * assign
				 */
				 
				$this->tpl->assign('ITEM', $item);
			
				if ($item['publish'] == 0) $this->tpl->parse('content.comment_list.item.awaiting');
				
				/**
				 * rating
				 */
				 
				if ($item['rating'] > 0) {
					$rating = round($item['rating']);
					$_nSite = new nSite("component/rating_stars~rating={$rating}~");
					$this->tpl->assign('RATING_STARS', $_nSite->getContent());
				} else {
					$this->tpl->assign('RATING_STARS', '');
				}
				
				//parse item block
				$this->tpl->parse('content.comment_list.item');
			}
		}
		
		
		if (count($list) > 0) {
			$this->tpl->parse('content.comment_list');
		}

	}

	
	
	/**
	 * insert comments
	 */
	 
	function insertComment($data, $options = false) {
		
		if ($_POST['save']) {
		
			if ($this->checkData($data)) {
			
				/**
				 * set customer id
				 */
				 
				if (is_numeric($_SESSION['client']['customer']['id']) && $_SESSION['client']['customer']['id'] > 0) {
		
					$data['customer_id'] = $_SESSION['client']['customer']['id'];
			
				} else if (!is_numeric($data['customer_id']) && $options['allow_anonymouse_submit'])  {
					//anonymous
					$data['customer_id'] = 0;
				}
		
				if (is_numeric($data['customer_id'] )) {
					
					if ($this->Comment->insertComment($data)) {
						
						msg('Your comment has been inserted');
						
						return true;
					}
				} else {
					msg("Must be logged in!", 'error');
					return false;
				}
				
			} else {
				
				msg("Please fill in all fields", 'error');
			}
		} else {
		
			return false;
		}
	
	}
	
	/**
	 * check data
	 */
	 
	public function checkData($data) {
	
		if (trim($data['title']) == '' || trim($data['author_name']) == '' || trim($data['author_email']) == '' || trim($data['title']) == '' || !is_numeric($data['rating'])) return false;
		else return true;
	}

	/**
	 * conditional display submit form
	 */
	 
	public function displaySubmitForm($data, $options) {

		/**
		 * display and process insert only when allowed
		 */
		 
		if ($_SESSION['client']['customer']['id'] || $options['allow_anonymouse_submit']) {
			
			if ($_POST['save']) {
			
				/**
				 * insert comment
				 */
				
				if ($this->insertComment($data, $options)) $this->tpl->parse('content.comment_inserted');
				else $this->assignAndParseForm($data);
			
			} else {
				 
				$this->assignAndParseForm($data);
			}
			
		} else {
			
			$_Onxshop = new nSite("component/client/login");
			$this->tpl->assign('LOGIN_BOX', $_Onxshop->getContent());
			
			$this->tpl->parse('content.log_to_insert');
		}

	}

	/**
	 * assign data to form and parse
	 */
	 
	public function assignAndParseForm($data) {
		
		/**
		 * prepopulate data
		 */
		 	
		if (is_numeric($_SESSION['client']['customer']['id']) && $_SESSION['client']['customer']['id'] > 0) {
		
			$data['customer_id'] = $_SESSION['client']['customer']['id'];
			$customer_detail = $this->Comment->getCustomerDetail($data['customer_id']);

			$data['author_name'] = "{$customer_detail['customer']['title_before']} {$customer_detail['customer']['first_name']} {$customer_detail['customer']['last_name']}";
			$data['author_email'] = $customer_detail['customer']['email'];
			
		}
		
		$this->tpl->assign('COMMENT', $data);
		
		
		/**
		 * display insert form
		 */
		
		$this->tpl->parse('content.comment_insert');
	}

}
