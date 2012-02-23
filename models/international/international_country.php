<?php
/**
 * class international_country
 *
 * Copyright (c) 2009-2012 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class international_country extends Onxshop_Model {

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
	var $iso_code2;
	/**
	 * @access private
	 */
	var $iso_code3;
	/**
	 * @access private
	 */
	var $eu_status;
	/**
	 * @access private
	 */
	var $currency_code;

	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE international_country (
    id serial NOT NULL PRIMARY KEY,
    name character varying(255),
    iso_code2 character(2),
    iso_code3 character(3),
    eu_status boolean,
    currency_code character(3)
);
		";
		
		return $sql;
	}

	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {
		if (array_key_exists('international_country', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['international_country'];
		else $conf = array();

		// define default country
		if (!is_numeric($conf['default_id'])) $conf['default_id'] = 222;
		
		//better use CODE3 (i.e. GBR)

		return $conf;
	}

}
