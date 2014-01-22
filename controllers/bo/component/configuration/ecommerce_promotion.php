<?php
/**
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/component/configuration.php');
require_once('models/ecommerce/ecommerce_promotion.php');

class Onxshop_Controller_Bo_Component_Configuration_Ecommerce_Promotion extends Onxshop_Controller_Bo_Component_Configuration {
	
	/**
	 * custom action
	 */
	
	public function mainAction() {

		$this->standardConfAction();

		return true;
	
	}	

	/**
	 * display
	 */
	
	function displayConf($conf) {
	
		$this->tpl->assign("CONF", $conf['ecommerce_promotion']);
		return true;
	}


	/**
	 * save
	 */
	
	function saveConfiguration($conf) {
		
		if (is_array($conf)) {
		
			msg("Saving config");
			
			foreach ($conf['item'] as $property=>$value) {
				
				if ($this->Configuration->saveConfig($conf['object'], $property, $value, $conf['node_id'])) {
					if ($property == 'minimum_order_amount') $this->updateVouchers('minimum_order_amount', $value);
					if ($property == 'discount_value') $this->updateVouchers('discount_value', $value);
					msg("Saved $property $value");
				}
			}
		}
	}

	function updateVouchers($property, $value) {

		switch ($property) {

			case 'minimum_order_amount': 
				$column = 'limit_to_order_amount';
				break;

			case 'discount_value':
				$column = 'discount_fixed_value';
				break;

			default:
				return;
		}

		$Promotion = new ecommerce_promotion();
		$value = pg_escape_string($value);
		$sql = "UPDATE ecommerce_promotion SET $column = '$value' WHERE type = 2";
		$Promotion->executeSql($sql);

	}

}
