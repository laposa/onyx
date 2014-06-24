<?php
/**
 *
 * Copyright (c) 2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class client_acl extends Onxshop_Model {

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
		'customer_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
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
	
		$sql = "CREATE TABLE client_acl (
			id serial NOT NULL PRIMARY KEY,
			customer_id integer NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE CASCADE,
			permission integer NOT NULL,
			scope text,
			created timestamp without time zone NOT NULL DEFAULT NOW(),
			modified timestamp without time zone NOT NULL DEFAULT NOW(),
			other_data text
		);

		CREATE INDEX client_acl_customer_id_key ON client_acl USING btree (permission);
		";
			
		return $sql;
	}
	
	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {
		if (array_key_exists('client_acl', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['client_acl'];
		else $conf = array();
		
		return $conf;
	}
	
	/**
	 * isBackofficeUser
	 */
	 
	public function isBackofficeUser($customer_id) {
		
		if (!is_numeric($customer_id)) return false;
		
		$acl_list = $this->listing("customer_id = $customer_id");
		
		if (count($acl_list) > 0) return true;
		else return false;
	}

}
