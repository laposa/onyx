<?php
/**
 * class common_comment
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class common_comment extends Onxshop_Model {

	/**
	 * PRIMARY KEY
	 * @access private
	 */
	var $id;
	/**
	 * NOT NULL REFERENCES common_comment ON UPDATE CASCADE ON DELETE CASCADE
	 * @access private
	 */
	var $parent;
	/**
	 * NOT NULL REFERENCES common_node ON UPDATE CASCADE ON DELETE RESTRICT
	 * @access private
	 */
	var $node_id;
	/**
	 * @access private
	 */
	var $title;
	/**
	 * @access private
	 */
	var $content;
	/**
	 * @access private
	 */
	var $author_name;
	/**
	 * @access private
	 */
	var $author_email;
	/**
	 * @access private
	 */
	var $author_website;

	var $author_ip_address;
	/**
	 * NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT
	 * @access private
	 */
	var $customer_id;
	/**
	 * @access private
	 */
	var $created;
	/**
	 * @access private
	 */
	var $publish;
	
	var $rating;
	
	var $relation_subject;

	var $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'parent'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'node_id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'title'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'content'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'author_name'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'author_email'=>array('label' => '', 'validation'=>'email', 'required'=>true),
		'author_website'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'author_ip_address'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'customer_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'publish'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'rating'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'relation_subject'=>array('label' => '', 'validation'=>'text', 'required'=>false)
	);
	
	/**
	 * get tree
	 */
	
	function getTree($node_id, $public = 1, $sort = 'ASC') {
		
		$sql = "SELECT id, parent, title as name, title as title, content, author_name, author_email, author_website, author_ip_address, customer_id, created, rating FROM common_comment WHERE publish >= $public AND node_id='$node_id' ORDER BY parent, created $sort";
		
		$records = $this->executeSql($sql);
		
		return $records;
	}
	
	/**
	 * get detail
	 */
	
	public function getDetail($id) {
	
		if (!is_numeric($id)) {
			msg("common_comment.getDetail: id is not numeric", 'error');
			return false;
		}
		
		$data = $this->detail($id);
		
		return $data;
	}
	
	/**
	 * list
	 */
	 
	function getCommentList($filter = false, $sort = 'id ASC') {
		
		$add_to_where = '1=1 ';
	
		/**
         * query filter
         * 
         */

		if (is_array($filter)) {
			if (is_numeric($filter['node_id'])) {
	            $add_to_where .= "AND node_id = '{$filter['node_id']}' ";
	        }
			
			if ($filter['relation_subject']) {
				$add_to_where .= " AND relation_subject = '{$filter['relation_subject']}' ";
			}
			
			if (is_numeric($filter['parent'])) {
	            $add_to_where .= " AND parent = '{$filter['parent']}'";
	        } else if (array_key_exists('parent', $filter) && $filter['parent'] === null) {
	        	$add_to_where .= " AND parent IS NULL";
	        }
        }
		
		/**
		 * get list
		 */
		 
		$list = $this->listing($add_to_where, $sort);
		
		return $list;
	}
	
	/**
	 * insert comment
	 */

	function insertComment($data) {
	
		//retype null values
		if ($data['parent'] == 0) $data['parent'] = null;
		if ($data['node_id'] == 0) $data['node_id'] = null;
		
		$data['created'] = date('c');

		if (!is_numeric($data['publish'])) $data['publish'] = 0;
		if (!is_numeric($data['rating'])) $data['rating'] = 0;
		$data['author_ip_address'] = $_SERVER['REMOTE_ADDR'];

		if ($id = $this->insert($data)) {
		
			$this->sendNewCommentNotificationEmail($id, $data);
			
			return $id;
			
		} else {
			msg("Cannot insert comment", "error");
			return false;
		}
	}
	
	/**
	 * update comment
	 */

	function updateComment($data) {
	
		//retype null values
		if ($data['parent'] == 0) $data['parent'] = null;
		if ($data['node_id'] == 0) $data['node_id'] = null;

		if (!is_numeric($data['publish'])) $data['publish'] = 0;
		if (!is_numeric($data['rating'])) $data['rating'] = 0;

		if ($id = $this->update($data)) {
			return $id;
		} else {
			msg("Cannot update comment", "error");
			return false;
		}
	}
	
	/**
	 * get customer detail
	 */
	
	function getCustomerDetail($id) {
		require_once('models/client/client_customer.php');
		$Customer = new client_customer();
		
		$data = $Customer->getClientData($id);
		
		return $data;
	}

	/**
	 * notification email
	 */
	 
	public function sendNewCommentNotificationEmail($comment_id, $comment_data) {
	
		require_once('models/common/common_email_form.php');
    	$EmailForm = new common_email_form();
    			
    	//is passed as DATA array into the template at common_email_form->_format
    	$GLOBALS['common_email_form']['comment'] = $comment_data;
    	
    	if (!$EmailForm->sendEmail('comment_notify')) {
    		msg('New comment notification email sending failed.', 'error');
    	}
		
	}
}
