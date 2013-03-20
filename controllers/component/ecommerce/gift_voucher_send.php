<?php
/**
 * Copyright (c) 2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/gift_voucher.php');

class Onxshop_Controller_Component_Ecommerce_Gift_Voucher_Send extends Onxshop_Controller_Component_Ecommerce_Gift_Voucher
{

	/**
	 * main action
	 */
	 
	public function mainAction()
	{
		if (!is_numeric($this->GET['promotion_id'])) {
			msg("Onxshop_Controller_Component_Ecommerce_Gift_Voucher_Generate: promotion_id isn't numeric");
			return false;
		}

		$promotion_id = $this->GET['promotion_id'];

		if ($gift_voucher_product_id = $this->getGiftVoucherProductId($order_id)) {

			require_once('models/ecommerce/ecommerce_promotion.php');
			$Promotion = new ecommerce_promotion();

			$promotion_data = $Promotion->getDetail($promotion_id);

			if ($promotion_data) {

				$gift_voucher_directory = ONXSHOP_PROJECT_DIR . "var/vouchers/";
				$gift_voucher_filename = "{$promotion_data['code_pattern']}.png";
				$gift_voucher_filename_fullpath = $gift_voucher_directory . $gift_voucher_filename;

				$voucher_data = array();
				$voucher_data['recipient_name'] = $promotion_data['other_data']['recipient_name'];
				$voucher_data['recipient_email'] = $promotion_data['other_data']['recipient_email'];
				$voucher_data['message'] = $promotion_data['other_data']['message'];
				$voucher_data['sender_name'] = $promotion_data['other_data']['sender_name'];
				if ($promotion_data['other_data']['delivery_date']) $voucher_data['delivery_date'] = $promotion_data['other_data']['delivery_date'];

				$this->sendEmail($promotion_data, $voucher_data, $gift_voucher_filename);

				msg("Email sent");
			}
		}		

		return true;
	}

}
