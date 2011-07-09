<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Pagination extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		// input: count of all records, records per page, from which record item we display
		// count [int], from[int], per_page[int], link[string]
		
		/**
		 * Compulsory input variables
		 */
		
		$count = $this->GET['count'];
		$from = $this->GET['limit_from'];
		$per_page = $this->GET['limit_per_page'];
		
		/**
		 * Optional input variables
		 */ 
		if ($this->GET['link'])  $link = $this->GET['link'];
		else $link = $_SERVER['REDIRECT_URL'];
		
		//display options
		if ($this->GET['option_show_all']) $option_show_all = $this->GET['option_show_all'];
		else $option_show_all = 1;
		if ($this->GET['option_per_page']) $option_per_page = $this->GET['option_per_page'];
		else $option_per_page = 0;
		
		$link = translateURL(ltrim($link, '/'));
		
		/*
		$count = 10;
		$from = 2;
		$per_page = 2;
		$link = $_SERVER['REQUEST_URI'];
		*/
		
		$pagination = array();
		
		
		// generate pagination items
		$rest = $count%$per_page;
		if ($per_page  > 0) $pages = ($count - $rest) / $per_page;
		if ($rest > 0) $pages++;
		
		//echo "pages $pages";
		//echo "Count: $count";
		//echo "Rest: $rest";
		//echo "Perpage: $per_page";
		
		for ( $i = 1; $i <= $pages; $i++ ) {
			if ($i == 1) $l['from'] = 0;
			else $l['from'] = ($i - 1) * $per_page;
			$l['per_page'] = $per_page;
			$l['page'] = $i;
			$classes = array();
		
			if ($i == 1) $classes[] = 'first';
			if ($i == $pages) $classes[] = 'last';
			$l['class'] = implode(' ', $classes);
			//$l['link'] = $link;
			$pagination[] = $l;
		}
		
		//print_r($pagination);
		
		// pagination display
		$i=0;
		$display_limit_links = 25;
		
		foreach ($pagination as $item) {
			if ($i < $display_limit_links) {
				if ($item['from'] == $from && $item['per_page'] == $per_page) {
					$previous = $item['from'] - $per_page;
					$next = $item['from'] + $per_page;
					$item['class'] = 'active';
				} else {
					$item['class'] = '';
				}
			
				$item['link'] = $link;
				if ($item['from'] == 0) $limit = '';
				else $limit = "?limit_from={$item['from']}&amp;limit_per_page={$item['per_page']}";
				$this->tpl->assign('LIMIT', $limit);
			
				$this->tpl->assign('ITEM', $item);
		
				if ($item['per_page'] > 0) $this->tpl->parse('content.pagination.item');
			}
			
			$i++;
		}
		
		//previous
		if ($from > 0) {
			if ($previous == 0) $limit = '';
			else $limit = "?limit_from={$previous}&amp;limit_per_page={$item['per_page']}";
			$this->tpl->assign('LIMIT', $limit);
			$this->tpl->parse('content.pagination.previous');
		}
		//next
		
		if ($next < $count)  {
			$limit = "?limit_from={$next}&amp;limit_per_page={$item['per_page']}";
			$this->tpl->assign('LIMIT', $limit);
			$this->tpl->parse('content.pagination.next');
		}
		
		//display options?
		if ($option_show_all) $this->tpl->parse('content.pagination.show_all');
		if ($option_per_page) $this->tpl->parse('content.pagination.per_page');
		
		if ($count > $per_page) $this->tpl->parse('content.pagination');

		return true;
	}
}
