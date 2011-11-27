<?php
/**
 * class international_currency
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class international_currency extends Onxshop_Model {

	var $id;
	
	/**
	 * @access private
	 */
	var $code;
	/**
	 * @access private
	 */
	var $name;
	/**
	 * @access private, DEPRICATED
	 */
	var $symbol_left;
	/**
	 * @access private, DEPRICATED
	 */
	var $symbol_right;
	
	var $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'code'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'name'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'symbol_left'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'symbol_right'=>array('label' => '', 'validation'=>'string', 'required'=>false)
	);
	
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE international_currency (
    id serial NOT NULL PRIMARY KEY,
    code character(3),
    name character varying(255) DEFAULT ''::character varying NOT NULL,
    symbol_left character varying(255),
    symbol_right character varying(255)
);
		";
		
		return $sql;
	}

	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {
	
		if (array_key_exists('international_currency', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['international_currency'];
		else $conf = array();
		
		$conf['default'] = GLOBAL_DEFAULT_CURRENCY;
		$conf['allowed'] = array(GLOBAL_DEFAULT_CURRENCY);
		//$conf['allowed'] = array('all');
		
		return $conf;
	}
	
	/**
	 * convert
	 */
	 
	function convert( $value, $from, $to ) {
	
		require_once('models/international/international_currency_rate.php');
		$Currency_rate = new international_currency_rate();
		
		if ($from == $to) return $value;
		else $result = $Currency_rate->convert($value, $from, $to);
		
		return $result;
	}
	
	/**
	 * get latest rate
	 */
	
	function getLatestExchangeRate($from, $to) {
	
		$result = $this->convert(1, $from, $to);
		
		return $result;
	}
}