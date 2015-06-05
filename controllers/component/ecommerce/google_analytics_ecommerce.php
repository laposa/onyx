<?php
/** 
 * Copyright (c) 2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Ecommerce_Google_Analytics_Ecommerce extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		setlocale(LC_MONETARY, $GLOBALS['onxshop_conf']['global']['locale']);

		if (is_numeric($this->GET['order_id'])) {

			require_once('models/ecommerce/ecommerce_transaction.php');
			$this->Transaction = new ecommerce_transaction();
			$this->Transaction->setCacheable(false);

			$order_data = $this->Transaction->getOrderDetail($this->GET['order_id']);
			$this->tpl->assign("ORDER", $order_data);
			$this->tpl->assign("COMMA", ",");

			foreach ($order_data['basket']['items'] as $i => $item) {
				$this->tpl->assign("ITEM", $item);
				if ($i == count($order_data['basket']['items']) - 1) $this->tpl->assign("COMMA", "");
				$this->tpl->parse('content.google_analytics.item');
			}
			
			$this->tpl->parse('content.google_analytics');
		}

		setlocale(LC_MONETARY, LOCALE);

		return true;
		
	}
	
}
