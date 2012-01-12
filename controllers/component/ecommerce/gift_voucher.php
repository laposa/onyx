<?php
/**
 *
 * Copyright (c) 2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Ecommerce_Gift_Voucher extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * get product conf
		 */
		 
		require_once('models/ecommerce/ecommerce_product.php');
		$ecommerce_product_conf = ecommerce_product::initConfiguration();
		
		/**
		 * check gift voucher product ID is set
		 */
		 
		if (!is_numeric($ecommerce_product_conf['gift_voucher_product_id']) || $ecommerce_product_conf['gift_voucher_product_id']  == 0) {
			
			msg("You need to create ecommerce_product.gift_voucher_product_id conf option to use gift voucher component", 'error');
			
			return true;
		}
		
		/**
		 * get gift voucher product detail
		 */
		 
	 	$this->gift_voucher_product_detail = $this->getGiftVoucherProductDetail($ecommerce_product_conf['gift_voucher_product_id']);
		
		/**
		 * get input
		 */
		 
		if (is_array($_POST['gift_voucher_specify'])) $data = $this->getPreviewData($_POST['gift_voucher_specify']);
		else if ($this->GET['voucher_code']) $data = $this->getVoucherData($this->GET['voucher_code']);
		else $data = $this->getDefaultPreviewData();
		
		
		
		$this->customAction($data);
		
		return true;
	}
	
	/**
	 * customAction
	 */
	 
	public function customAction($data) {
		
		$this->tpl->assign('GIFT_VOUCHER', $data);
		
		return true;
		
	}
	
	/**
	 * get gift voucher product detail
	 */
	
	public function getGiftVoucherProductDetail($gift_voucher_product_id) {
		
		$Product = new ecommerce_product();
		
		$gift_voucher_product_detail = $Product->getProductDetail($gift_voucher_product_id);
		
		return $gift_voucher_product_detail;
				
	}
	
	/**
	 * check if gift voucher is in basket
	 */
	 
	public function checkGiftVoucherSelected($variety_id) {
		
		require_once('models/ecommerce/ecommerce_basket.php');
		$Basket = new ecommerce_basket();
		$Basket->setCacheable(false);
		
		$variety_id_list = $Basket->getContentItemsVarietyIdList($_SESSION['basket']['id']);
		
		if (in_array($variety_id, $variety_id_list)) return true;
		else return false;
	}
	
	/**
	 * validate data
	 */
	 
	public function validateData($data) {
	
		if (!is_array($data)) return false;
		
		if (!is_numeric($data['variety_id'])) {
			msg("Gift Voucher: variety_id is not numeric", 'error');
			return false;
		} else if ($data['variety_id'] == 0) {
			msg("Gift Voucher: amount option isn't selected", 'error');
			return false;
		}
		
		if (trim($data['recipient_name']) == '') {
			msg("Gift Voucher: recipient_name is not provided", 'error');
			return false;
		}
		
		if (trim($data['recipient_email']) == '') {
			msg("Gift Voucher: recipient_email is not provided", 'error');
			return false;
		}
		
		if (trim($data['sender_name']) == '') {
			msg("Gift Voucher: sender_name is not provided", 'error');
			return false;
		}
		
		if ($data['recipient_email_repeat']) {
			if ($data['recipient_email'] != $data['recipient_email_repeat']) {
				msg("Gift Voucher: recipient_email and  recipient_email_repeat doesn't match", 'error');
				return false;
			}
		}
		
		//email validation regex copied from onxshop.model
		$regex = '/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i';
		if (!preg_match($regex, $data['recipient_email'])) {
			msg("Gift Voucher: recipient_email is not valid email address", 'error');
			return false;
		}
		
		//if ($data['delivery_date']) {
		//	if (!preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/", $data['delivery_date'])) {
		//		msg("Gift Voucher: delivery_date is not valid format", 'error');
		//		return false;
		//	}
		//}
		
		
		return true;
	}
	
	
	/**
	 * getDefaultData
	 */
	 
	public function getDefaultPreviewData() {
		
		$data = array();
		
		$data['variety_name'] = '£…';
		$data['variety_description'] = 'VIRTUAL GIFT CARD, … POUNDS';
		$data['recipient_name'] = 'Recipient\'s Name';
		$data['message'] = 'Your Message';
		$data['sender_name'] = 'Your Name';
		$data['delivery_date'] = 'now';
		
		return $data;
	}
	
	/**
	 * getPreviewData
	 */
	 
	public function getPreviewData($data) {
		
		if (is_numeric($data['variety_id'])) {
			foreach ($this->gift_voucher_product_detail['variety'] as $variety) {
				if ($variety['id'] == $data['variety_id']) {
					$data['variety_name'] = $variety['name'];
					$data['variety_description'] = $variety['description'];
				}
			}
			
		} else {
			$data['variety_name'] = '£…';
			$data['variety_description'] = 'VIRTUAL GIFT CARD, … POUNDS';
		}
		
		return $data;
		
	}
	
	/**
	 * getVoucherData
	 */
	 
	public function getVoucherData($voucher_code) {
	
		require_once('models/ecommerce/ecommerce_promotion.php');
		$Promotion = new ecommerce_promotion();
		
		$promotion_list = $Promotion->listing("code_pattern = '{$voucher_code}'");
		
		$promotion_data = $Promotion->getDetail($promotion_list[0]['id']);
		
		$data = $promotion_data['other_data'];
		$data['variety_name'] = "£" . round($promotion_data['discount_fixed_value']);
		$data['variety_description'] = "VOUCHER CODE: {$voucher_code}";
		
		return $data;
	}
}
