<?php
/**
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/ecommerce/ecommerce_store.php');
require_once('models/common/common_node.php');

class Onxshop_Controller_Component_Ecommerce_Store_Map extends Onxshop_Controller {

	/**
	 * main action
	 */
	public function mainAction()
	{
	
		$taxonomy_id = $this->GET['taxonomy_id'];
		$store_to_select = $this->GET['store_id'];
		
		if (is_numeric($this->GET['zoom'])) $this->tpl->assign('ZOOM', $this->GET['zoom']);
		else $this->tpl->assign('ZOOM', 13);
		
		$Mapping = new common_uri_mapping();
		$this->Store = new ecommerce_store();
		
		$store_pages = $this->getStorePages();
		$stores = $this->getAllStores($taxonomy_id);
		
		// display pins
		foreach ($stores as $store) {

			if ($store['latitude'] != 0 && $store['longitude'] != 0) {

				// pre-select this item?
				if ($store['id'] == $store_to_select) {
					$selected_store = $store;
				}

				// find page and url
				$page = $store_pages[$store['id']];
				$store['url'] = $Mapping->stringToSeoUrl("/page/{$page['id']}");
				$store['node_id'] = $page['id'];
				$store['icon'] = $store['id'] == $selected_store['id'] ? 'false' : 'true';
				$store['open'] = $store['id'] == $selected_store['id'] ? 'true' : 'false';

				// parse item
				$this->tpl->assign("STORE", $store);
				$this->tpl->parse("content.map.store_marker");

			}
		}

		// center map to a selected store
		if ($selected_store) {

			$map['latitude'] = $selected_store['latitude'];
			$map['longitude'] = $selected_store['longitude'];

		} else {

			$map['latitude'] = $this->Store->conf['latitude'];
			$map['longitude'] = $this->Store->conf['longitude'];

			$this->fitMapToBounds($stores);
		}
		
		$this->tpl->assign("MAP", $map);
		$this->tpl->parse("content.map");

		return true;
	}

	public function fitMapToBounds(&$stores)
	{
		$bounds = array();
		$bounds['latitude']['max'] = -9999;
		$bounds['latitude']['min'] = 9999;
		$bounds['longitude']['max'] = -9999;
		$bounds['longitude']['min'] = 9999;

		foreach ($stores as $store) {

			if ($store['latitude'] != 0 && $store['longitude'] != 0) {

				if ($store['latitude'] > $bounds['latitude']['max']) $bounds['latitude']['max'] = $store['latitude'];
				if ($store['latitude'] < $bounds['latitude']['min']) $bounds['latitude']['min'] = $store['latitude'];
				if ($store['longitude'] > $bounds['longitude']['max']) $bounds['longitude']['max'] = $store['longitude'];
				if ($store['longitude'] < $bounds['longitude']['min']) $bounds['longitude']['min'] = $store['longitude'];

			}

		}

		if ($bounds['latitude']['min'] != 9999) {
			$this->tpl->assign("BOUNDS", $bounds);
			$this->tpl->parse("content.map.fit_to_bounds");
		}

	}

	/**
	 * Returns array of all store pages. Store id is used as array index.
	 * 
	 * @return Array
	 */
	protected function getStorePages()
	{

		$Node = new common_node();

		$pages_raw = $Node->listing("node_group = 'page' AND node_controller = 'store' AND content ~ '[0-9]+'");

		$pages = array();

		foreach ($pages_raw as $page) {
			$store_id = (int) $page['content'];
			$pages[$store_id] = $page;
		}

		return $pages;
	}


	/**
	 * Returns array of all published stores in the database
	 * 
	 * @return Array
	 */
	protected function getAllStores($taxonomy_id = false)
	{	
		if (is_numeric($taxonomy_id)) {
		
			$store_list = $this->Store->getFilteredStoreList($taxonomy_id, false, 0, false, false, 9999);
			$store_list = $store_list[0];
			
		} else {
		
			$store_list = $this->Store->listing("publish = 1");
		
		}
		
		return $store_list;
	}

}

