<?php
/**
 *
 * Copyright (c) 2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class client_role_permission extends Onxshop_Model {

	/**
	 * Permission cache to prevent multiple request in single script run
	 */
	private static $permissionCache = array();

	/**
	 * @private
	 */
	var $id;

	/**
	 * @private
	 */
	var $role_id;
	
	/**
	 * @private
	 */
	var $permission;

	/**
	 * @private
	 */
	var $scope;

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
		'role_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'permission'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'scope'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false),
	);

	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "CREATE TABLE client_role_permission (
			id serial NOT NULL PRIMARY KEY,
			role_id integer NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE CASCADE,
			permission integer NOT NULL,
			scope text,
			created timestamp without time zone NOT NULL DEFAULT NOW(),
			modified timestamp without time zone NOT NULL DEFAULT NOW(),
			other_data text
		);

		CREATE INDEX client_role_permission_role_id_key ON client_role_permission USING btree (permission);
		";
			
		return $sql;
	}
	
	/**
	 * Check if given customer can access back office
	 */
	 
	public function isBackofficeUser($customer_id)
	{
		if (!is_numeric($customer_id)) return false;
		
		$list = $this->listing("role_id IN (SELECT role_id FROM client_customer_role WHERE customer_id = $customer_id)");
		
		if (count($list) > 0) return true;
		else return false;
	}

	/**
	 * Return true if given customer has given permission
	 * @param  int    $customer_id Customer Id
	 * @param  int    $permission  Permission
	 * @param  string $scope       Permission scope (null by default)
	 * @return bool
	 */
	public function checkPermissionByCustomer($customer_id, $permission, $scope = null)
	{
		if (!is_numeric($customer_id)) return false;

		// store customer's permission to static variable (kind of cache invalidated by the end of script )
		if (!isset(self::$permissionCache[$customer_id])) {
			self::$permissionCache[$customer_id] = $this->getAllCustomersPermissions($customer_id);
		}

		foreach (self::$permissionCache[$customer_id] as $item) {
			if ($item['permission'] == $permission) {
				if ($scope === null) return true;
				if ($scope == $item['scope']) return true;
			}
		}

		return false;
	}

	/**
	 * Get all customer's permission as array where its index
	 * is permission
	 * @param  [type] $customer_id [description]
	 * @return [type]              [description]
	 */
	public function getAllCustomersPermissions($customer_id)
	{
		if (!is_numeric($customer_id)) return false;

		$sql = "SELECT * FROM client_role_permission WHERE role_id IN (SELECT role_id FROM client_customer_role WHERE customer_id = $customer_id)";
		return $this->executeSql($sql);

	}
}
