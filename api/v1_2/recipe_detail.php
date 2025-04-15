<?php
/** 
 * Copyright (c) 2014-2019 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api/v1_1/recipe_detail.php');

class Onyx_Controller_Api_v1_2_Recipe_Detail extends Onyx_Controller_Api_v1_1_Recipe_Detail {
    
    /**
     * get data
     */
    
    public function getData() {
        
        $data = parent::getData();
        
        return array($data); // API v1.2 and v1.3 incorrectly returns "items.item.*", TODO: fix in v1.4 and change to "items.*" or "detail.*"
        
    }
    
    /**
     * formatItem
     */
     
    static function formatItem($original_item) {
        
        if (!is_array($original_item)) return false;
        
        $item = array();
        
        $protocol = onyxDetectProtocol();
        
        $item['id'] = (int)$original_item['id'];
        $item['title'] = $original_item['title'];
        $item['description'] = preg_replace("/[\r\n\t]/", " ", strip_tags($original_item['description']));
        $item['instructions'] = preg_replace("/[\r\n\t]/", " ", $original_item['instructions']);
        $item['url'] = "$protocol://{$_SERVER['HTTP_HOST']}/recipe/" . $original_item['id'];
        $item['priority'] = (int)$original_item['priority'];
        $item['created'] = $original_item['created'];
        $item['modified'] = $original_item['modified'];
        $item['ingredients'] = self::getIngredients($item['id']);
        $item['categories'] = self::getCategories($item['id']);
        $item['images'] = array("$protocol://{$_SERVER['HTTP_HOST']}/image/" . $original_item['image']['src']);
        $item['video'] = $original_item['video_url'];
        $item['comments'] = array();
        $item['rating'] = self::getRating($item['id']);
        $item['serving_people'] = (int)$original_item['serving_people'];
        $item['preparation_time'] = (int)$original_item['preparation_time'];
        $item['cook_time'] = (int)$original_item['cooking_time'];
        $item['recommended_wines'] = array();//$this->getRecommendedWines($post->ID);
        $item['related_offers'] = array();//$this->getSpecialOffersForRecipe($post->ID);
        $item['meal_types'] = array();//$this->getMealTypesForRecipe($post->ID);
        
        return $item;
        
    }
    
    /**
     * getCategories
     */
    
    static function getCategories($recipe_id) {
        
        if (!is_numeric($recipe_id)) return false;
        
        require_once('models/ecommerce/ecommerce_recipe.php');
        $Recipe = new ecommerce_recipe();
        
        return $Recipe->getRelatedTaxonomy($recipe_id);
        
    }
    
}
