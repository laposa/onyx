<?php
/**
 * Copyright (c) 2009-2013 Laposa Ltd (http://laposa.co.uk)
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

	var $_metaData = array(
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
	 * create table sql
	 * 
	 * @return string
	 * SQL command for table creating
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE common_comment ( 
	id serial PRIMARY KEY NOT NULL,
	parent int REFERENCES common_comment ON UPDATE CASCADE ON DELETE CASCADE,
	node_id int REFERENCES common_node ON UPDATE CASCADE ON DELETE RESTRICT,
	title varchar(255) ,
	content text ,
	author_name varchar(255) ,
	author_email varchar(255) ,
	author_website varchar(255) ,
	author_ip_address varchar(255),
	customer_id int NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
	created timestamp(0) default now(),
	publish smallint,
	rating default 0,
	relation_subject text
);
CREATE INDEX common_comment_node_id_key1 ON common_comment USING btree (node_id);
		";
		
		return $sql;
	}
	
	/**
	 * get comments tree
	 * 
	 * @param integer $node_id
	 * ID of node for comments
	 * 
	 * @param integer $public
	 * only published (1) or also unpublished (0) comments
	 * 
	 * @param string $sort
	 * sorting direction ['ASC'/'DESC']
	 * 
	 * @return array
	 * comments
	 */
	
	function getTree($node_id, $public = 1, $sort = 'ASC') {
		
		$sql = "SELECT id, parent, title as name, title as title, content, author_name, author_email, author_website, author_ip_address, customer_id, created, rating, relation_subject FROM common_comment WHERE publish >= $public AND node_id='$node_id' ORDER BY parent, created $sort";
		
		$records = $this->executeSql($sql);
		
		return $records;
	}
	
	/**
	 * get detail
	 * 
	 * @param integer $id
	 * comment ID
	 * 
	 * @return array
	 * comment informations
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
	 * 
	 * @param array $filter
	 * comments filter with any of keys node_id, relation_subject and parent
	 * 
	 * @param string $sort
	 * sorting direction ['ASC'/'DESC']
	 * 
	 * @return array
	 * comments list or false
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
	 * 
	 * @param array $data
	 * comment informations for save
	 * 
	 * @return integer
	 * saved comment ID or false if save failed
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
	 * 
	 * @param array $data
	 * comment informations for save
	 * 
	 * @return integer
	 * saved comment ID or false if save failed
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
	 * 
	 * @param integer $id
	 * customer ID
	 * 
	 * @return array
	 * customer informations
	 */
	
	function getCustomerDetail($id) {
		require_once('models/client/client_customer.php');
		$Customer = new client_customer();
		
		$data = $Customer->getClientData($id);
		
		return $data;
	}

	/**
	 * notification email
	 * 
	 * @param integer $comment_id
	 * ID of comment - not used
	 * 
	 * @param array $comment_data
	 * information about comment
	 */
	 
	public function sendNewCommentNotificationEmail($comment_id, $comment_data) {
	
		require_once('models/common/common_email.php');
    	$EmailForm = new common_email();
    			
    	//is passed as DATA array into the template at common_email->_format
    	$GLOBALS['common_email']['comment'] = $comment_data;
    	
    	if (!$EmailForm->sendEmail('comment_notify')) {
    		msg('New comment notification email sending failed.', 'error');
    	}
		
	}
	
	/**
	 * getRating
	 */
	
	public function getRating($node_id) {
		
		if (!is_numeric($node_id)) return false;
		
		$sql = "SELECT count(review.id) AS count, avg(review.rating) AS rating FROM {$this->_class_name} review WHERE node_id = $node_id AND publish = 1";
		
		$records = $this->executeSql($sql);
		
		if (is_array($records)) {
			
			$review = $records[0];
			
			if (is_array($review)) return $review;
			else return false;
		
		} else {
			
			return false;
		}
		
	}
}
