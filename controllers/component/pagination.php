<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Pagination extends Onxshop_Controller {
	
	const PAGINATION_SHOW_ITEMS = 10;

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

		if ($count > $per_page) {		

			/**
			 * Optional input variables
			 */ 
			if ($this->GET['link'])  $link = $this->GET['link'];
			else $link = $_SERVER['REDIRECT_URL'];
			
			// display options
			if ($this->GET['option_show_all']) $option_show_all = $this->GET['option_show_all'];
			else $option_show_all = 1;
			if ($this->GET['option_per_page']) $option_per_page = $this->GET['option_per_page'];
			else $option_per_page = 0;

			// page items		
			$pages_total = ceil($count / $per_page);
			$current_page = floor($from / $per_page) + 1;

			// setup cycle range
			$first = max(1, $current_page - round(self::PAGINATION_SHOW_ITEMS / 2));
			$last = $first + (self::PAGINATION_SHOW_ITEMS - 1);
			if ($last > $pages_total) {
				$last = $pages_total;
				$first = max(1, $last - (self::PAGINATION_SHOW_ITEMS - 1));
			}

			for ($i = $first; $i <= $last; $i++) {

				$limit_from = ($i - 1) * $per_page;

				if ($limit_from == 0) $limit = '';
				else $limit = "?limit_from=$limit_from&amp;limit_per_page=$per_page";

				$item = array(
					'class' => $i == $current_page ? 'active' : '',
					'page' => $i,
					'link' => $link
				);

				$this->tpl->assign('LIMIT', $limit);
				$this->tpl->assign('ITEM', $item);
				$this->tpl->parse('content.pagination.item');
			}

			// previous
			if ($from > 0) {
				$previous = max($from - $per_page, 0);
				if ($previous == 0) $limit = '';
				else $limit = "?limit_from={$previous}&amp;limit_per_page=$per_page";
				$this->tpl->assign('LIMIT', $limit);
				$this->tpl->parse('content.pagination.previous');
			}
			
			// next
			$next = $from + $per_page;
			if ($next < $count)  {
				$limit = "?limit_from={$next}&amp;limit_per_page=$per_page";
				$this->tpl->assign('LIMIT', $limit);
				$this->tpl->parse('content.pagination.next');
			}
			
			// display options?
			if ($option_show_all) $this->tpl->parse('content.pagination.show_all');
			if ($option_per_page) {
				$this->tpl->assign("PAGINATION_selected_$per_page", 'selected="selected"');
				$this->tpl->parse('content.pagination.per_page');
			}
			
			// parse template
			 $this->tpl->parse('content.pagination');

		}

		return true;
	}
}
