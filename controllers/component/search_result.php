<?php
/** 
 * Zend Search Lucene
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Search_Result extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		if (isset($this->GET['search_query'])) {
		
			require_once('Zend/Search/Lucene.php');
			require_once('models/common/common_node.php');
			$Node = new common_node();
			
			$count = strlen(trim($this->GET['search_query']));

			if ($count > 2) {
			
				//sanitize
				$search_query = htmlentities(strip_tags( $this->GET['search_query'] ));
				
				$index_location = ONXSHOP_PROJECT_DIR . 'var/index';
				
				try {
					$index = Zend_Search_Lucene::open($index_location);
				} catch(Exception $e) {
					msg("Cannot open Lucene index", 'error');
					return false;
				}	
		
				$search_query = Zend_Search_Lucene_Search_QueryParser::parse($search_query, 'UTF-8');
				$hits = $index->find($search_query);
				
				$result_items_shown = 0;
				$result_items_show = 15;
				
				if (count($hits) > 0) {
				
					foreach ($hits as $hit) {
						
						if ($result_items_shown >= $result_items_show) break;

						$doc = $hit->getDocument();
						$uri = $hit->uri == '' ? '/' : $hit->uri; 

						$node_id = $Node->getNodeIdFromSeoUri($uri);
						if (!$node_id) continue;

						$path = $Node->getFullPathDetailForBreadcrumb($node_id);
						$page = end($path);

						try {
							$page['body'] = $doc->getFieldValue("body");

							$this->parseBreadcrumb($path);
							$this->parsePageDetails($page);
							$result_items_shown++;

						} catch (Exception $e) {
							continue;
						}

					}
				
				}			

				if ($result_items_shown > 0) $this->tpl->parse('content.result');
				else $this->tpl->parse('content.empty_result');

			} else {
			
				msg("Please specify at least 3 characters", "error");
			
			}
		}

		return true;
	}


	/**
	 * load addional page info (excerpt and image)
	 */
	protected function parsePageDetails(&$page)
	{
		switch ($page['node_controller']) {

			case 'recipe':
				$result = $this->getRecipeDetails($page['content']);
				break;

			case 'product':
				$result = $this->getProductDetails($page['content']);
				break;

			case 'store':
				$result = $this->getStoreDetails($page['content']);
				break;

		}

		if ($result) {

				$page['excerpt'] = $result;

		} else {

			if (strlen($page['description']) < 20) {

				$page['excerpt'] = $this->reduceDescription($page['body']);
				$page['excerpt'] = $this->highlightKeywords($page['excerpt']);

			} else {

				$page['excerpt'] = $page['description'];
			}

		}

		$this->tpl->assign('PAGE', $page);
		$this->tpl->parse('content.result.item');

	}

	/**
	 * load recipe details
	 */
	protected function getRecipeDetails($recipe_id)
	{
		if (!is_numeric($recipe_id)) return false;

		require_once("models/ecommerce/ecommerce_recipe.php");
		$Recipe = new ecommerce_recipe();
		$recipe = $Recipe->detail($recipe_id);

		$excerpt = $this->highlightKeywords(strip_tags($recipe['description']));

		$request = new Onxshop_Request("component/image~relation=recipe:role=main:width=100:node_id={$recipe['id']}:limit=0,1~");
		$this->tpl->assign('IMAGE', $request->getContent());
		$this->tpl->parse('content.result.item.image');

		return $excerpt;
	}

	/**
	 * load store details
	 */
	protected function getStoreDetails($store_id)
	{
		if (!is_numeric($store_id)) return false;

		require_once("models/ecommerce/ecommerce_store.php");
		$Store = new ecommerce_store();
		$store = $Store->detail($store_id);

		$excerpt = strip_tags($store['description']);
		if (strlen($page['description']) > 0) $excerpt .= "<br/>";
		if (strlen($store['address']) > 0) $excerpt .= nl2br($store['address']);
		if (strlen($store['opening_hours']) > 0) $excerpt .= "<br/><br/>" . nl2br($store['opening_hours']);
		$excerpt = $this->highlightKeywords($excerpt);

		$request = new Onxshop_Request("component/image~relation=store:role=main:width=100:node_id={$store['id']}:limit=0,1~");
		$this->tpl->assign('IMAGE', $request->getContent());
		$this->tpl->parse('content.result.item.image');

		return $excerpt;
	}

	/**
	 * load product details
	 */
	protected function getProductDetails($product_id)
	{
		if (!is_numeric($product_id)) return false;

		require_once("models/ecommerce/ecommerce_product.php");
		$Product = new ecommerce_product();
		$product = $Product->detail($product_id);

		if (strlen($page['description']) > 10) $excerpt = $product['description'];
		else $excerpt = $product['address'];
		$excerpt = $this->highlightKeywords(strip_tags($excerpt));

		$request = new Onxshop_Request("component/image~relation=product:role=main:width=100:node_id={$product['id']}:limit=0,1~");
		$this->tpl->assign('IMAGE', $request->getContent());
		$this->tpl->parse('content.result.item.image');

		return $excerpt;
	}


	/**
	 * display breadcrumb
	 */
	protected function parseBreadcrumb(&$path)
	{
		foreach ($path as $i => $p) {
			if ($p['node_group'] == 'page' || $this->GET['type'] == 'taxonomy') {
				if ($p['page_title'] == '') $p['page_title'] = $p['title'];
				$this->tpl->assign('PATH', $p);
				if ($i == count($path) - 1) $this->tpl->parse('content.result.item.linklast');
				else $this->tpl->parse('content.result.item.link');
			}
		}
	}

	/**
	 * get array of query keywords
	 */
	protected function getKeywords()
	{
		$query = trim($this->GET['search_query']);
		$keywords = explode(" ", $query);
		foreach ($keywords as &$keyword) $keyword = trim($keyword);
		return $keywords;
	}


	/**
	 * highlight keywords in given text using html strong tag
	 */
	protected function highlightKeywords($text)
	{
		$keywords = $this->getKeywords();

		foreach ($keywords as $keyword) {
			if (strlen($keyword) > 2) {
				$keyword = preg_quote($keyword);
				$pattern = "/($keyword)/i";
				$text = preg_replace($pattern, "<strong style=\"color: #000000; background: #ff0; \">$1</strong>", $text);
			}
		}
		
		return $text;
	}

	/**
	 * reduce given text to 200 characters
	 * try to reduce it to the part where the first keyword is present
	 */
	protected function reduceDescription($text)
	{
		$keywords = $this->getKeywords();
		$text = preg_replace("/\s\s([\s]+)?/", " ", $text);

		foreach ($keywords as $keyword) {
			if (strlen($keyword) > 2) {
				$pos = stripos($text, $keyword);
				if ($pos !== FALSE) {
					$start = max(0, $pos - 100);
					$length = 200;
					while ($start > 0 && $text[$start] != ' ') $start--; // whole words
					while ($length < 250 && $text[$start + $length] != ' ') $length++; // whole words
					$line = substr($text, $start, $length) . "&hellip;";
					if (strlen($line) > 100) return $line;
				}
			}
		}

		return substr($text, 0, 200) . "&hellip;";
	}
}
