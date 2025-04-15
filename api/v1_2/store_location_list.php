<?php
/** 
 * Copyright (c) 2014-2019 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api/v1_1/store_location_list.php');

class Onyx_Controller_Api_v1_2_Store_Location_List extends Onyx_Controller_Api_v1_1_Store_Location_List {

    /**
     * formatItem
     */
     
    public function formatItem($record) {
        
        $item = array();
        
        $address_detail = $this->getAddressDetail($record);
            
        $item['id'] = $record['id'];
        $item['code'] = $record['code'];
        $item['title'] = (string)$record['title'];
        $item['address'] = (string)$address_detail['address'];
        $item['city'] = (string)$address_detail['city'];
        $item['county'] = (string)$address_detail['county'];
        $item['country'] = (string)$address_detail['country'];
        $item['latitude'] = $record['latitude'];
        $item['longitude'] = $record['longitude'];
        $item['opening_hours'] = (string)$record['opening_hours'];
        $item['phone'] = (string)$record['telephone'];
        $item['fax'] = '';
        $item['manager'] = (string)$record['manager_name'];
        $item['modified'] = $record['modified'];
        $item['categories'] = self::getCategories($record['id']);
        
        return $item;
    }
    
    /**
     * getCategories
     */
    
    static function getCategories($store_id) {
        
        if (!is_numeric($store_id)) return false;
        
        require_once('models/ecommerce/ecommerce_store.php');
        $Store = new ecommerce_store();
        
        return $Store->getRelatedTaxonomy($store_id);
        
    }
    
}
