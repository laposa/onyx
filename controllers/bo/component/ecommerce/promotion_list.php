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
		
		$promotion_list = $this->getList();

		if (is_array($promotion_list)) $this->parseList($promotion_list);

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
	 * get list
	 */
	 
	public function getList() {
		
		$promotion_list = $this->Promotion->getList();
	
		return $promotion_list;
		
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

