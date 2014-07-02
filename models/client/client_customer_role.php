<?php
/**
 * class client_customer_role
 * 
 * Copyright (c) 2009-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class client_customer_role extends Onxshop_Model {

	/**
	 * primary key
	 * @access private
	 */
	var $id;

	/**
	 * @access private
	 */
	var $role_id;
	
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
		'role_id'=>array('label' => 'Role', 'validation'=>'int', 'required'=>true),
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
		$sql = "CREATE TABLE client_customer_role (
			id serial NOT NULL PRIMARY KEY,
			role_id integer NOT NULL REFERENCES client_role ON UPDATE CASCADE ON DELETE CASCADE,
			customer_id integer NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE CASCADE,
			created timestamp without time zone NOT NULL DEFAULT NOW(),
			modified timestamp without time zone NOT NULL DEFAULT NOW()
		);
		CREATE INDEX client_customer_role_role_id_key ON client_customer_role USING btree (role_id);
		CREATE INDEX client_customer_role_customer_id_key ON client_customer_role USING btree (customer_id);
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
	 * Get array of customer's role ids
	 */
	function getCustomersRoleIds($customer_id)
	{
		if (!is_numeric($customer_id)) return false;
		$list = $this->listing("customer_id = $customer_id");
		$result = array();
		foreach ($list as $item) 
			$result[] = $item['role_id'];
		return $result;		
	}	

	/**
	 * Assign role to a customer (if not set already)
	 * 
	 * @param  int $role_id     Role Id
	 * @param  int $customer_id Customer Id
	 * @return int              Number of updated rows
	 */
	function assignRoleToCustomer($role_id, $customer_id)
	{
		if (!is_numeric($role_id)) return false;
		if (!is_numeric($customer_id)) return false;

		$list = $this->listing("role_id = $role_id AND customer_id = $customer_id");
		if (count($list) > 0) return 0;

		return $this->insert(array(
			"role_id" => $role_id,
			"customer_id" => $customer_id,
			"created" => date("c"),
			"modified" => date("c")
		));
	}

	/**
	 * Remove role from a customer
	 * 
	 * @param  int $role_id     Role Id
	 * @param  int $customer_id Customer Id
	 * @return int              Number of updated rows
	 */
	function removeRoleFromCustomer($role_id, $customer_id)
	{
		if (!is_numeric($role_id)) return false;
		if (!is_numeric($customer_id)) return false;

		$list = $this->listing("role_id = $role_id AND customer_id = $customer_id");
		if (count($list) > 0) return $this->delete($list[0]['id']);
		return 0;
	}

	/**
	 * Update customer's roles as per given array of role ids
	 * 
	 * @param  int   $customer_id Customer Id
	 * @param  array $role_id     Role Ids
	 * @return int                Number of updated rows
	 */
	function updateCustomerRoles($customer_id, $role_ids)
	{
		if (!is_array($role_ids)) return false;
		if (!is_numeric($customer_id)) return false;

		$list = $this->listing("customer_id = $customer_id");
		$result = 0;

		// remove roles which are not specified
		foreach ($list as $item) {
			if (!in_array($item['role_id'], $role_id)) $result += $this->delete($item['id']);
		}

		foreach ($role_ids as $role_id) {
			$result += $this->assignRoleToCustomer($role_id, $customer_id);
		}

		return $result;
	}

}
