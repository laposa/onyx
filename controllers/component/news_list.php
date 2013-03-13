<?php
/**
 * Copyright (c) 2010-2012 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Component_News_List extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		return $this->newsListAction();

	}
	
	/**
	 * news list action
	 */
	 
	public function newsListAction() {
	
		/**
		 * initialise
		 */
		 
		require_once('models/common/common_node.php');
		$this->Node = new common_node();
		
		/**
		 * basic input data
		 */
		 
		if (is_numeric($this->GET['blog_node_id'])) $blog_node_id = $this->GET['blog_node_id'];
		else $blog_node_id = $this->Node->conf['id_map-blog'];
		
		$display_teaser_image = $this->GET['display_teaser_image'];

		/**
		 * check
		 */
		 
		if (!is_numeric($blog_node_id)) {
			msg("component/news_list: blog_node_id must be numeric", 'error');
			return false;
		}
		
		/**
		 * get detail of blog container node
		 */
		 		
		$news_list_detail = $this->Node->getDetail($blog_node_id);
		$this->tpl->assign('NEWS_LIST', $news_list_detail);

		/**
		 * get input variables
		 */
		 
		//if (is_numeric($this->GET['taxonomy_tree_id'])) $taxonomy_tree_id = $this->GET['taxonomy_tree_id'];
		//else $taxonomy_tree_id = '';
		$taxonomy_tree_id = $this->getTaxonomyList();
		
		if (is_numeric($this->GET['created'])) $created = $this->GET['created'];
		else if (preg_match('/[0-9]{4}-[0-9]{1,2}/', $this->GET['created'])) $created = $this->GET['created'];
		else $created = '';
		
		if (is_numeric($this->GET['publish'])) $publish = $this->GET['publish'];
		else $publish = '';
		
		if ($this->GET['display_pagination'] == 1) $display_pagination = 1;
		else $display_pagination = 0;
		
		/**
		 * image size
		 */
		 
		if (is_numeric($this->GET['image_width']) && $this->GET['image_width'] > 0) $image_width = $this->GET['image_width'];
		else $image_width = 125;
		
		if (is_numeric($this->GET['image_height']) && $this->GET['image_height'] > 0) $image_height = $this->GET['image_height'];
		else $image_height = 0;
		
		
		/**
		 * Initialize pagination variables
		 */
		
		if (is_numeric($this->GET['limit_from']) && is_numeric($this->GET['limit_per_page'])) {
			$limit_from = $this->GET['limit_from'];
			$limit_per_page = $this->GET['limit_per_page'];
		} else {
			$limit_from = 0;
			$limit_per_page = 10;
		}
		
		/**
		 * disable pagination when using taxonomy filter or created filter
		 * if it came from HTTP GET from news_filter or archive (not internal GET)
		 * can be removed when news_filter and news_archive will be improved
		 */
		 
		if ((is_numeric($_GET['taxonomy_tree_id']) || is_numeric($_GET['created'])) || $this->GET['show_all_from_bo']) {
			$limit_from = 0;
			$limit_per_page = 999999;
		}
		
		$limit = "$limit_from,$limit_per_page";
		
		/**
		 * prepare filter
		 */
		 
		$filter = array(
			'node_group' => 'page',
			'node_controller' => 'news',
			'parent' => $blog_node_id,
			'publish' => $publish,
			'created' => $created,
			'taxonomy_tree_id' => $taxonomy_tree_id
		);
		
		$news_list = $this->getNewsList($filter, $limit_from, $limit_per_page);
		
		if (is_array($news_list) && count($news_list) > 0) {
			
			/**
			 * Display pagination
			 */
			
			if ($display_pagination == 1) {
			
				$this->displayPagination($filter, $limit_from, $limit_per_page);
				
			}
			
			/**
			 * display news list
			 */
			 
			$this->parseNewsList($news_list, $display_teaser_image, $image_width, $image_height);
		}
		
		return true;
	}
	
	/**
	 * getNewsListAll
	 */
	 
	public function getNewsListAll($filter, $sorting = 'common_node.created DESC, id DESC') {
		
		$news_list = $this->Node->getNodeList($filter, $sorting);
		
		return $news_list;
		
	}
	
	/**
	 * getNewsList
	 */
	 
	public function getNewsList($filter, $limit_from, $limit_per_page) {
	
		/**
		 * get list using filter
		 */
		
		$news_list = $this->getNewsListAll($filter);
		
		/**
		 * iterate items and created news_list_filtered
		 * implemented pagination
		 */
		
		if (is_array($news_list)) {
		
			$news_list_filtered = array();
			
			foreach ($news_list as $i=>$item) {
			
				//skip active article if any (e.g. for showing related articles)
				if ($this->GET['node_id'] == $item['id']) {
					
					//don't add to the list
				
				} else {
				
					//check if it's within requested pagination limit
					if ($i >= $limit_from  && $i < ($limit_from + $limit_per_page) ) {
						
						/**
						 * unserialize other data
						 */
						 
						$item['other_data'] = unserialize($item['other_data']);
						
						/**
						 * unserialize component data
						 */
						 
						$item['component'] = unserialize($item['component']);
						
						/**
						 * add author detail
						 */
						
						$item['author_detail'] = $this->Node->getAuthorDetailbyId($item['author']);
						
						//overwrite author name
						if ($item['component']['author'] != '') $item['author_detail']['name'] = $item['component']['author'];
						
						/**
						 * odd_even_class
						 */
						 
						$odd_even = ( $odd_even == 'odd' ) ? 'even' : 'odd';
						$item['odd_even_class'] = $odd_even;
						
						/**
						 * add disabled class if not published items are in the list
						 */
						 
						if ($item['publish'] == 0) $item['class'] .= ' disabled';
						
						/**
						 * add related_taxonomy
						 */
						
						$item['related_taxonomy'] = $this->Node->getRelatedTaxonomy($item['id']);
						
						/**
						 * create taxonomy_class from related_taxonomy
						 */
						
						$item['taxonomy_class'] = '';
						
						foreach ($item['related_taxonomy'] as $t_item) {
							$item['taxonomy_class'] .= "t{$t_item['id']} ";
						}
						
						/**
						 * add modified item to final result
						 */
						
						$news_list_filtered[] = $item;
					}
				}
			}
			
			return $news_list_filtered;
			
		} else {
		
			return false;
			
		}
		
	}
	
	/**
	 * parseNewsList
	 */
	 
	public function parseNewsList($news_list, $display_teaser_image = false, $image_width = 125, $image_height = 0) {
	
		if (!is_array($news_list)) return false;
		
		if (count($news_list) > 0) {
			
			/**
			 * get comment count for all news pages
			 */
			 
			$comment_count = $this->Node->getCommentCount('page', 'news');
			
			/**
			 * display news list
			 */
			 
			foreach ($news_list as $item) {
				
				/**
				 * teaser image
				 */
				
				if ($display_teaser_image) {
					
					$Image = new Onxshop_Request("component/image&relation=node&role=main&node_id={$item['id']}&width=$image_width&height=$image_height&limit=0,1");
					$this->tpl->assign('IMAGE', $Image->getContent());
				}
	
				/**
				 * assign node (ITEM) data
				 */
				
				$this->tpl->assign('ITEM', $item);
				
				/**
				 * check comments
				 */
				
				
				if ($item['component']['allow_comment'] == 1) {
					
					$item_comment_count = $comment_count[$item['id']];
					if (!is_numeric($item_comment_count)) $item_comment_count = 0;
					$this->tpl->assign('COMMENT_COUNT', $item_comment_count);
					$this->tpl->parse('content.list.item.comment');
					
				}
				
				/**
				 * parse
				 */
				
				$this->tpl->parse('content.list.item');
			}
			
			//display news list
			$this->tpl->parse('content.list');
			
		}
		
	}
	
	/**
	 * get taxonomy
	 */
	 
	public function getTaxonomyList() {
		
		if (is_numeric($this->GET['taxonomy_tree_id'])) {
			$taxonomy_tree_id_list = $this->GET['taxonomy_tree_id'];
		} else if (is_numeric($this->GET['product_id'])) {
			$taxonomy_tree_id_list = $this->getTaxonomyListFromProduct($this->GET['product_id']);
		} else if (is_numeric($this->GET['node_id'])) {
			$taxonomy_tree_id_list = $this->getTaxonomyListFromNode($this->GET['node_id']);
		} else {
			$taxonomy_tree_id_list = '';
		}
		
		return $taxonomy_tree_id_list;
	}
	
	/**
	 * get taxonomy from node
	 */
	 
	public function getTaxonomyListFromNode($node_id) {
		
		if (!is_numeric($node_id)) return false;
		 
		$taxonomy_tree_id_list = $this->Node->getTaxonomyForNode($node_id);
		
		return $taxonomy_tree_id_list;
	}
	
	/**
	 * get taxonomy from product
	 * this is for showing related blog articles at product page
	 */
	 
	public function getTaxonomyListFromProduct($product_id) {
		
		if (!is_numeric($product_id)) return false;
		
		require_once('models/ecommerce/ecommerce_product.php');
		$Product = new ecommerce_product();
		
		$taxonomy_tree_id_list = $Product->getTaxonomyForProduct($product_id);
		
		return $taxonomy_tree_id_list;
	}
	
	/**
	 * displayPagination
	 */
	 
	public function displayPagination($filter, $limit_from, $limit_per_page) {
	
		$full_news_list = $this->getNewsListAll($filter);
				
		$count = count($full_news_list);
				
		$_Onxshop_Request = new Onxshop_Request("component/pagination~limit_from=$limit_from:limit_per_page=$limit_per_page:count=$count~");
		$this->tpl->assign('PAGINATION', $_Onxshop_Request->getContent());
				
	}
}
