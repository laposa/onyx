<?php
/** 
 * Copyright (c) 2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('models/common/common_node.php');

class Onxshop_Controller_Component_Ecommerce_Store_Province_Form extends Onxshop_Controller
{

	const PROVINCE_ROOT_PAGE_ID = 1345;
	const DUBLIN_PAGE_ID = 1536;


	/**
	 * main action
	 */
	 
	public function mainAction()
	{
		$active_ids = $this->getFullPath();

		$this->tpl->assign("PROVINCE_ROOT_PAGE_ID", self::PROVINCE_ROOT_PAGE_ID);

		$this->parseCountySelect($active_ids);
		$areaSelected = $this->parseAreaSelect($active_ids);
		$this->parseStoreSelect($active_ids, $areaSelected);

		return true;
	}



	protected function parseCountySelect($active_ids)
	{
		$provinces = $this->getPageChildren(self::PROVINCE_ROOT_PAGE_ID);

		foreach ($provinces as $province) {

			if ($province['publish'] == 1) {

				$this->tpl->assign("PROVINCE_NAME", $province['title']);

				$counties = $this->getPageChildren($province['id']);

				foreach ($counties as $county) {
					if ($county['publish'] == 1 && $county['content'] == '') {
						$county['selected'] = (in_array($county['id'], $active_ids) ? 'selected="selected"' : '');
						$this->tpl->assign("COUNTY", $county);
						$this->tpl->parse("content.county_dropdown.province.county");
					}
				}

				$this->tpl->parse("content.county_dropdown.province");

			}
		}

		$this->tpl->parse("content.county_dropdown");

	}



	protected function parseAreaSelect($active_ids)
	{
		$areaSelected = false;

		$districts = $this->getPageChildren(self::DUBLIN_PAGE_ID);

		foreach ($districts as $district) {
			if ($district['publish'] == 1 && $district['content'] == '') {
				if (in_array($district['id'], $active_ids)) {
					$areaSelected = true;
					$district['selected'] = 'selected="selected"';
				}
				$this->tpl->assign("DISTRICT", $district);
				$this->tpl->parse("content.dublin_dropdown.district");
			}
		}

		$this->tpl->assign("DUBLIN_PAGE_ID", self::DUBLIN_PAGE_ID);

		$this->tpl->parse("content.dublin_dropdown");

		return $areaSelected;
	}



	protected function parseStoreSelect($active_ids, $areaSelected)
	{
		if ($areaSelected) $stores = $this->getPageChildren($active_ids[count($active_ids) - 5]);
		else $stores = $this->getPageChildren($active_ids[count($active_ids) - 4]); 

		$stores = php_multisort($stores, array(array('key' => 'title', 'sort' => 'asc')));

		foreach ($stores as $store) {
			if ($store['publish'] == 1 && is_numeric($store['content'])) {
				$store['selected'] = (in_array($store['id'], $active_ids) ? 'selected="selected"' : '');
				$this->tpl->assign("STORE", $store);
				$this->tpl->parse("content.store_dropdown.store");
			}
		}

		$this->tpl->parse("content.store_dropdown");

	}



	public function getPageChildren($parent)
	{
		$Node = new common_node();
		
		return $Node->getChildren($parent);
	}



	public function getFullPath() {

		if (is_numeric($this->GET['store_node_id'])) {
			$Node = new common_node();
			return $Node->getActiveNodes($this->GET['store_node_id']);
		}

		return $_SESSION['full_path'];

	}

}
