<?php
/** 
 * Copyright (c) 2015-2019 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api/v1_3/recipe_search.php');

class Onxshop_Controller_Api_v1_4_Recipe_Search extends Onxshop_Controller_Api_v1_3_Recipe_Search {

    static $thumbnail_size = 200;
    
    /**
     * formatItem
     */
     
    public function formatItem($original_item) {
        
        if (!is_array($original_item)) return false;
        
        $protocol = onxshopDetectProtocol();
        
        $item = array();
        $item['id'] = $original_item['id'];
        $item['title'] = $original_item['title'];
        $item['description'] = strip_tags($original_item['description']);
        $item['images'] = array("$protocol://" . $_SERVER['HTTP_HOST'] . "/image/" . $original_item['image']['src']);
        $item['thumbnails'] = array("$protocol://" . $_SERVER['HTTP_HOST'] . "/thumbnail/" . self::$thumbnail_size . '/'. $original_item['image']['src']);
        $item['video'] = $original_item['video_url'];
        $item['ready_time'] = $original_item['preparation_time'] + $original_item['cooking_time'];
        $item['categories'] = $this->getCategories($original_item);
        $item['url'] = "$protocol://" . $_SERVER['HTTP_HOST'] . "/recipe/{$original_item['id']}";
        
        return $item;
        
    }
        
    /**
     * getCategories
     */
     
    public function getCategories($original_item) {
        
        $categories = $this->Recipe->getRelatedTaxonomy($original_item['id']);
        
        return $categories;
            
    }

}
