<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

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
			}
		}
		
		// store detail
		$store = $Store->detail($this->GET['id']);
		$store['publish'] = ($store['publish'] == 1) ? 'checked="checked" ' : '';
		$store['street_view_options'] = unserialize($store['street_view_options']);
		$this->tpl->assign('STORE', $store);
		$this->tpl->assign('STREET_VIEW_IMAGE_' . ((int) $store['street_view_options']['image']), 'checked="checked"');

		return true;
	}
}	
			
