<?php
/**
 * Copyright (c) 2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Ecommerce_Store_List extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		// initialize filter variables
		$taxonomy_id = $this->GET['taxonomy_tree_id'];
		
		$keyword = $this->GET['keyword'];
		$type_id = 1;

		if (is_numeric($this->GET['store_id']))	$active_store_id = $this->GET['store_id'];
		
		// get the list
		require_once('models/ecommerce/ecommerce_store.php');
		$Store = new ecommerce_store();	
		$store_list = $Store->getFilteredStoreList($taxonomy_id, $keyword, $type_id, false, false, false, false, true);
		//$count = $Store->getFilteredStoreCount($taxonomy_id, $keyword, $type_id, true);
		
		if (!is_array($store_list)) return false;

		if (count($store_list) == 0) {
			$this->tpl->parse('content.empty_list');
			return true;
		}
        
		// active store details to calculate distance
		if ($active_store_id) {
			$active_store_detail = $Store->detail($active_store_id);
		}

		// distance
		foreach ($store_list as $k=>$item) {
			
			// distance_from_selected_store
			$distance_from_selected_store = $Store->distance($active_store_detail['latitude'], $active_store_detail['longitude'], $item['latitude'], $item['longitude']);
			
			if ($distance_from_selected_store > 1) $distance_from_selected_store = round($distance_from_selected_store);
			else $distance_from_selected_store = round($distance_from_selected_store, 1);
			
			// distance_from_client_geoposition
			if ($client_geoposition) {
				$distance_from_client_geoposition = $Store->distance($client_geoposition['latitude'], $client_geoposition['longitude'], $item['latitude'], $item['longitude']);
				if ($distance_from_client_geoposition > 1) $distance_from_client_geoposition = round($distance_from_client_geoposition);
				else $distance_from_client_geoposition = round($distance_from_client_geoposition, 1);
			}
			
			$store_list[$k]['distance_from_selected_store'] = $distance_from_selected_store;
			$store_list[$k]['distance_from_client_geoposition'] = $distance_from_client_geoposition;
		
			if  ($distance_from_client_geoposition > 0) $distance = $distance_from_client_geoposition;
			else $distance = $distance_from_selected_store;
			
			$store_list[$k]['distance'] = $distance;
				
		}
		
		// sort by distance
		usort($store_list, ['Onxshop_Controller_Component_Ecommerce_Store_List', 'cmp']);
		
		// parse items
		foreach ($store_list as $k=>$item) {
			
			$item['modified'] = date("d/m/Y H:i", strtotime($item['modified']));
			$item['taxonomy_class'] = $this->buildTaxonomyClass($item['taxonomy']);
			if (trim($item['url']) == '') $item['url'] = $Store->conf['default_store_url'];
			
			$this->tpl->assign('ITEM', $item);
			
			if ($item['image_src']) $this->tpl->parse('content.list.item.image');
			
			$even_odd = ( 'odd' != $even_odd ) ? 'odd' : 'even';
			$publish = $item['publish'] ? '' : 'disabled';
			$this->tpl->assign('CLASS', "class='$even_odd $publish fullstore'");

			if ($k == 0) $this->tpl->assign('OPEN', 'open');
			else $this->tpl->assign('OPEN', '');
			
			// taxonomy
			$Onxshop_Request = new Onxshop_Request("component/ecommerce/store_taxonomy~store_id={$item['id']}~");
			$this->tpl->assign('STORE_TAXONOMY_LIST', $Onxshop_Request->getContent());
			
			// parse item
			$this->tpl->parse('content.list.item');
		}
		
		$this->tpl->parse('content.list');

		return true;
		
	}
	
	/**
	 * compare distance 
	 */
	 
	static function cmp($a, $b)
	{
	    if ($a['distance'] == $b['distance']) {
	        return 0;
	    }
	    return ($a['distance'] < $b['distance']) ? -1 : 1;
	}
	
    /**
     * create taxonomy_class from related_taxonomy
     */
			 
	public function buildTaxonomyClass($taxonomy) {
			
		if ($taxonomy) {
			$related_taxonomy = explode(',', $taxonomy);
			
			$taxonomy_class = '';
			
			if (is_array($related_taxonomy)) {
				foreach ($related_taxonomy as $t_item) {
					$taxonomy_class .= "t{$t_item} ";
				}
			}
			
		} else {
			
			$taxonomy_class = '';
			
        }

        return $taxonomy_class;
	}
}
