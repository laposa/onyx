<?php
/**
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Promotion_Add extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/ecommerce/ecommerce_promotion.php');
		$Promotion = new ecommerce_promotion();
		
		/**
		 * Save on request
		 */
		if (is_array($_POST['promotion'])) {
			$_POST['promotion']['type'] = 1; // can add discount coupon only
			if ($Promotion->addPromotion($_POST['promotion'])) {
				//onxshopGoTo("/backoffice/marketing");
				msg ('Inserted');
			} else {
				msg('Insert failed', 'error');
			}
		}

		/**
		 * Display Detail
		 */

		if (count($_POST['promotion']) > 0) {
			$this->tpl->assign('PROMOTION', $_POST['promotion']);
		}
		
		return true;
	}
}

