<?php
/**
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Promotion_List extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		$this->initialisePromotion();

		if  (is_numeric($this->GET['limit_from'])) $from = $this->GET['limit_from'];
		else $from = 0;
		if (is_numeric($this->GET['limit_per_page'])) $per_page = $this->GET['limit_per_page'];
		else $per_page = 25;
		
		$promotion_list = $this->Promotion->getList($from, $per_page);

		if (is_array($promotion_list)) $this->parseList($promotion_list);

		/**
		 * Display pagination
		 */
		
		$count = $this->Promotion->count();

		if ($count > 0) {		

			$_nSite = new nSite("component/pagination~limit_from=$from:limit_per_page=$per_page:count=$count~");
			$this->tpl->assign('PAGINATION', $_nSite->getContent());
		}

		return true;
	}
	
	/**
	 * initialize
	 */
	 
	public function initialisePromotion() {
		
		require_once('models/ecommerce/ecommerce_promotion.php');
	
		$this->Promotion = new ecommerce_promotion();
		
	}
	
	/**
	 * parse list
	 */
	
	public function parseList($list) {
	
		if (count($list) > 0) {
		
			foreach ($list as $item) {
				
				if ($item['publish'] == 0) $this->tpl->assign('DISABLED', 'disabled');
				else $this->tpl->assign('DISABLED', '');
				$this->tpl->assign('ITEM', $item);
				$this->tpl->parse('content.item');
			}
		} else {
			$this->tpl->parse('content.empty');
		}
		
	}
}

