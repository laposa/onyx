<?php
/**
 * class client_company
 * 
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class client_company extends Onxshop_Model {

	/**
	 * @access private
	 */
	var $id;
	/**
	 * @access private
	 */
	var $name;
	/**
	 * @access private
	 */
	var $www;
	/**
	 * @access private
	 */
	var $telephone;
	/**
	 * @access private
	 */
	var $fax;
	
	/**
	 * REFERENCES customer(id) ON UPDATE CASCADE ON DELETE CASCADE
	 * @access private
	 */
	var $customer_id;
	
	var $registration_no;
	
	/**
	 * @access private
	 */
	var $vat_no;
	
	var $other_data;

	var $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'name'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'www'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'telephone'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'fax'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'customer_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'registration_no'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'vat_no'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false)
		);
		
	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {
		if (array_key_exists('client_company', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['client_company'];
		else $conf = array();

		return $conf;
	}
		
	/**
	 * get company detail
	 */
	 
	public function getDetail($id) {
		$data = $this->detail($id);
		
		foreach ($data as $key=>$item) {
			if ($item == '') $data[$key] = 'n/a';
		}
		
		return $data;
	}
	
	/**
	 * get all registered companies for a customer
	 */
	 
	public function getCompanyListForCustomer($customer_id) {
		$list = $this->listing("customer_id = $customer_id");
		
		return $list;
		
	}
}
