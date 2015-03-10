<?php
/** 
 * Copyright (c) 2013-2015 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api/v1_0/special_offer_list.php');

class Onxshop_Controller_Api_v1_1_Special_Offer_List extends Onxshop_Controller_Api_v1_0_Special_Offer_List {
		
	/**
	 * formatItem
	 */
	 
	public function formatItem($original_item) {
		
		$item = parent::formatItem($original_item);
		$item['id'] = (int)$item['id']; // typecast back to correct type
		
		return $item;	
	}
	
}
