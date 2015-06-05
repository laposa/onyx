<?php
/** 
 * Copyright (c) 2013-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/ecommerce/ecommerce_store_type.php');

class Onxshop_Controller_Bo_Component_Ecommerce_Store_Edit extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		// initialize
		require_once('models/ecommerce/ecommerce_store.php');
		$Store = new ecommerce_store();
		
		// save		 
		if ($_POST['save']) {

			// set values
			if (!isset($_POST['store']['publish'])) $_POST['store']['publish'] = 0;
			$_POST['store']['modified'] = date('c');
			
			// handle other_data
			$_POST['store']['other_data'] = serialize($_POST['store']['other_data']);
			// force numeric types
			$_POST['store']['coordinates_x'] = (int) $_POST['store']['coordinates_x'];
			$_POST['store']['coordinates_y'] = (int) $_POST['store']['coordinates_y'];
			$_POST['store']['latitude'] = (float) $_POST['store']['latitude'];
			$_POST['store']['longitude'] = (float) $_POST['store']['longitude'];
			// serialize street_view_options
			$_POST['store']['street_view_options'] = serialize($_POST['store']['street_view_options']);
			
			// remove if country_id isn't numeric
			if (!is_numeric($_POST['store']['country_id'])) unset($_POST['store']['country_id']);
			
			// update store
			if($id = $Store->update($_POST['store'])) {
			
				msg("Store ID=$id updated");
			
				// update node info (if exists)
				$store_homepage = $Store->getStoreHomepage($_POST['store']['id']);
			
				if (is_array($store_homepage) && count($store_homepage) > 0) {
					$store_homepage['publish'] = $_POST['store']['publish'];
					
					require_once('models/common/common_node.php');
					$Node = new common_node();
					
					$Node->nodeUpdate($store_homepage);
					
				}
				
				// forward to store list main page and exit
				onxshopGoTo("/backoffice/stores");
				return true;
				
			} else {
				
				msg("Cannot update store details", 'error');
			
			}
		}
		
		// store detail
		$store = $Store->detail($this->GET['id']);
		$store['publish'] = ($store['publish'] == 1) ? 'checked="checked" ' : '';
		$store['street_view_options'] = unserialize($store['street_view_options']);
		$this->tpl->assign('STORE', $store);
		$this->tpl->assign('STREET_VIEW_IMAGE_' . ((int) $store['street_view_options']['image']), 'checked="checked"');

		$this->parseTypeSelect($store['type_id']);

		return true;
	}

	protected function parseTypeSelect($selected_id)
	{
		$Type = new ecommerce_store_type();
		$records = $Type->listing();

		foreach ($records as $item) {
			if ($item['id'] == $selected_id) $item['selected'] = 'selected="selected"';
			$this->tpl->assign("ITEM", $item);
			$this->tpl->parse("content.type.item");
		}
		$this->tpl->parse("content.type");
	}

}	
			
