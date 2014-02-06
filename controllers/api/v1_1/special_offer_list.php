<?php
/** 
 * Copyright (c) 2013-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api/v1_0/special_offer_list.php');

class Onxshop_Controller_Api_v1_1_Special_Offer_List extends Onxshop_Controller_Api_v1_0_Special_Offer_List {

	/**
	 * get data
	 */
	
	public function getData() {

		$data = '';
		
		/**
		 * initialize
		 */
		 
		require_once('models/wordpress/wordpress_special_offer.php');
		$SpecialOffer = new wordpress_special_offer();
		
		/**
		 * get special offer list
		 */
		
		$records = $SpecialOffer->getSpecialOfferList();
		
		$data = array();
		
		foreach($records as $record) {

			$item = $SpecialOffer->getSpecialOfferDetail_v1_0($record->ID, $record);
			$item['expiry_date'] = $item['expiry_date'] . ' 23:59:59';
			
			$data[] = $item;
			
		}
			
		return $data;
		
	}
		
}
