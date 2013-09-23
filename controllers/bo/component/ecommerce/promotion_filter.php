<?php
/**
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('models/ecommerce/ecommerce_promotion_type.php');

class Onxshop_Controller_Bo_Component_Ecommerce_Promotion_Filter extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		/**
		 * Store submited data to the SESSION
		 */
		if (isset($_POST['voucher-filter'])) {
			$_SESSION['voucher-filter'] = $_POST['voucher-filter'];
			onxshopGoTo('/backoffice/marketing');
		}

		$this->parseTypeSelect($_SESSION['voucher-filter']['type']);
		$this->tpl->parse("content.form");

		return true;

	}

	protected function parseTypeSelect($selected_id)
	{
		$Type = new ecommerce_promotion_type();
		$records = $Type->listing();

		foreach ($records as $item) {
			if ($item['id'] == $selected_id) $item['selected'] = 'selected="selected"';
			$this->tpl->assign("ITEM", $item);
			$this->tpl->parse("content.form.type.item");
		}
		$this->tpl->parse("content.form.type");
	}

}

