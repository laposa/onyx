<?php
/**
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/component/configuration.php');
require_once('models/ecommerce/ecommerce_delivery_carrier.php');
require_once('models/ecommerce/ecommerce_delivery_carrier_zone.php');
require_once('models/ecommerce/ecommerce_delivery_carrier_zone_price.php');
require_once('models/international/international_country.php');

class Onxshop_Controller_Bo_Component_Configuration_Ecommerce_Delivery extends Onxshop_Controller_Bo_Component_Configuration {

	/* model instances */
	protected $Delivery_Carrier;
	protected $Delivery_Zone;
	protected $Delivery_Price;

	/** List of countries */
	protected $countries;

	/**
	 * custom action
	 */
	
	public function mainAction() {

		// create model instances
		$this->Delivery_Carrier = new ecommerce_delivery_carrier();
		$this->Delivery_Zone = new ecommerce_delivery_carrier_zone();
		$this->Delivery_Price = new ecommerce_delivery_carrier_zone_price();

		// load country list
		$Country = new international_country();
		$this->countries = $Country->listing();


		// process form
		$this->processFormSubmission($_POST['carrier']);

		// display form
		$this->parseForm();

		return true;
	
	}

	/**
	 * Process submited form data and save them to database
	 * 
	 * @param  Array $carriers Array of form values
	 */
	protected function processFormSubmission($carriers)
	{
		if (!is_array($carriers)) return false;

		foreach ($carriers as $carrier_id => $carrier) {

			// update row in ecommerce_delivery_carrier
			$this->Delivery_Carrier->update(array(
				"id" => (int) $carrier_id,
				"title" => $carrier['title'],
				"publish" => (int) $carrier['publish'],
				"priority" => (int) $carrier['priority'],
				"fixed_value" => (float) $carrier['fixed_value'],
				"fixed_percentage" => (float) $carrier['fixed_percentage'],
				"limit_list_products" => NULL,
				"limit_list_countries" => $this->processArray($carrier['limit_list_countries']),
				"free_delivery_map" => $this->processMap($carrier['free_delivery_map'])
			));

			$this->updateZonePrices($carrier['price']);
			$this->insertZonePrices($carrier['new_price']);
		}

		msg("Delivery configuration has been saved.");

		return true;
	
	}


	/**
	 * Filter out non-numeric values and return coma separated list of values
	 * 
	 * @param  array $values Array of integers
	 * @return String Comma separated list of integers
	 */
	protected function processArray($values)
	{
		$result = array();

		if (is_array($values)) {
			foreach ($values as $item) 
				if (is_numeric($item)) $result[] = (int) $item;
		}

		return implode(",", $result);
	}


	/**
	 * Filter out non-numeric values and return serialized map
	 * 
	 * @param  array $values Array of integers
	 * @return String Comma separated list of integers
	 */
	protected function processMap($values)
	{
		$result = array();

		if (is_array($values)) {
			foreach ($values as $country_id => $value)
				if ($country_id > 0 && is_numeric($country_id) && is_numeric($value)) {
					$country_id = (int) $country_id;
					$result[$country_id] = (int) $value;
				}
		}

		if (count($result) > 0) return serialize($result);
		else return null;
	}


	/**
	 * Process submited form zone data and update the database
	 * 
	 * @param  Array $prices Array of form values
	 */
	protected function updateZonePrices($prices)
	{
		if (!is_array($prices)) return;

		// update prices in ecommerce_delivery_carrier_zone_price
		foreach ($prices as $price_id => $price) {
			
			$price_id = (int) $price_id;
			$value = (float) $price['price'];

			if ($value > 0) {

				// read data to update
				$data = $this->Delivery_Price->detail($price_id);

				$data["weight"] = (int) $price['weight'];
				$data["price"] = $value;

				// update price row
				$this->Delivery_Price->update($data);

			} else {

				// delete price row
				$this->Delivery_Price->delete((int) $price_id);
			}
		}

	}

	/**
	 * Process submited form zone data and do db inserts
	 * 
	 * @param  Array $zones Array of form values
	 */
	protected function insertZonePrices($zones)
	{
		if (!is_array($zones)) return;

		$currency_code = $GLOBALS['onxshop_conf']['global']['default_currency'];

		foreach ($zones as $zone_id => $zone) {
			
			$zone_id = (int) $zone_id;

			foreach ($zone as $price) {

				$weight = (int) $price['weight'];
				$value = (float) $price['price'];

				if ($value > 0) {

					// insert price row
					$this->Delivery_Price->insert(array(
						"zone_id" => $zone_id,
						"weight" => $weight,
						"price" => $value,
						"currency_code" => $currency_code
					));

				}

			}

		}

	}

	/**
	 * Display form items
	 */
	protected function parseForm()
	{
		$carriers = $this->Delivery_Carrier->getList("", "publish DESC, priority DESC");

		$this->tpl->assign("CURRENCY", $GLOBALS['onxshop_conf']['global']['default_currency']);

		// parse form
		foreach ($carriers as $carrier) {

			// parse main info
			$carrier_id = (int) $carrier['id'];
			$carrier['publish'] = $carrier['publish'] ? 'checked' : '';
			$carrier['fixed_value'] = number_format($carrier['fixed_value'], 2, '.', '');
			$carrier['fixed_percentage'] = number_format($carrier['fixed_percentage'], 2, '.', '');
			$this->tpl->assign("CARRIER", $carrier);

			// parse zone prices table
			$this->parseZones($carrier_id);

			// parse country restriction
			if (is_array($carrier['limit_list_countries'])) {
				foreach ($carrier['limit_list_countries'] as $country_id) {
					$this->parseCountries("content.carrier.limit_list_countries.item", $country_id);
					$this->tpl->parse("content.carrier.limit_list_countries");
				}
			}
			$this->parseCountries("content.carrier.limit_list_countries_empty.item", "");
			$this->tpl->parse("content.carrier.limit_list_countries_empty");

			// parse free delivery by country
			if (is_array($carrier['free_delivery_map'])) {
				foreach ($carrier['free_delivery_map'] as $country_id => $value) {
					$this->parseCountries("content.carrier.free_delivery_map.item", $country_id);
					$this->tpl->assign("COUNTRY_ID", $country_id);
					$this->tpl->assign("VALUE", $value);
					$this->tpl->parse("content.carrier.free_delivery_map");
				}
			}
			$this->parseCountries("content.carrier.free_delivery_map_empty.item", "");
			$this->tpl->parse("content.carrier.free_delivery_map_empty");

			$this->tpl->parse("content.carrier");
		}

	}

	/**
	 * Display zone prices table
	 */
	protected function parseZones($carrier_id) {

		// load data from db
		$zones = $this->Delivery_Zone->listing("carrier_id = $carrier_id");

		if (count($zones) > 0) {

			// parse zone rates table
			foreach ($zones as $zone) {

				$zone_id = (int) $zone['id'];
				$this->tpl->assign("ZONE", $zone);

				$prices = $this->Delivery_Price->listing("zone_id = $zone_id", "weight ASC");

				foreach ($prices as $price) {

					$this->tpl->assign("PRICE", $price);
					$this->tpl->parse("content.carrier.zone_rates.zone.price");

				}

				$this->tpl->parse("content.carrier.zone_rates.zone_header");
				$this->tpl->parse("content.carrier.zone_rates.zone_header2");
				$this->tpl->parse("content.carrier.zone_rates.zone");

			}

			// parse table
			$this->tpl->parse("content.carrier.zone_rates");

		} else {
			// no zones defined
			$this->tpl->parse("content.carrier.no_zones");
		}

	}

	/**
	 * Pollute JavaScript array with country list
	 */
	public function parseCountries($block, $selected_id)
	{
		foreach ($this->countries as $i => $country) {
			$country['comma'] = ($i == count($this->countries) - 1) ? "" : ",";
			$country['selected'] = ($country['id'] == $selected_id) ? "selected" : "";
			$this->tpl->assign("COUNTRY", $country);
			$this->tpl->parse($block);
		}
	}

}
