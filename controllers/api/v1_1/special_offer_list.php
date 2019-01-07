<?php
/** 
 * Copyright (c) 2013-2015 Onxshop Ltd (https://onxshop.com)
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
        
        $records = $this->getOffersList();
        
        $data = array();
        
        foreach($records as $record) {

            $item = $this->formatItem($record);
            $data[] = $item;
            
        }
            
        return $data;
        
    }
    
    /**
     * formatItem
     */
     
    public function formatItem($original_item) {
        
        $item = parent::formatItem($original_item);
        $item['id'] = (int)$item['id']; // typecast back to correct type
        
        // hack specific for consumer aaGheo5b - requires fixed ID for 'This Weeks Offers'
        // TODO: in v1.4 create option to have an extra attribute allowing to search for specific offers
        if ($this->GET['api_key'] == 'aaGheo5b') {
            if (strtolower(trim($item['group_title'])) == 'this weeks offers') $item['group_id'] = 6; // hard code This Weeks Offers ID
        }
        
        return $item;   
    }
    
}
