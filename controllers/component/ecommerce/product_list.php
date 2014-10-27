<?php
/**
 * Copyright (c) 2010-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Ecommerce_Product_List extends Onxshop_Controller {

	/**
	 * main action (only a router in this case)
	 */
	 
	public function mainAction() {
	
		/**
		 * input variables
		 */
		
		$http_get_query = http_build_query($this->GET, '', ':');
		
		/**
		 * read from session or input
		 */
		 
		if ($this->GET['product_list_mode']) {
		
			$product_list_mode = $this->GET['product_list_mode'];
				
		} else if ($_SESSION['product_list_mode']) {
		
			$product_list_mode = $_SESSION['product_list_mode'];
		
		} else {
		
			$product_list_mode = $GLOBALS['onxshop_conf']['global']['product_list_mode']; //setting from database common_configuration, object: global, property: product_list_mode
		
		}
		
		/**
		 * save to session
		 */
		
		$_SESSION['product_list_mode'] = $product_list_mode;
		
		/**
		 * choose appropriate controller
		 */
		
		switch ($product_list_mode) {
			
			case 'grid':
			
				if (is_numeric($GLOBALS['onxshop_conf']['global']['product_list_grid_columns'])) $number_of_columns = $GLOBALS['onxshop_conf']['global']['product_list_grid_columns']; //setting from database common_configuration, object: global, property: product_list_grid_columns
				else $number_of_columns = 4;
				
				$mode = $number_of_columns . 'columns';
				
				//hack to display all products when in grid view
				$_GET['limit_per_page'] = 300;
				
			break;
			
			case 'shelf':
			default:
			
				$mode = 'shelf';
				
			break;
			
		}
		
		/**
		 * call sub controller
		 */
		
		$_Onxshop_Request = new Onxshop_Request("component/ecommerce/product_list_$mode~$http_get_query~");
		$this->tpl->assign('PRODUCT_LIST', $_Onxshop_Request->getContent());
	
		return true;
	}
	
	/**
	 * process product list
	 */
	 
	public function processProductList() {	 
	
		/**
		 * initialize product
		 */
		
		require_once('models/ecommerce/ecommerce_product.php');
		$this->Product = new ecommerce_product();
		
		/**
		 * image size
		 */
		
		if (is_numeric($this->GET['image_width'])) $image_width = $this->GET['image_width'];
		else $image_width = $GLOBALS['onxshop_conf']['global']['product_list_image_width'];
		
		/**
		 * get product variety list
		 *
		 */
		
		$product_variety_list = $this->getProductVarietyList();
		
		/**
		 * don't continue if product list is empty, but don't return false
		 */
		
		if (!is_array($product_variety_list)) {
			return true;
		}

		/**
		 * reformat list
		 */
		
		$product_id_list = array();
		
		foreach ($product_variety_list AS $item) {
			if ($item['node_publish']) {
				if (!in_array($item['product_id'], $product_id_list)) {
					$product_list[$item['product_id']] = $item;
					$product_id_list[] = $item['product_id'];
				//if it's a different variety and price is smaller, use this
				} else if ($product_list[$item['product_id']]['price'] > $item['price'] && $product_list[$item['product_id']]['variety_id'] != $item['variety_id']) {
					$product_list[$item['product_id']] = $item;
				}
			}
		}
		
		/**
		 * don't continue if product list is empty, but don't return false
		 */
		
		if (!is_array($product_list)) {
			return true;
		}
		
		/**
		 * Sorting
		 */
		 
		
		$this->_prepareSorting();
		
		/**
		 * read variables from session
		 */
			
		$sortby = $_SESSION['product_list-sort-by'];
		$direction = $_SESSION['product_list-sort-direction'];
	
		
		if ($this->GET['display_sorting']) {

			$this->_displaySorting();

		} else if (is_array($this->GET['product_id_list']) && $this->GET['product_id_list_force_sorting_as_listed']) {

			// force sorting by priority if product_id_list_force_sorting_as_listed
			$sortby = 'priority'; //priority should have been modified to reflect product_list_list order
			$direction = 'DESC';

		}
		
		$product_list = $this->_processSorting($product_list, $sortby, $direction);
		
		/**
		 * Initialize pagination variables
		 */
		
		if  (is_numeric($this->GET['limit_from'])) $from = $this->GET['limit_from'];
		else $from = 0;
		if (is_numeric($this->GET['limit_per_page'])) $per_page = $this->GET['limit_per_page'];
		else $per_page = $GLOBALS['onxshop_conf']['global']['product_list_per_page'];
		
		
		$limit = "$from,$per_page";
		
		
		/**
		 * Display pagination
		 */
		
		if (is_numeric($this->GET['display_pagination'])) $display_pagination = $this->GET['display_pagination'];
		else $display_pagination = 1;
		
		if ($display_pagination) {
			//$link = "/page/" . $_SESSION['active_pages'][0];
			$count = count($product_list);
			$_Onxshop_Request = new Onxshop_Request("component/pagination~limit_from=$from:limit_per_page=$per_page:count=$count~");
			$this->tpl->assign('PAGINATION', $_Onxshop_Request->getContent());
		}
		
		/**
		 * Display items
		 * 
		 */
		
		if ($product_list = $this->_prepareItems($product_list)) {
			return $this->processItems($product_list, $image_width, $from, $per_page);
		} else {
			return false;
		}
	}
	
	
	/**
	 * get product variety list
	 *
	 * check requested list
	 * can be product_id_list or node_id 
	 * if node_id not provided, use first parent page
	 */
	
	function getProductVarietyList() {
		
		$filter = array();
		
		//this filter option will ensure listing only enabled products and varieties
		$filter['publish'] = 1;
			
		//image role filter
		if (in_array($this->GET['image_role'], array('default', 'teaser', 'feature', 'background', 'RTE'))) $filter['image_role'] = $this->GET['image_role'];
		else $filter['image_role'] = 'main';
		
		$this->tpl->assign('IMAGE_ROLE', $filter['image_role']);
		
		if (is_array($this->GET['product_id_list'])) {
			
			/*
			passed from this controllers:
			./component/ecommerce/best_buys.php
			./component/ecommerce/recently_viewed_products.php
			./component/ecommerce/product_related_basket.php
			./component/ecommerce/product_related_to_customer.php
			./component/ecommerce/product_related.php
			*/

			$filter['product_id_list'] = $this->GET['product_id_list'];
			
			$product_variety_list = $this->Product->getFilteredProductList($filter, GLOBAL_LOCALE_CURRENCY);
			
			/**
			 * modify priority according sort of product_id_list
			 */
			
			if ($this->GET['product_id_list_force_sorting_as_listed']) {
			
				foreach ($product_variety_list as $key=>$item) {
					$product_variety_list[$key]['priority'] = 1000 - array_search($item['product_id'], $this->GET['product_id_list']);
				}
			
			}
			
		} else {
		
			/**
			 * initialize node
			 */
			 
			require_once('models/common/common_node.php');
			$Node = new common_node();
		
			/**
			* Find node_id
			* 1. can be provided
			* 2. detected from place where the component sits
			*/
				
			if (is_numeric($this->GET['id'])) {
				$node_id = $this->GET['id'];
				//TODO find first parent page from given "id"
				//$node_id = $Node->getFirstParentPage($_SESSION['active_pages']);
			} else {
				$node_id = $Node->getFirstParentPage($_SESSION['active_pages']);
				//don't continue if node_id is not numeric (i.e. empty SESSION.active_pages)
				if (!is_numeric($node_id)) return false;
			}
			
			/**
			 * filter, find if taxonomy is set for node
			 */
	
			if ($this->GET['taxonomy_tree_id']) {

				$taxonomy = explode(",", $this->GET['taxonomy_tree_id']);
				if (count($taxonomy) > 0 && is_numeric($taxonomy[0])) $filter['taxonomy_json'] = json_encode($taxonomy);

			} else if ($taxonomy = $Node->getTaxonomyForNode($node_id)) {
				
				//add extra taxonomy filter
				$taxonomy = $this->addTaxonomyFilter($taxonomy);
				
				$filter['taxonomy_json'] = json_encode($taxonomy);
				
			} else {
				
				//add taxonomy filter from session or POST
				$taxonomy = $this->addTaxonomyFilter();
				
				if (count($taxonomy) > 0) $filter['taxonomy_json'] = json_encode($taxonomy);
				
			}
			
			/**
			 * add keyword filter
			 */
			
			$filter['keyword'] = $this->addKeywordFilter();
			
			
			/**
			 * get product list
			 *
			 */
			
			$product_variety_list = $this->Product->getFilteredProductList($filter, GLOBAL_LOCALE_CURRENCY);
			
		}
		
		//print_r($product_variety_list);
		
		return $product_variety_list;
	}
	
	/**
	 * process items
	 */
	 
	function processItems($product_list, $image_width, $from, $per_page, $divide_after = 1) {
	
		return $this->_displayItems($product_list, $image_width, $from, $per_page, $divide_after);
		
	}
	
	/**
	 * prepare items
	 * 
	 */
	
	function _prepareItems($product_list) {
		
		if (!is_array($product_list)) {
			msg("product_list.displayItems: product list is not an array", "error");
			return false;
		}
		
		/**
		 * reformat for correct pagination
		 */
		foreach ($product_list as $item) {
			$p[] = $item;
		}
			
		$product_list = $p;
		
		return $product_list;		
	}

	/**
	 * display items
	 * Implemented pagination
	 */
	
	function _displayItems($product_list, $image_width, $from, $per_page, $divide_after, $item_block = 'item') {
		
		/**
		 * pagination
		 */

		$product_list_paginated = array();
		
		foreach ($product_list as $i=>$item) {
			
			if ($i >= $from  && $i < ($from + $per_page) ) {
				
				/**
				 * odd_even_class
				 */
				 
				$odd_even = ( $odd_even == 'odd' ) ? 'even' : 'odd';
				$item['odd_even_class'] = $odd_even;
				
				/**
				 * add related_taxonomy
				 */
				
				$related_taxonomy = explode(',', $item['taxonomy']);
				
				/**
				 * create taxonomy_class from related_taxonomy
				 */
				
				$item['taxonomy_class'] = '';
				
				if (is_array($related_taxonomy)) {
					foreach ($related_taxonomy as $t_item) {
						$item['taxonomy_class'] .= "t{$t_item} ";
					}
				}
						
				/**
				 * add to array
				 */
		
				$product_list_paginated[] = $item;
				
			}
		}
		
		
	
		/**
		 * display paginated
		 */
		 
		$count = count($product_list_paginated);
		
		foreach ($product_list_paginated as $i=>$item) {
		
			/**
			 * first_last_class
			 */
			
			if ($divide_after > 1) {
			
				if ($i == 0 || $i%$divide_after == 0)  $item['first_last_class'] = 'first';
				else if (($i + 1)%$divide_after == 0) $item['first_last_class'] = 'last';
				else $item['first_last_class'] = '';
			
			} else {
				
				if ($i == 0) $item['first_last_class'] = 'first';
				else if ($i == ($count - 1)) $item['first_last_class'] = 'last';
				else $item['first_last_class'] = '';
				
			}
				
			/**
			 * parse item
			 */
			 
			$this->_parseItem($item, $i, $count, $image_width, $divide_after, $item_block);
		}
		
		return true;
	}
	
	/**
	 * parse one item
	 */
	 
	function _parseItem($item, $i, $count, $image_width, $divide_after, $item_block = 'item') {

		/*
		//optionally we can use image wrapper
		$Image = new Onxshop_Request("component/image&relation=product&role=main&width=$image_width&node_id={$item['product_id']}&limit=0,1");
		$this->tpl->assign('IMAGE_PRODUCT', $Image->getContent());
		*/
		$this->tpl->assign('IMAGE_WIDTH', $image_width);
		
		// display divider after each $divide_after items
		if (($i + 1)%$divide_after == 0) {
			// but not the last one
			if (($i+1) != $count) $this->tpl->parse("content.$item_block.divider");
		}
				
		$this->tpl->assign("ITEM", $item);
		
		/**
		 * rating
		 */
		 
		if ($item['review_count'] > 0) {
			$rating = round($item['review_rating']);
			$_Onxshop_Request = new Onxshop_Request("component/rating_stars~rating={$rating}~");
			$this->tpl->assign('RATING_STARS', $_Onxshop_Request->getContent());
			if ($item['review_count'] == 1) $this->tpl->assign('REVIEWS', 'Review');
			else $this->tpl->assign('REVIEWS', 'Reviews');
			$this->tpl->parse("content.$item_block.reviews");
		} else {
			$this->tpl->assign('RATING_STARS', '');
		}
		
		
		$this->tpl->parse("content.$item_block");
				
	}
	
	/**
	 * prepare sorting variables and save them in the session
	 */
		 
	function _prepareSorting() {
		
		/**
		 * sort[by]
		 */
		  
		if ($this->GET['sort']['by'] && in_array($this->GET['sort']['by'], array('popularity', 'price', 'name', 'priority', 'created', 'share_counter'))) {
		
			$sortby = $this->GET['sort']['by'];
		
		} else if (!$_SESSION['product_list-sort-by']) {
		
			//default
			$sortby = $GLOBALS['onxshop_conf']['global']['product_list_sorting'];//set in global configuration
		
		} else {

			return;

		}
		
		/**
		 * sort[direction]
		 */
		 
		if ($this->GET['sort']['direction'] && in_array($this->GET['sort']['direction'], array('DESC', 'ASC'))) {
		
			$direction = $this->GET['sort']['direction'];
		
		} else if (!$_SESSION['product_list-sort-direction']) {
		
			//default
			$direction = 'DESC';
		
		} else {

			return; // if sorting not forced by GET, then don't alter session settings

		}
		
		/**
		 * save to session
		 */
		 
		$_SESSION['product_list-sort-by'] = $sortby;
		$_SESSION['product_list-sort-direction'] = $direction;
	}
	
	/**
	 * process sorting
	 */
	 
	function _processSorting($product_list, $sortby, $direction) {
		
		/**
		 * reorder
		 */
		 
		//msg("Sorted by $sortby $direction");
		
		switch ($sortby) {
			
			case 'price':
				if ($direction == 'DESC') $direction = SORT_DESC;
				else $direction = SORT_ASC;
				array_multisort($product_list, $direction);
				break;
				
			case 'name':
				$product_list = php_multisort($product_list, array(array('key'=>'product_name', 'sort'=>$direction), array('key'=>'product_id', 'type'=>'numeric')));
		
				foreach ($product_list as $item) {
					$p[] = $item;
				}
				
				$product_list = $p;
				break;
				
			case 'popularity':
				/* faster */
				/*$most_popular = $Product->getMostPopularProducts('DESC', 1000);
				foreach ($most_popular as $item) {
					$popularity[$item['product_id']] = $item['count'];
				}
				
				foreach ($product_list as $i=>$item) {
					$product_list[$i]['popularity'] = $popularity[$item['product_id']];
				}*/
				
				/* more accurate */
				foreach ($product_list as $i=>$item) {
					$product_list[$i]['popularity'] = $this->Product->getPopularity($item['product_id']);
				}
				
				$product_list = php_multisort($product_list, array(array('key'=>'popularity', 'sort'=>$direction), array('key'=>'product_id', 'type'=>'numeric')));
				
				break;
				
			case 'priority':
				$product_list = php_multisort($product_list, array(array('key'=>'priority', 'sort'=>$direction), array('key'=>'product_id', 'type'=>'numeric')));
		
				foreach ($product_list as $item) {
					$p[] = $item;
				}
				
				$product_list = $p;
				break;
				
			case 'share_counter':
				
				$product_list = php_multisort($product_list, array(array('key'=>'share_counter', 'sort'=>$direction), array('key'=>'product_id', 'type'=>'numeric')));
		
				foreach ($product_list as $item) {
					$p[] = $item;
				}
				
				$product_list = $p;
				break;
			
			case 'created':
			default:
				//product_id, or modified, or TODO created attribute for ecommerce_product
				$product_list = php_multisort($product_list, array(array('key'=>'product_id', 'sort'=>$direction), array('key'=>'product_id', 'type'=>'numeric')));
		
				foreach ($product_list as $item) {
					$p[] = $item;
				}
				
				$product_list = $p;
				break;
				
		}
		
		return $product_list;
	}
	
	/**
	 * display sorting
	 */
	 
	function _displaySorting() {
	
		/**
		 * read variables from the session
		 */
			
		$sortby = $_SESSION['product_list-sort-by'];
		$direction = $_SESSION['product_list-sort-direction'];
		
		/**
		 * call and assign result from the sorting interface controller
		 */
		 
		$_Onxshop_Request = new Onxshop_Request("component/ecommerce/product_list_sorting~sort[by]={$sortby}:sort[direction]={$direction}~");
		$this->tpl->assign('SORTING', $_Onxshop_Request->getContent());
			
	}
	
	/**
	 * add taxonomy filter
	 */
	 
	public function addTaxonomyFilter($taxonomy = array()) {
	
		if (!is_array($taxonomy)) return false;
	
		//add extra taxonomy if filter is submitted
		if (is_array($_POST['filter'])) {
					
			foreach ($_POST['filter'] as $filter_item) {
					
				if (is_numeric($filter_item) && $filter_item > 0) $taxonomy[] = $filter_item;
						
			}
			
		} else if (is_array($_SESSION['filter'])) {
			
			foreach ($_SESSION['filter'] as $filter_item) {
					
				if (is_numeric($filter_item) && $filter_item > 0) $taxonomy[] = $filter_item;
						
			}
			
		} else if (is_numeric($this->GET['taxonomy_tree_id'])) {
		
			$taxonomy[] = $this->GET['taxonomy_tree_id'];
			
		}
		
		return $taxonomy;
	}
	
	/**
	 * add keyword filter
	 */
	 
	public function addKeywordFilter() {
	
		$keywords = '';
		
		if (is_array($_POST['filter'])) {
					
			if ($_POST['filter']['keywords']) $keywords = addslashes(trim($_POST['filter']['keywords']));
			
		} else if ($_SESSION['filter']['keywords']) {
			
			$keywords = addslashes(trim($_SESSION['filter']['keywords']));
			
		}
		
		if ($keywords == '') $keywords = false;
		
		return $keywords;
	}

}
