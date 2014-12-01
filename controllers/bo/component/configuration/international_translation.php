<?php
/**
 * Copyright (c) 2009-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/component/configuration.php');
require_once('models/international/international_translation.php');

class Onxshop_Controller_Bo_Component_Configuration_International_Translation extends Onxshop_Controller_Bo_Component_Configuration {

	/**
	 * custom action
	 */
	
	public function mainAction() {

		$Translation = new international_translation();

		if ($this->GET['add'] == "true") {
			$last_item = $Translation->listing("", "id DESC", "0,1");
			if (is_numeric($last_item[0]['id'])) {
				unset($last_item[0]['id']);
			} else {
				$last_item[0] = array(
					"locale" => $GLOBALS['onxshop_conf']['global']['locale']
				);
			}
			$last_item[0]['original_string'] = "";
			$last_item[0]['translated_string'] = "";
			$Translation->insert($last_item[0]);
		}

		$dictionary = $Translation->listing("", "id DESC");

		foreach ($dictionary as $item) {

			$this->tpl->assign("ITEM", $item);
			$this->tpl->parse("content.item");

		}

		return true;
	
	}

}
