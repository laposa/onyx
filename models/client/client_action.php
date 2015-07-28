<?php
/**
 *
 * Copyright (c) 2013-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
require_once('models/common/common_node_taxonomy.php');

class client_action extends Onxshop_Model {

	/**
	 * @private
	 */
	var $id;

	/**
	 * @private
	 */
	var $customer_id;

	/**
	 * @private
	 */
	var $node_id;

	/**
	 * @private
	 */
	var $action_id;

	/**
	 * @private
	 */
	var $network;

	/**
	 * @private
	 */
	var $action_name;

	/**
	 * @private
	 */
	var $object_name;

	/**
	 * @private
	 */
	var $created;

	/**
	 * @private
	 */
	var $modified;

	/**
	 * @private
	 */
	var $other_data;

	var $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'customer_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'node_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'action_id'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'network'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'action_name'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'object_name'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false),
	);

	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "CREATE TABLE client_action (
			id serial NOT NULL PRIMARY KEY,
			customer_id integer NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE CASCADE,
			node_id integer NOT NULL REFERENCES common_node ON UPDATE CASCADE ON DELETE CASCADE,
			action_id varchar(255),
			network varchar(255),
			action_name varchar(255),
			object_name varchar(255),
			created timestamp without time zone NOT NULL,
			modified timestamp without time zone NOT NULL,
			other_data text
		);

		CREATE INDEX client_action_customer_id_key ON client_action USING btree (customer_id);
		CREATE INDEX client_action_network_key ON client_action USING btree (network);
		";
			
		return $sql;
	}
	
	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {
		if (array_key_exists('client_action', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['client_action'];
		else $conf = array();
		
		return $conf;
	}

	/**
	 * insertAction
	 */
	 
	public function insertAction($data) {
		
		if (!is_array($data)) return false;
		
		$data['created'] = date('c');
		$data['modified'] = date('c');
		
		return $this->insert($data);
		
	}
	
	/**
	 * Get list of actions performed by given customers from local database
	 * 
	 * @param  array $customer_ids Customers id to query
	 * @return Array of client_action
	 */
	public function getActionsForCustomers($customer_ids, $num_displayed_items = 3, $filter = false)
	{
		$result = array();

		if (is_array($customer_ids) && count($customer_ids) > 0 && $ids = $this->prepareListForSql($customer_ids))  {

			$filter_sql = '';
			if (is_array($filter) && count($filter) > 0) {
				$filter_sql = ' AND (';
				foreach ($filter as $action) {
					$action = explode("-", $action);
					if (count($action) == 2) {
						$filter_sql .= "(action_name = '" . pg_escape_string($action[0]) . "'";
						$filter_sql .= " AND object_name = '" . pg_escape_string($action[1]) . "') OR ";
					}
				}
				$filter_sql .= '(1 = 0))';
			}

			$list = $this->listing("network = 'facebook' AND customer_id IN ($ids) $filter_sql", 
				"id DESC", "0,{$num_displayed_items}");
			if (is_array($list) && count($list) > 0) foreach ($list as $item) $result[] = $item;

		}

		return $result;
	}
	
	/**
	 * Convert list of identifiers to comma separated values
	 * @param  Array   $list   Array of values
	 * @param  boolean $escape Escape values for SQL?
	 * @return String
	 */
	public function prepareListForSql($list, $escape = false)
	{
		$result = false;

		if (is_array($list) && count($list) > 0) {

			$items = array();
			foreach ($list as $item) {
				if ($escape) $items[] = pg_escape_string(trim($item));
				else $items[] = (int) trim($item);
			}

			$result = implode(",", $items);
		}

		return $result;
	}


	/**
	 * Has Facebook app defined given Open Graph story?
	 * @param  string  $action Action name
	 * @param  string  $object Objectname name
	 * @return boolean
	 */
	static function hasOpenGraphStory($action, $object)
	{
		if (defined('ONXSHOP_FACEBOOK_APP_OG_STORIES')) {

			$stories = explode(',', ONXSHOP_FACEBOOK_APP_OG_STORIES);
			$action = strtolower(trim($action));
			$object = strtolower(trim($object));
	
			if (is_array($stories)) {
				foreach ($stories as $story) {
					$story = explode("-", trim($story));
					if (is_array($story) && count($story) == 2
						&& $action == strtolower(trim($story[0]))
						&& $object == strtolower(trim($story[1]))) return true;
				}
			}
		}

		return false;
	}

}
