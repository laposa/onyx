<?php
/**
 * class international_currency_rate
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class international_currency_rate extends Onxshop_Model {

	/**
	 * @access private
	 */
	var $id;
	/**
	 * @access private
	 * currencty TO
	 */
	var $currency_code;
	/**
	 * @access private
	 * currency FROM
	 */
	var $currency_code_from;
	/**
	 * @access private
	 */
	var $source;
	/**
	 * @access private
	 */
	var $date;
	/**
	 * @access private
	 */
	var $amount;

	var $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'currency_code'=>array('label' => '', 'validation'=>'string', 'required'=>true), //currency TO
		'currency_code_from'=>array('label' => '', 'validation'=>'string', 'required'=>true), // currency FROM
		'source'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'date'=>array('label' => '', 'validation'=>'date', 'required'=>true),
		'amount'=>array('label' => '', 'validation'=>'decimal', 'required'=>true)
	);

	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE international_currency_rate (
    id serial NOT NULL PRIMARY KEY,
    currency_code character(3),
    currency_code_from character(3),
    source character varying(255),
    date timestamp(0) without time zone,
    amount numeric(12,8)
);
		";
		
		return $sql;
	}
	
	/**
	 * init configuration
	 */
	static function initConfiguration() {
	
		if (array_key_exists('international_currency_rate', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['international_currency_rate'];
		else $conf = array();
		
		return $conf;
	}
	
	/**
	 * get detail
	 */
	 
	function getDetail($id) {
	
		if (!is_numeric($id)) {
	
			msg('international_currency_rate.getDetail(): id is not numeric', 'error');
			return false;
	
		} else {
	
			$rate_detail = $this->detail($id);
			
			return $rate_detail;
	
		}
	}

	/**
	 * insert rate
	 * !!before insert, check if value isn't too different to a last one!!!
	 */
	 
	function insertRate($rate_data) {
	
		if (!is_array($rate_data)) return false;
		
		if (!preg_match('/[A-Z]{3}/', $rate_data['currency_code'])) return false;
		if (!preg_match('/[A-Z]{3}/', $rate_data['currency_code_from'])) return false;
		if ($rate_data['date'] == '') $rate_data['date'] = date('c');
		if (!is_numeric($rate_data['amount'])) return false;
		
		$id = $this->insert($rate_data);
		
		return $id;
	}
	
	/**
	 * get list
	 */
	
	function getLatestRateList($currency_code_from = '') {
		
		$full_rate_list = $this->listing($where, 'date ASC');
		
		foreach ($full_rate_list as $item) {
		
			$rate_list[$item['currency_code_from']] = $item;
		
		}
		
		return $rate_list;
	}
	
	/**
	 * convert estimate, DEPRICATED
	 */
	
	function convert( $value_from, $currency_code_from, $corrency_code_to ) {
	
		// get latest exchange rate
		$exchange_rate = $this->getLatestExchangeRate($currency_code_from, $corrency_code_to);
		
		// process only if we have exchange rate available
		if (is_numeric($exchange_rate)) {
			
			$exchanged_value = $value_from * $exchange_rate;
			
		} else {
		
			msg("Cannot convert exchange rate $currency_code_from/$corrency_code_to", 'error');
			return false;
		
		}
				
		return $exchanged_value;
	}
	
	
	/**
	 * get latest exchange rate
	 */
	
	function getLatestExchangeRate($currency_code_from, $corrency_code_to) {
	
		if ($currency_code_from == $corrency_code_to) return 1;
		
		//get actual currency rates
		$currency_rate_data = $this->listing("currency_code='$corrency_code_to' AND currency_code_from='$currency_code_from'", 'date DESC');
		
		if (count($currency_rate_data) == 0) {
			
			msg("No exchange rate available for $currency_code_from/$corrency_code_to", 'error');
			return false;
			
		} else {
		
			$exchange_rate = $currency_rate_data[0]['amount'];
			
			return $exchange_rate;
		
		}
		
	}
	
	/**
	 * TODO: download from Czech National Bank
	 */
	
	function getLatestFromCNB() {
	
		//http://www.cnb.cz/cs/financni_trhy/devizovy_trh/kurzy_devizoveho_trhu/denni_kurz.txt
		//http://www.cnb.cz/en/financial_markets/foreign_exchange_market/exchange_rate_fixing/daily.txt
		//http://www.bankofengland.co.uk/markets/forex/announcements.htm
	}


}