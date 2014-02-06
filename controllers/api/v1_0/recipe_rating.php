<?php
/** 
 * Copyright (c) 2012-2014 Laposa Ltd (http://laposa.co.uk)
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
		 
		require_once('models/wordpress/wordpress_recipe.php');
		$this->Recipe = new wordpress_recipe();
		
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
					$new_rating = $this->Recipe->getRating($recipe_id);
					
					$data['rating_submitted'] = $rating;
					$data['rating'] = $new_rating;
			
				} else {
				
					$message = "Recipe ID $recipe_id was not found";
					$status = 400;
					
				}
				
			}
		}
				
		$data['message'] = $message;
		$data['status'] = $status;
		
		//print_r($data);
			
		return $data;
		
	}
	
	/**
	 * submitRating
	 */
	 
	public function submitRating($recipe_id, $rating) {
	
		if (!is_numeric($recipe_id)) return false;
		if (!is_numeric($rating)) return false;
		
		return $this->Recipe->updateRating($recipe_id, $rating);
		
	}
	
}
