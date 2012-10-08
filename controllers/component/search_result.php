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
						
						if ($result_items_shown < $result_items_show) {
							
							$r['score'] = $hit->score;
							$r['title'] = $hit->title;
							$r['uri'] = $hit->uri;
							$r['id'] = $hit->id; //not node_id
							$r['description'] = $hit->description;
							//hack for the homepage
							if ($r['uri'] == '') $r['uri'] = '/';
							$r['node_id'] = $Node->getNodeIdFromSeoUri($r['uri']);
							
							//breadcrumb
							$path = $Node->getFullPathDetailForBreadcrumb($r['node_id']);
							
							foreach ($path as $p) {
							
								if ($p['node_group'] == 'page' || $this->GET['type'] == 'taxonomy') {
									
									if ($p['page_title'] == '') $p['page_title'] = $p['title'];
									
									$this->tpl->assign('PATH', $p);
									
									if ($p['id'] == $r['node_id']) $this->tpl->parse('content.result.item.linklast');
									else $this->tpl->parse('content.result.item.link');
									
									//image
									if ($p['node_group'] == 'page' && $p['node_controller'] == 'product') {
										
										$_nSite = new nSite("component/image~relation=product:role=main:width=100:height=100:node_id={$p['content']}:limit=0,1~");
										$this->tpl->assign('IMAGE', $_nSite->getContent());
										
										$this->tpl->parse('content.result.item.image');
									}
								}
								
							}
			
							
							$this->tpl->assign('RESULT', $r);
							$this->tpl->parse('content.result.item');
							
							$result_items_shown++;
						}
					}
				
					$this->tpl->parse('content.result');
				} else {
					$this->tpl->parse('content.empty_result');
				}
			} else {
				msg("Please specify at least 3 characters", "error");
			}
		}

		return true;
	}
	
	
	/**
	 * Highlight the results
	 *
	 * @param string $content
	 * @return string
	 * @access public
	 */
	 
	public function highlight($content) {
		return $this->query->highlightMatches($content);
	}
}
