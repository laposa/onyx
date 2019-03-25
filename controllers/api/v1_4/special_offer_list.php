<?php
/** 
 * Copyright (c) 2015-2019 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api/v1_3/special_offer_list.php');

class Onxshop_Controller_Api_v1_4_Special_Offer_List extends Onxshop_Controller_Api_v1_3_Special_Offer_List {
    
    static $thumbnail_size = 200;
    
    /**
     * getOffersList
     */
     
    public function getOffersList() {

        /**
         * initialize
         */
         
        require_once('models/ecommerce/ecommerce_offer.php');
        $Offer = new ecommerce_offer();
        
        /**
         * get special offer list
         */
        
        $records = $Offer->getActiveOffers(true); // includeForthcoming

        return $records;
        
    }
    
    /**
     * formatItem
     */
     
    public function formatItem($original_item) {
        
        if (!is_array($original_item)) return false;
        $original_item['price_formatted'] = $this->formatPrice($original_item['price'], $original_item['currency_code']);
        
        require_once('models/ecommerce/ecommerce_product.php');
        $Product = new ecommerce_product();
        $product_detail = $Product->getProductDetail($original_item['product_id']);
        
        $protocol = onxshopDetectProtocol();
        
        $item['id'] = $original_item['offer_id'];
        $item['title'] = $product_detail['name'];
        $item['content'] = strip_tags($product_detail['description']);
        $item['url'] = "$protocol://{$_SERVER['HTTP_HOST']}/product/{$original_item['product_id']}";
        $item['priority'] = $product_detail['priority'];
        $item['created'] = $product_detail['modified'];
        $item['modified'] = $product_detail['modified'];
        $item['images'] = array("$protocol://{$_SERVER['HTTP_HOST']}/image/" . $Product->getProductMainImageSrc($original_item['product_id']));
        $item['thumbnails'] = array("$protocol://{$_SERVER['HTTP_HOST']}/thumbnail/" . self::$thumbnail_size . '/' . $Product->getProductMainImageSrc($original_item['product_id']));
        $item['rondel'] = $this->getRoundelText($original_item);
        $item['rondel_image_url'] = $this->getRoundelImageSource($original_item);
        $item['price'] = money_format('%n', $original_item['price']);
        $item['start_date'] = $original_item['group_schedule_start'];
        $item['expiry_date'] = $original_item['group_schedule_end'];
        $item['categories'] = $Product->getRelatedTaxonomy($original_item['product_id']);
        $item['product_id'] = $product_detail['variety'][0]['sku'];//TODO this is showing only first ones
        //special offer group
        $item['group_id'] = $original_item['group_id'];
        $item['group_title'] = $original_item['group_title'];
        
        return $item;   
    }
}
