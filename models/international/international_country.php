<?php
/**
 * class international_country
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
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
	 * init configuration
	 */
	 
	static function initConfiguration() {
		if (array_key_exists('international_country', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['international_country'];
		else $conf = array();

		// define default country
		$conf['default_id'] = 222;
		
		//better use CODE3 (i.e. GBR)

		return $conf;
	}

}
