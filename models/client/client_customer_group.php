<?php
/**
 * class client_customer_group
 * 
 * Copyright (c) 2009-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class client_customer_group extends Onxshop_Model {

	/**
	 * primary key
	 * @access private
	 */
	var $id;

	/**
	 * @access private
	 */
	var $group_id;
	
	/**
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
	var $modified;


	var $_metaData = array(
		'id'=>array('label' => 'Id', 'validation'=>'int', 'required'=>true), 
		'group_id'=>array('label' => 'Group', 'validation'=>'int', 'required'=>true),
		'customer_id'=>array('label' => 'Customer', 'validation'=>'int', 'required'=>true),
		'created'=>array('label' => 'Create', 'validation'=>'date', 'required'=>fase),
		'modified'=>array('label' => 'Modified', 'validation'=>'date', 'required'=>false)
	);
	
	/**
	 * create table sql
	 * 
	 * @return string
	 * SQL command for table creating
	 */
	 
	private function getCreateTableSql()
	{
		$sql = "CREATE TABLE client_customer_group (
			id serial NOT NULL PRIMARY KEY,
			group_id integer NOT NULL REFERENCES client_group ON UPDATE CASCADE ON DELETE CASCADE,
			customer_id integer NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE CASCADE,
			created timestamp without time zone NOT NULL DEFAULT NOW(),
			modified timestamp without time zone NOT NULL DEFAULT NOW()
		);
		CREATE INDEX client_customer_group_group_id_key ON client_customer_group USING btree (group_id);
		CREATE INDEX client_customer_group_customer_id_key ON client_customer_group USING btree (customer_id);
		";
		
		return $sql;
	}
	
	/**
	 * init configuration
	 * 
	 * @return array
	 * customer configuration
	 */
	 
	static function initConfiguration()
	{
		if (array_key_exists('client_customer', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['client_customer'];
		else $conf = array();
		
		return $conf;
	}

	/**
	 * Get array of customer's group ids
	 */
	function getCustomersGroupIds($customer_id)
	{
		if (!is_numeric($customer_id)) return false;
		$list = $this->listing("customer_id = $customer_id");
		$result = array();
		foreach ($list as $item) 
			$result[] = $item['group_id'];
		return $result;		
	}	

	/**
	 * Assign group to a customer (if not set already)
	 * 
	 * @param  int $group_id     Group Id
	 * @param  int $customer_id Customer Id
	 * @return int              Number of updated rows
	 */
	function assignGroupToCustomer($group_id, $customer_id)
	{
		if (!is_numeric($group_id)) return false;
		if (!is_numeric($customer_id)) return false;

		$list = $this->listing("group_id = $group_id AND customer_id = $customer_id");
		if (count($list) > 0) return 0;

		return $this->insert(array(
			"group_id" => $group_id,
			"customer_id" => $customer_id,
			"created" => date("c"),
			"modified" => date("c")
		));
	}

	/**
	 * Remove group from a customer
	 * 
	 * @param  int $group_id     Group Id
	 * @param  int $customer_id Customer Id
	 * @return int              Number of updated rows
	 */
	function removeGroupFromCustomer($group_id, $customer_id)
	{
		if (!is_numeric($group_id)) return false;
		if (!is_numeric($customer_id)) return false;

		$list = $this->listing("group_id = $group_id AND customer_id = $customer_id");
		if (count($list) > 0) return $this->delete($list[0]['id']);
		return 0;
	}

	/**
	 * Update customer's groups as per given array of group ids
	 * 
	 * @param  int   $customer_id Customer Id
	 * @param  array $group_id    Group Ids
	 * @return int                Number of updated rows
	 */
	function updateCustomerGroups($customer_id, $group_ids)
	{
		if (!is_array($group_ids)) return false;
		if (!is_numeric($customer_id)) return false;

		$list = $this->listing("customer_id = $customer_id");
		$result = 0;

		// remove groups which are not specified
		foreach ($list as $item) {
			if (!in_array($item['group_id'], $group_ids)) $result += $this->delete($item['id']);
		}

		foreach ($group_ids as $group_id) {
			$result += $this->assignGroupToCustomer($group_id, $customer_id);
		}

		return $result;
	}

}
