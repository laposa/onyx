<?php
/**
 * Backoffice product list controller
 *
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Product_List extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * Get input variables
		 */
		 
		if ($_POST['product-list-filter']) $filter = $_POST['product-list-filter'];
		else $filter = $_SESSION['product-list-filter'];

		if (is_numeric($this->GET['taxonomy_tree_id'])) $filter['taxonomy_json'] = json_encode(array($this->GET['taxonomy_tree_id']));
		else $filter['taxonomy_json'] = false;
		
		/**
		 * Get the list
		 */
		 
		require_once('models/ecommerce/ecommerce_product.php');
		
		$Product = new ecommerce_product();	
		
		$product_list = $Product->getFilteredProductList($filter);
		
		if (!is_array($product_list)) return false;
		if (count($product_list) == 0) {
			msg("No products found.");
			return true;
		}
		
			/**
			 * Sorting
			 */
			
			//$_nSite = new nSite("component/ecommerce/product_list_sorting");
			//$this->tpl->assign('SORTING', $_nSite->getContent());
			
			if ($this->GET['product-list-sort-by']) {
				$_SESSION['product-list-sort-by'] = $this->GET['product-list-sort-by'];
			}
			
			if ($this->GET['product-list-sort-direction']) {
				$_SESSION['product-list-sort-direction'] = $this->GET['product-list-sort-direction'];
			}
			
			if ($_SESSION['product-list-sort-by']) {
				$sortby = $_SESSION['product-list-sort-by'];
			} else {
				$sortby = "modified";
			}
			
			if ($_SESSION['product-list-sort-direction']) {
				$direction = $_SESSION['producs-list-sort-direction'];
			} else {
				$direction = "DESC";
			}
			
			//msg("Sorted by $sortby $direction");
			$product_list_sorted = array();
			switch ($sortby) {
				default:
				case 'id':
					$product_list = php_multisort($product_list, array(array('key'=>'product_id', 'sort'=>$direction), array('key'=>'product_id', 'type'=>'numeric')));
			
					foreach ($product_list as $item) {
						$product_list_sorted[] = $item;
					}

					break;
				case 'modified':
					$product_list = php_multisort($product_list, array(array('key'=>'modified', 'sort'=>$direction), array('key'=>'product_id', 'type'=>'numeric')));
			
					foreach ($product_list as $item) {
						$product_list_sorted[] = $item;
					}
					
					break;
			}
			
			$product_list = $product_list_sorted;
		
			
			//print_r($product_list);exit;
			
			/**
			 * Reformat
			 */
			 
			$pl = array();
			foreach ($product_list as $item) {
				$pl[$item['product_id']][] = $item;
			}
			$product_list = array();
			foreach ($pl as $item) {
				$product_list[] = $item;
			}
		
			/**
			 * Initialize pagination variables
			 */
			
			if  (is_numeric($this->GET['limit_from'])) $from = $this->GET['limit_from'];
			else $from = 0;
			if (is_numeric($this->GET['limit_per_page'])) $per_page = $this->GET['limit_per_page'];
			else $per_page = 25;
			
			
			$limit = "$from,$per_page";
			
			
			/**
			 * Display pagination
			 */
			
			//$link = "/page/" . $_SESSION['active_pages'][0];
			$count = count($product_list);
			
			$_nSite = new nSite("component/pagination~limit_from=$from:limit_per_page=$per_page:count=$count~");
			$this->tpl->assign('PAGINATION', $_nSite->getContent());
			
			
		
		/**
		 * Parse items
		 * Implemented pagination
		 */
		
		//print_r($product_list); exit;
		 
		
		foreach ($product_list as $i=>$p_item) {
			
			if ($i >= $from  && $i < ($from + $per_page) ) {
				
				$item = $p_item[0];
				
				$rowspan = count($p_item);
				
				$this->tpl->assign('ROWSPAN', "rowspan='$rowspan'");
				
				$item['disabled'] = ($item['publish']) ? '' : 'disabled';
						
				$this->tpl->assign('ITEM', $item);
				
				if ($item['image_src']) $this->tpl->parse('content.list.item.imagetitle.image');
				
				$this->tpl->parse('content.list.item.imagetitle');
				
				$even_odd = ( 'odd' != $even_odd ) ? 'odd' : 'even';
				$this->tpl->assign('CLASS', "class='$even_odd fullproduct'");
                
				foreach ($p_item as $item) {
					if ($item['variety_publish'] == 0) $item['variety_publish'] = 'disabled';
					$this->tpl->assign('ITEM', $item);
					$this->tpl->parse('content.list.item');
					$this->tpl->assign('CLASS', "class='$even_odd'");
				}
				
			}
		}
		
		$this->tpl->parse('content.list');

		return true;
	}
}
