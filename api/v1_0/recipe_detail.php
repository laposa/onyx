<?php
/** 
 * Copyright (c) 2013-2019 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api.php');

class Onyx_Controller_Api_v1_0_Recipe_Detail extends Onyx_Controller_Api {

    /**
     * get data
     */
    
    public function getData() {
        
        /**
         * input
         */
         
        if (is_numeric($this->GET['recipe_id'])) $recipe_id = $this->GET['recipe_id'];
        else {
            //msg("missing recipe_id", 'error');
            $data = array();
            $data['message'] = "missing recipe_id";
            $data['status'] = 400;
            
            return $data;
            
        }
        
        /**
         * initialize
         */
         
        require_once('models/ecommerce/ecommerce_recipe.php');
        $Recipe = new ecommerce_recipe();
        
        /**
         * get recipe detail
         */

        $item = $Recipe->getDetail($recipe_id);

        /**
         * get recipe image
         */

        require_once('models/ecommerce/ecommerce_recipe_image.php');
        $Image = new ecommerce_recipe_image();

        $images = $Image->listFiles($item['id']);
        if (count($images) > 0) {
            $item['image']['src'] = $images[0]['src'];
            $item['image']['title'] = $images[0]['title'];
        }

        /**
         * return the result
         */

        if ($item['publish'] == 1) return $this->formatItem($item);
        else return array();
        
        return $data;
        
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
        $item['video'] = (int)self::getVideoIdFromUrl($original_item['video_url']);
        $item['comments'] = array();
        $item['rating'] = self::getRating($item['id']);
        $item['serving_people'] = (int)$original_item['serving_people'];
        $item['preparation_time'] = (int)$original_item['preparation_time'];
        $item['cook_time'] = (int)$original_item['cooking_time'];
        $item['recommended_wines'] = array();//$this->getRecommendedWines($post->ID);
        $item['related_offers'] = array();//$this->getSpecialOffersForRecipe($post->ID);
        $item['meal_types'] = self::getMealTypesFromCategories($item['categories']);
        
        return $item;
        
    }
    
    /**
     * getIngredients
     */
    
    static function getIngredients($recipe_id) {
        
        if (!is_numeric($recipe_id)) return false;
        
        require_once('models/ecommerce/ecommerce_recipe_ingredients.php');
        $Ingredients = new ecommerce_recipe_ingredients();
        
        return $Ingredients->getIngredientsForRecipeOptimised($recipe_id);
        
    }
    
    /**
     * getCategories
     */
    
    static function getCategories($recipe_id) {
        
        if (!is_numeric($recipe_id)) return false;
        
        require_once('models/ecommerce/ecommerce_recipe.php');
        $Recipe = new ecommerce_recipe();
        
        $categories_system = $Recipe->getRelatedTaxonomy($recipe_id);
        $categories = array();
        
        foreach ($categories_system as $k=>$item) {
            $categories[$k] = array();
            $categories[$k]['id'] = $item['id'];
            $categories[$k]['title'] = $item['title'];
            $categories[$k]['priority'] = $item['priority'];
            $categories[$k]['usage_count'] = 0;
        }
        
        /**
         * historic reasons...
         */
         
        if (count($categories) == 0) {
            $categories[0] = array();
            $categories[0]['id'] = 1;
            $categories[0]['title'] = 'Uncategorised';
            $categories[0]['priority'] = 0;
            $categories[0]['usage_count'] = 0;
        }
        
        return $categories;
    }
    
    /**
     * getMealTypesFromCategories
     */
     
    static function getMealTypesFromCategories($categories) {
        
        $meal_types = array();
        foreach ($categories as $item) {
            
            $meal_types[] = array('id'=>$item['id'], 'title'=>$item['title']);
            
        }
        
        return $meal_types;
        
    }
    
    /**
     * getVideoIdFromUrl
     */
     
    static function getVideoIdFromUrl($video_url) {
        
        if (!empty($video_url)) {

            $vimeo_video_id = false;
            $youtube_video_id = false;

            // detect vimeo
            preg_match("/https?:\/\/vimeo.com\/(\d+)/", $video_url, $matches);
            if (isset($matches[1]) && is_numeric($matches[1])) $vimeo_video_id = $matches[1];

            // detect youtube
            //preg_match("/https?:\/\/youtu.be\/([0-9a-zA-Z]+)/", $video_url, $matches);
            //if (isset($matches[1]) && !empty($matches[1])) $youtube_video_id = $matches[1];
            //preg_match("/https?:\/\/www.youtube.com\/watch\?v=([0-9a-zA-Z-]+)/", $video_url, $matches);
            //if (isset($matches[1]) && !empty($matches[1])) $youtube_video_id = $matches[1];

            if (is_numeric($vimeo_video_id)) $video_id = $vimeo_video_id;
            else $video_id = 0;
            
        } else {
            
            $video_id = 0;
            
        }
            
        return $video_id;
        
    }
    
    /**
     * getRating
     */
     
    static function getRating($recipe_id) {
        
        if (!is_numeric($recipe_id)) return false;
        
        require_once('models/ecommerce/ecommerce_recipe_review.php');
        $Review = new ecommerce_recipe_review();
        $review_data = $Review->getRating($recipe_id);
        
        $rating = array();
        $rating['value'] = (int)$review_data['rating'];
        $rating['votes_sum'] = (int)$review_data['count'];
        $rating['voters_sum'] = (int)$review_data['count'];
        
        return $rating;
        
    }
}
