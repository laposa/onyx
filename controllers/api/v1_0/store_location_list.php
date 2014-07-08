<?php
/** 
 * Copyright (c) 2012-2014 Laposa Ltd (http://laposa.co.uk)
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
		
		$records = $Store->listing("type_id = 1");
		
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
		
		$address_detail = $this->parseAddressCommas($record['address']);
			
		$item['id'] = $record['id'];
		$item['title'] = $record['title'];
		$item['address'] = $address_detail['address'];
		$item['city'] = $address_detail['city'];
		$item['county'] = $address_detail['county'];
		$item['country'] = $address_detail['country'];
		$item['latitude'] = $record['latitude'];
		$item['longitude'] = $record['longitude'];
		$item['openning_hours'] = $record['opening_hours']; //TODO rename openning_hours to opening_hours in API 1.1
		$item['phone'] = $record['telephone'];
		$item['fax'] = '';
		$item['manager'] = $record['manager_name'];
		$item['modified'] = $record['modified'];
		
		return $item;
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
}
