<?php
/** 
 * Copyright (c) 2012-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api.php');

class Onxshop_Controller_Api_v1_0_Store_Location_List extends Onxshop_Controller_Api {

	/**
	 * get data
	 */
	
	public function getData() {
		
		/**
		 * initialize
		 */
		 
		require_once('models/ecommerce/ecommerce_store.php');
		$Store = new ecommerce_store();
		
		/**
		 * get stores
		 */
		
		$records = $Store->listing("type_id = 1 AND publish = 1");
		
		$data = array();
		
		foreach($records as $record ) {
			
			$data[] = $this->formatItem($record);
						
		}
			
		return $data;
		
	}
	
	/**
	 * formatItem
	 */
	 
	public function formatItem($record) {
		
		$item = array();
		
		$address_detail = $this->getAddressDetail($record);
			
		$item['id'] = $record['id'];
		$item['title'] = (string)$record['title'];
		$item['address'] = (string)$address_detail['address'];
		$item['city'] = (string)$address_detail['city'];
		$item['county'] = (string)$address_detail['county'];
		$item['country'] = (string)$address_detail['country'];
		$item['latitude'] = $record['latitude'];
		$item['longitude'] = $record['longitude'];
		$item['openning_hours'] = (string)$record['opening_hours']; // spelling fixed in API v1.2
		$item['phone'] = (string)$record['telephone'];
		$item['fax'] = '';
		$item['manager'] = (string)$record['manager_name'];
		$item['modified'] = $record['modified'];
		
		return $item;
	}
	
	/**
	 * getAddressDetail
	 */
	 
	public function getAddressDetail($record) {
		
		$address_detail = array();
		
		if ($record['address_line_1']) {
			
			$address_detail['address'] = $record['address_line_1'];
			if ($record['address_line_2']) $address_detail['address'] = "{$address_detail['address']}, {$record['address_line_2']}";
			if ($record['address_line_3']) $address_detail['address'] = "{$address_detail['address']}, {$record['address_line_3']}";
			$address_detail['city'] = $record['address_city'];
			$address_detail['county'] = $record['address_county'];
			if (is_numeric($record['country_id'])) $address_detail['country'] = $this->getCountryName($record['country_id']);
			else $address_detail['country'] = '';
			
		} else if (($line_count = count(preg_split('/\R/',$record['address']))) > 1) {
			
			$address_detail = $this->parseAddressLineByLine($record['address']);
			
		} else {
			
			$address_detail = $this->parseAddressCommas($record['address']);
			
		}
		
		return $address_detail;
		
	}
	
	/**
	 * parseAddressLineByLine
	 */
	 
	public function parseAddressLineByLine($address) {
		
		$address_detail = preg_split('/\R/', $address);
		
		$address_detail = array_reverse($address_detail);
		
		$formated_address = array();
		$formated_address['country'] = $address_detail[0];
		$formated_address['county'] = $address_detail[1];
		$formated_address['city'] = $address_detail[2];
		$formated_address['address'] = $address_detail[3];
		if ($address_detail[4]) $formated_address['address'] = $formated_address['address'] . ', ' .  $address_detail[4];
		
		return $formated_address;
	}
	
	/**
	 * parseAddressCommas
	 */
	 
	public function parseAddressCommas($address) {
		
		$address_detail = preg_split('/,/', $address);
		
		$address_detail = array_reverse($address_detail);
		
		$formated_address = array();
		$formated_address['country'] = 'Ireland';
		$formated_address['county'] = $address_detail[0];
		$formated_address['city'] = $address_detail[1];
		$formated_address['address'] = $address_detail[2];
		if ($address_detail[3]) $formated_address['address'] = $formated_address['address'] . ', ' .  $address_detail[3];
		
		return $formated_address;
	}
	
	/**
	 * getCountryName
	 */
	 
	public function getCountryName($country_id) {
		
		if (!is_numeric($country_id)) return false;
		
		require_once('models/international/international_country.php');
		$Country = new international_country();
		
		$country_detail = $Country->detail($country_id);
		
		if (is_array($country_detail)) return $country_detail['name'];
		else return false;
		
	}
}
