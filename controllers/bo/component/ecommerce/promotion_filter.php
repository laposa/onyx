<?php
/**
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Promotion_Filter extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		/**
		 * Store submited data to the SESSION
		 */
		if (isset($_POST['reset'])) {
			$_SESSION['voucher-filter'] = null;
			onxshopGoTo('/backoffice/marketing');
		}
		
		if (isset($_POST['voucher-filter'])) {
			$_SESSION['voucher-filter'] = $_POST['voucher-filter'];
			onxshopGoTo('/backoffice/marketing');
		}

		$selected = array();
		if ($_SESSION['voucher-filter']['usage'] == 'used') $selected['voucher_used'] = 'selected="selected"';
		if ($_SESSION['voucher-filter']['usage'] == 'unused') $selected['voucher_unused'] = 'selected="selected"';
		if ($_SESSION['voucher-filter']['type'] == 'REF-') $selected['voucher_type_ref'] = 'selected="selected"';
		if ($_SESSION['voucher-filter']['type'] == 'REW-') $selected['voucher_type_rew'] = 'selected="selected"';
		if ($_SESSION['voucher-filter']['type'] == 'GIFT-') $selected['voucher_type_gift'] = 'selected="selected"';
		if ($_SESSION['voucher-filter']['type'] == 'other') $selected['voucher_type_other'] = 'selected="selected"';

		$this->tpl->assign("SELECTED", $selected);
		$this->tpl->parse("content.form");

		return true;

	}
}

