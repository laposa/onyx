<?php
/**
 * class ecommerce_product_type
 * link to document with vat rates
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_product_type extends Onxshop_Model {

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
	var $vat;
	
	var $publish;


	var $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'name'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'vat'=>array('label' => '', 'validation'=>'numeric', 'required'=>true),
		'publish'=>array('label' => '', 'validation'=>'int', 'required'=>false)
	);

	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {
		if (array_key_exists('ecommerce_price', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['ecommerce_price'];
		else $conf = array();
		
		$conf['default_id'] = 9;
		
		return $conf;
	}
}