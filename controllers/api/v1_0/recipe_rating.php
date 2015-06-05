<?php
/** 
 * Copyright (c) 2012-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api.php');

class Onxshop_Controller_Api_v1_0_Recipe_Rating extends Onxshop_Controller_Api {

	/**
	 * get data
	 */
	
	public function getData() {
		
		/**
		 * initialize
		 */
		 
		require_once('models/ecommerce/ecommerce_recipe_review.php');
		$this->RecipeReview = new ecommerce_recipe_review();
		$this->RecipeReview->setCacheAble(false);
		
		$data = array();
		
		/**
		 * check input
		 */
		if (is_numeric($this->GET['recipe_id'])) $recipe_id = $this->GET['recipe_id'];
		else {
			//msg("missing recipe_id", 'error');
			$message = "missing recipe_id";
			$status = 400;
		}
		
		if (is_numeric($this->GET['rating'])) $rating = $this->GET['rating'];
		else {
			$message = "missing rating value";
			$status = 400;
		}
		
		if ($status == 400) {
		
			$message .= ", Input data required: recipe_id, rating";
			
		} else {
		
			if (!in_array($rating, array(1,2,3,4,5))) {
				
				$message = "invalid rating value, acception only 1-5";
				$status = 400;
				
			} else {
			
				/**
				 * submit
				 */
				 
				if ($this->submitRating($recipe_id, $rating)) {
					
					$message = "Your rating was submitted";
					$status = 200;
					
					$new_rating = $this->RecipeReview->getRating($recipe_id);
					
					$data['rating_submitted'] = $rating;
					$data['rating'] = array(); 
					$data['rating']['value'] = round($new_rating['rating'], 2);
					$data['rating']['votes_sum'] = $new_rating['rating'] * $new_rating['count']; // TODO: remove in later API version
					$data['rating']['voters_sum'] = $new_rating['count'];
			
				} else {
				
					$message = "Recipe ID $recipe_id was not found";
					$status = 400;
					
				}
				
			}
		}
				
		$data['message'] = $message;
		$data['status'] = $status;
			
		return $data;
		
	}
	
	/**
	 * submitRating
	 */
	 
	public function submitRating($recipe_id, $rating) {
	
		if (!is_numeric($recipe_id)) return false;
		if (!is_numeric($rating)) return false;
		
		if ($this->RecipeReview->insertNodeRatingWithoutReview($recipe_id, $rating)) {
			return $this->RecipeReview->getRating($recipe_id);
		}
		
	}
	
}
