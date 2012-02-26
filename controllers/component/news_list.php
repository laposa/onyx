<?php
/**
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
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
	
		require_once('models/common/common_node.php');
		
		$this->Node = new common_node();
		
		//FIXME fixed news_list_id
		//$blog_node_id = $this->GET['blog_node_id'];
		$blog_node_id = CMS_BLOG_ID;
		$display_teaser_image = $this->GET['display_teaser_image'];

		/**
		 * check
		 */
		 
		if (!is_numeric($blog_node_id)) {
			msg("component/news_list: blog_node_id must be numeric", 'error');
			return false;
		}
		
		/**
		 * get detail
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
		else $created = '';
		
		if (is_numeric($this->GET['publish'])) $publish = $this->GET['publish'];
		else $publish = '';
		
		if ($this->GET['display_pagination'] == 1) $display_pagination = 1;
		else $display_pagination = 0;
		
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
		 
		if (is_numeric($_GET['taxonomy_tree_id']) || is_numeric($_GET['created'])) {
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
		
		/**
		 * get list using filter
		 */
		
		$news_list = $this->Node->getNodeList($filter, 'common_node.created DESC, id DESC');
		
		/**
		 * get comment count for all news pages
		 */
		 
		$comment_count = $this->Node->getCommentCount('page', 'news');
		
		/**
		 * Display pagination
		 */
		
		if ($display_pagination == 1) {
		
			$count = count($news_list);
		
			$_nSite = new nSite("component/pagination~limit_from=$limit_from:limit_per_page=$limit_per_page:count=$count~");
			$this->tpl->assign('PAGINATION', $_nSite->getContent());
		}
		
		/**
		 * Parse items
		 * Implemented pagination
		 */
		
		if (is_array($news_list)) {
		
			$count_news_list = count($news_list);
			
			foreach ($news_list as $i=>$item) {
			
				//skip active article if any
				//TODO: when this happens, limit per page is actually descreesed by 1
				if ($this->GET['node_id'] == $item['id']) {
					
					$count_news_list = $count_news_list - 1;
				
				} else {
				
					if ($i >= $limit_from  && $i < ($limit_from + $limit_per_page) ) {
						
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
						 * teaser image
						 */
						 
						if ($display_teaser_image) {
							
							$Image = new nSite("component/image&relation=node&role=main&node_id={$item['id']}&width=130&limit=0,1");
							$this->tpl->assign('IMAGE', $Image->getContent());
						}
						
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
				}
			}
			
			if ($count_news_list > 0) $this->tpl->parse('content.list');
		}

		return true;
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
	 */
	 
	public function getTaxonomyListFromProduct($product_id) {
		
		if (!is_numeric($product_id)) return false;
		
		require_once('models/ecommerce/ecommerce_product.php');
		$Product = new ecommerce_product();
		
		$taxonomy_tree_id_list = $Product->getTaxonomyForProduct($product_id);
		
		return $taxonomy_tree_id_list;
	}
}
