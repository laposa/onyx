<?php
/**
 * Copyright (c) 2013-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('models/ecommerce/ecommerce_recipe.php');

class Onxshop_Controller_Component_Ecommerce_Recipe_List extends Onxshop_Controller {

	/**
	 * main action (only a router in this case)
	 */

	public function mainAction()
	{
		// init models
		$Recipe = new ecommerce_recipe();

		// get taxonomy ids
		if (empty($this->GET['taxonomy_tree_id'])) {
			$taxonomy_ids = array();
		} else {
			$taxonomy_ids = explode(",", $this->GET['taxonomy_tree_id']);
			// validate input
			if (!is_array($taxonomy_ids)) return false;
			foreach ($taxonomy_ids as $taxonomy_id) 
				if (!is_numeric($taxonomy_id)) return false;
		}

		// is there a limit?
		if  (is_numeric($this->GET['limit_from'])) $limit_from = $this->GET['limit_from'];
		else $limit_from = 0;
		if (is_numeric($this->GET['limit_per_page'])) $limit_per_page = $this->GET['limit_per_page'];
		else $limit_per_page = 25;

		// is there requested sorting?
		if ($this->GET['sort']['by'] && in_array($this->GET['sort']['by'], array('title', 'created', 'modified', 'priority', 'share_counter'))) $sort_by = $this->GET['sort']['by'];
		else $sort_by = 'modified';
		if ($this->GET['sort']['direction'] && in_array($this->GET['sort']['direction'], array('DESC', 'ASC'))) $sort_direction = $this->GET['sort']['direction'];
		else $sort_direction = 'DESC';

		// image role
		if ($this->GET['image_role']) $image_role = $this->GET['image_role'];
		else $image_role = 'teaser';

		/**
		 * get the list
		 */
		 
		$list = $Recipe->getRecipeListForTaxonomy($taxonomy_ids, $sort_by, $sort_direction, $limit_from, $limit_per_page, $image_role);

		$this->parseItems($list);

		if ($this->GET['display_pagination'] == 1) {
		
			$count = $Recipe->getRecipeCountForTaxonomy($taxonomy_ids);
			$_Onxshop_Request = new Onxshop_Request("component/pagination~limit_from=$limit_from:limit_per_page=$limit_per_page:count=$count~");
			$this->tpl->assign('PAGINATION', $_Onxshop_Request->getContent());
			
		}

		return true;
	}

	/**
	 * Parse recipe list items
	 */
	public function parseItems(&$list)
	{
		foreach ($list as $i => $item) {
			
			$this->parseItem($item);
			
		}
	}
	
	/**
	 * Parse one item
	 */
	 
	public function parseItem($item, $block_name = 'content.item')
	{
			
			$this->tpl->assign("ITEM", $item);

			if ($item['image']['src']) $this->tpl->parse("$block_name.image");

			if ($item['review']['count'] > 0) {
				
				$rating = round($item['review']['rating']);
				$_Onxshop_Request = new Onxshop_Request("component/rating_stars~rating={$rating}~");
				$this->tpl->assign('RATING_STARS', $_Onxshop_Request->getContent());
				if ($item['review']['count'] == 1) $this->tpl->assign('REVIEWS', 'Review');
				else $this->tpl->assign('REVIEWS', 'Reviews');
				
				$this->tpl->parse("$block_name.reviews");
			}

			$this->tpl->parse("$block_name");
	}

}
