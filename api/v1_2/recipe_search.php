<?php
/** 
 * Copyright (c) 2014-2019 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api/v1_1/recipe_search.php');

class Onyx_Controller_Api_v1_2_Recipe_Search extends Onyx_Controller_Api_v1_1_Recipe_Search {

    /**
     * formatItem
     */
     
    public function formatItem($original_item) {
        
        if (!is_array($original_item)) return false;
        
        $protocol = onyxDetectProtocol();
        
        $item = array();
        $item['id'] = $original_item['id'];
        $item['title'] = $original_item['title'];
        $item['description'] = strip_tags($original_item['description']);
        $item['image_thumbnail'] = "$protocol://" . $_SERVER['HTTP_HOST'] . "/image/" . $original_item['image']['src'];
        $item['video'] = $original_item['video_url'];
        $item['ready_time'] = $original_item['preparation_time'] + $original_item['cooking_time'];
        $item['meal_types'] = $this->getMealTypes($original_item);
        $item['categories'] = $this->getCategories($original_item);
        $item['url'] = "$protocol://" . $_SERVER['HTTP_HOST'] . "/recipe/{$original_item['id']}";
        
        return $item;
        
    }
    
}
