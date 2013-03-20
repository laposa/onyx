<?php
/**
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/common/common_node.php');
require_once('models/ecommerce/ecommerce_store.php');

class Onxshop_Controller_Component_Store_Nearby_Radius extends Onxshop_Controller {

	/**
	 * main action
	 */
	public function mainAction()
	{
		// get selected store for detail
		$node_id = (int) $this->GET['node_id'];

		if ($node_id > 0) {

			$Node = new common_node();
			$Store = new ecommerce_store();
			$current_store = $Store->findStoreByNode($node_id);

			if ($current_store & is_numeric($current_store['id'])) {

				$distance = 0.20;
				$lat1 = (float) ((float) $current_store['latitude'] - $distance);
				$lat2 = (float) ((float) $current_store['latitude'] + $distance);
				$lng1 = (float) ((float) $current_store['longitude'] - $distance);
				$lng2 = (float) ((float) $current_store['longitude'] + $distance);
				$stores = $Store->listing("id != {$current_store['id']} AND publish = 1 AND " . 
					"latitude BETWEEN $lat1 AND $lat2 AND longitude BETWEEN $lng1 AND $lng2");

				if (count($stores) > 0) {

					$distances = array();
					foreach ($stores as $i => $store) {
						$distance = $this->distance($current_store['latitude'], $current_store['longitude'],
							$store['latitude'], $store['longitude']);
						$distances[$i] = $distance;
					}

					asort($distances, SORT_NUMERIC);

					$i = 0;

					foreach ($distances as $store_index => $distance) {

						$store = $stores[$store_index];

						if ($distance < 1) $distance = round($distance * 1000) . " meters";
						else $distance = number_format($distance, 1) . " km";
						$store['distance'] = $distance;

						// get store page for url
						$page = $Store->getStoreHomepage($store['id']);
						$store['node_id'] = $page['id'];

						$this->tpl->assign("STORE", $store);

						$column = $i % 3 + 1;
						$this->tpl->parse("content.list.column$column");

						$i++;
						if ($i == 9) break; // 9 sotres is enough

					}
					$this->tpl->parse("content.list");
				}
			}
		}

		return true;
	}

	/**
	 * Get distance between two points on sphere (in km)
	 * http://en.wikipedia.org/wiki/Haversine_formula
	 * 
	 * @param  float $lat1 First point latitude
	 * @param  float $lng1 First point longitude
	 * @param  float $lat2 Second point latitude
	 * @param  float $lng2 Second point longitude
	 * @return float
	 */
	public function distance($lat1, $lng1, $lat2, $lng2)
	{
		$earth_radius = 6371;

		$sin_lat = sin(deg2rad($lat2  - $lat1) / 2.0);
		$sin2_lat = $sin_lat * $sin_lat;

		$sin_lng = sin(deg2rad($lng2 - $lng1) / 2.0);
		$sin2_lng = $sin_lng * $sin_lng;

		$cos_lat1 = cos($lat1);
		$cos_lat2 = cos($lat2);

		$sqrt = sqrt($sin2_lat + ($cos_lat1 * $cos_lat2 * $sin2_lng));

		$distance = 2.0 * $earth_radius * asin($sqrt);

		return $distance;
	}

}

