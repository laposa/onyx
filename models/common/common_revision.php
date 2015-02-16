<?php
/**
 * class common_revision
 *
 * Copyright (c) 2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class common_revision extends Onxshop_Model {

	/**
	 * @access public
	 */
	var $id;

	/**
	 * @access public
	 */
	var $object;

	/**
	 * @access public
	 */
	var $node_id;
	
	/**
	 * @access public
	 */
	var $content;
	
	/**
	 * @access public
	 */
	var $status;
	
	/**
	 * @access public
	 */
	var $customer_id;

	/**
	 * @access public
	 */
	var $created;
	
	/**
	 * @access public
	 */
	var $modified;

	/**
	 * @access public
	 */
	var $other_data;

	var $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'object'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'node_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'content'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'status'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'customer_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
		'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
		'other_data'=>array('label' => '', 'validation'=>'string', 'required'=>false)
	);

	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {

		$sql = "CREATE TABLE common_revision (
			id serial PRIMARY KEY NOT NULL,
			object varchar(255) NOT NULL,
			node_id integer NOT NULL,
			content text,
			status smallint,
			customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
			created timestamp without time zone NOT NULL DEFAULT NOW(),
			modified timestamp without time zone NOT NULL DEFAULT NOW(),
			other_data text
		);
		
		CREATE INDEX common_revision_combined_idx
			ON common_revision
			USING btree
			(object, node_id);
		";

		return $sql;
	}
	
	/**
	 * init configuration
	 */
	 
	static function initConfiguration()
	{
	
		if (array_key_exists('common_revision', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['common_revision'];
		else $conf = array();

		return $conf;
	}
	
	/**
	 * insertRevision
	 */
	 
	public function insertRevision($data) {
		
		if (!is_numeric($data['node_id'])) return false;
		if (strlen($data['object']) == 0) return false;
		if (!is_array($data['content'])) return false;
		
		/**
	     * serialize
	     */
	     
	    $data['content'] = serialize($data['content']);
	    
		/**
		 * customer_id
		 */
		 
		$bo_user_id = Onxshop_Bo_Authentication::getInstance()->getUserId();
		if (is_numeric($bo_user_id)) $data['customer_id'] = $bo_user_id;
		else $data['customer_id'] = (int) $_SESSION['client']['customer']['id'];
		
		return $this->insert($data);
	}

	/**
	 * get list
	 */
	 
	public function getList($object, $node_id) {
		
		$add_to_where = '1=1';
		if (in_array($object, self::getAllowedRevisionObjects())) $add_to_where .= " AND object = '$object' ";
		if (is_numeric($node_id)) $add_to_where .= " AND node_id = $node_id ";
		
		$list = $this->listing($add_to_where);

		return $list;
	}
	
	/**
	 * getAllowedRevisionObjects
	 */
	 
	static function getAllowedRevisionObjects() {
		 
		 return array('common_node', 'ecommerce_product', 'ecommerce_product_variety');
		 
	}
	
	/**
	 * getAuthorStats
	 */
	 
	public function getAuthorStats($customer_id) {
		
		if (!is_numeric($customer_id)) return false;
		
		$stats = array();
		
		$stats['total'] = $this->count("customer_id = $customer_id");
				
		return $stats;
		
	}
	
}