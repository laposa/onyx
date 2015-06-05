<?php
/**
 * class client_role
 * 
 * Copyright (c) 2011-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class client_role extends Onxshop_Model {

	/**
	 * primary key (serial)
	 */
	public $id;
	
	/**
	 * role title
	 */
	public $name;
	
	/**
	 * role description
	 */
	public $description;
	
	/**
	 * serialized reserved
	 */
	public $other_data;
	
	/**
	 * meta data 
	 */
	public $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'name'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'description'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false)
		);
	
	/**
	 * create table sql
	 * 
	 * @return string
	 * SQL command for table creating
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "CREATE TABLE client_role (
		    id serial NOT NULL PRIMARY KEY,
		    name varchar(255) ,
		    description text ,
		    other_data text
		)";
		
		return $sql;
	}
		
	/**
	 * init configuration
	 * 
	 * @return array
	 * role configuration
	 */
	 
	static function initConfiguration() {
	
		if (array_key_exists('client_role', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['client_role'];
		else $conf = array();

		return $conf;
	}
		
	/**
	 * get role detail
	 * 
	 * @param integer $id
	 * role ID
	 * 
	 * @return array
	 * SQL row with role informations
	 */
	 
	public function getDetail($id) {
	
		if (!is_numeric($id)) return false;
		
		$data = $this->detail($id);
		
		if ($data['other_data']) $data['other_data'] = unserialize($data['other_data']);
					
		return $data;
	}
	
	/**
	 * list available roles
	 * 
	 * @return array
	 * roles informations
	 */
	
	public function listRoles() {
	
		$list = $this->listing();
		
		$final_list = array();
		
		foreach ($list as $item) {
		
			if ($item['other_data']) $item['other_data'] = unserialize($item['other_data']);
			
			$final_list[] = $item;
		
		}
		
		return $final_list;
	}
	
	/**
	 * save role
	 * 
	 * @param array $data
	 * role informations for save
	 * 
	 * @return integer
	 * saved role ID or false if save failed
	 */
	 
	public function saveRole($data) {
		
		if (!is_array($data)) return false;
		
		if (array_key_exists('other_data', $data)) $data['other_data'] = serialize($data['other_data']);
		
		return $this->save($data);
		
	}
}
