<?php
/**
 * Copyright (c) 2013-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/node/page/default.php');
require_once('models/common/common_node.php');
require_once('models/ecommerce/ecommerce_recipe.php');
require_once('models/ecommerce/ecommerce_recipe_taxonomy.php');

class Onxshop_Controller_Node_Page_Recipe extends Onxshop_Controller_Node_Page_Default {

	/**
	 * main action
	 */
	public function mainAction()
	{
		$this->recipeDetail($this->GET['recipe_id']);
		
		/**
		 * standard page actions
		 */
		 
		$this->processContainers();
		$this->processPage();
		
		return true;
	}
	
	/**
	 * hook before parsing
	 */
	 
	public function parseContentTagsBeforeHook()
	{

		parent::parseContentTagsBeforeHook();

		/**
		 * pass GET.recipe_id into template
		 */
		 
		$Node = new common_node();
		$node_data = $Node->nodeDetail($this->GET['id']);
		$this->GET['recipe_id'] = $node_data['content'];

		/**
		 * pass GET.taxonomy_ids into template
		 */
		 
		$Recipe_Taxonomy = new ecommerce_recipe_taxonomy();
		$taxonomy_ids = (array) $Recipe_Taxonomy->getRelationsToRecipe($this->GET['recipe_id']);
		$this->GET['taxonomy_tree_id'] = implode(",", $taxonomy_ids);
		
		/**
		 * rating & reviews
		 */
		
		require_once('models/ecommerce/ecommerce_recipe_review.php');
		$Review = new ecommerce_recipe_review();
		$review_data = $Review->getRating($this->GET['recipe_id']);
		
		if ($review_data['count'] > 0) {
				
				$rating = round($review_data['rating']);
				$_Onxshop_Request = new Onxshop_Request("component/rating_stars~rating={$rating}~");
				$this->tpl->assign('RATING_STARS', $_Onxshop_Request->getContent());
				if ($review_data['count'] == 1) $this->tpl->assign('REVIEWS', 'Review');
				else $this->tpl->assign('REVIEWS', 'Reviews');
				
				$this->tpl->assign('REVIEW', $review_data);
				
				$this->tpl->parse('content.reviews');
		}

	}
	
	/**
	 * recipeDetail
	 */
	 
	public function recipeDetail($recipe_id) {
		
		if (!is_numeric($recipe_id)) return false;
		
		$Recipe = new ecommerce_recipe();
		$recipe = $Recipe->getDetail($recipe_id);

		if ($recipe) {
		
			/**
			 * get taxonomy_class
			 */
			 
			$related_taxonomy = $Recipe->getRelatedTaxonomy($recipe_id);
			$recipe['taxonomy_class'] = $this->createTaxonomyClass($related_taxonomy);
			
			/**
			 * save product taxonomy_class to registry
			 */
			
			$this->saveBodyCssClass($recipe['taxonomy_class']);
			
			/**
			 * singular/plurar text
			 */
			 			
			$recipe['persons'] = $recipe['serving_people'] == 1 ? 'person' : 'persons';
			$recipe['minutes'] = $recipe['cooking_time'] == 1 ? 'minute' : 'minutes';

			$this->tpl->assign("RECIPE", $recipe);

			/**
			 * video
			 */
			if (!empty($recipe['video_url'])) {

				$video_url = urlencode($recipe['video_url']);
				$_Onxshop_Request = new Onxshop_Request("component/video~video_url={$video_url}~");
				$this->tpl->assign('RECIPE_VIDEO', $_Onxshop_Request->getContent());

			}
		}

		return true;
	}

	/**
	 * getOpenGraphImage
	 */
	 
	public function getOpenGraphImage($node_id, $content = false) {
	
		if (is_numeric($content)) {
			
			$recipe_id = $content;
			
			require_once('models/ecommerce/ecommerce_recipe_image.php');
			$Image = new ecommerce_recipe_image();

			$image_list = $Image->listFiles($recipe_id);
			
			if (is_array($image_list) && count($image_list) > 0) return $image_list[0];
		
		} else {
			
			// Option to implement og:image associated to common_node record
			
		}

		return false;

	}

}