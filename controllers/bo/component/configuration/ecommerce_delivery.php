<?php
/**
 * Copyright (c) 2009-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/component/configuration.php');
require_once('models/ecommerce/ecommerce_delivery_carrier.php');
require_once('models/ecommerce/ecommerce_delivery_carrier_zone.php');
require_once('models/ecommerce/ecommerce_delivery_carrier_rate.php');
require_once('models/ecommerce/ecommerce_delivery_carrier_zone_to_country.php');
require_once('models/international/international_country.php');

class Onxshop_Controller_Bo_Component_Configuration_Ecommerce_Delivery extends Onxshop_Controller_Bo_Component_Configuration {

    /* model instances */
    protected $Delivery_Carrier;
    protected $Delivery_Zone;
    protected $Delivery_Zone_To_Country;
    protected $Delivery_Rate;
    protected $Country;

    /**
     * custom action
     */
    
    public function mainAction() {

        $this->initModels();
        $data = $this->readPostInput();

        if ($data) {

            $this->saveData($data);
            $data = $this->readData();
            $this->outputDataAsJson($data);
                    
        } else {

            $data = $this->readData();
            $this->outputDataToTemplate($data);
        }

        return true;
    
    }

    protected function initModels()
    {
        $this->Delivery_Carrier = new ecommerce_delivery_carrier();
        $this->Delivery_Zone = new ecommerce_delivery_carrier_zone();
        $this->Delivery_Zone_To_Country = new ecommerce_delivery_carrier_zone_to_country();
        $this->Delivery_Rate = new ecommerce_delivery_carrier_rate();
        $this->Country = new international_country();
    }

    /**
     * Read response payload JSON and convert to associative array
     * @return Array
     */
    protected function readPostInput()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    protected function outputDataAsJson(&$data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    protected function outputDataToTemplate(&$data)
    {
        $this->tpl->assign('DATA', json_encode($data));
        $this->tpl->assign('COUNTRIES', json_encode($this->Country->listing('', 'name ASC')));
        $this->tpl->assign("CURRENCY", $GLOBALS['onxshop_conf']['global']['default_currency']);
    }

    protected function readData()
    {
        $zones = $this->Delivery_Zone->getList();
        $carriers = $this->Delivery_Carrier->listing('', 'priority DESC, id ASC');
        $rates = $this->Delivery_Rate->listing('', 'weight_from ASC');

        foreach ($zones as &$zone) {
            foreach ($carriers as $carrier) {
                if ($zone['id'] == $carrier['zone_id']) {

                    $carrier['order_value_from'] = (float) $carrier['order_value_from'];
                    $carrier['order_value_to'] = (float) $carrier['order_value_to'];
                    $carrier['publish'] = ($carrier['publish'] == 1);

                    foreach ($rates as $rate) {
                        if ($rate['carrier_id'] == $carrier['id']) {
                            $rate['weight_from'] = (float) $rate['weight_from'];
                            $rate['weight_to'] = (float) $rate['weight_to'];
                            $carrier['rates'][] = $rate;
                        }
                    }

                    $zone['carriers'][] = $carrier;

                }
            }
        }

        return $zones;
    }

    protected function saveData(&$data) {

        if (!is_array($data)) return;

        // save zones
        foreach ($data as $zone) {

            $zone['id'] = $this->saveItem($zone, $this->Delivery_Zone);

            if ($zone['id'] > 0 && !empty($zone['carriers'])) {

                // save carriers
                foreach ($zone['carriers'] as $index => $carrier) {

                    $carrier['priority'] = (count($zone['carriers']) - $index) * 10;
                    $carrier['zone_id'] = $zone['id'];
                    $carrier['id'] = $this->saveItem($carrier, $this->Delivery_Carrier);

                    // save rates
                    if ($carrier['id'] > 0 && !empty($carrier['rates'])) {

                        foreach ($carrier['rates'] as $rate) {
                            $rate['carrier_id'] = $carrier['id'];
                            if ($rate['weight_from'] === '' && $rate['weight_to'] === '' && $rate['price'] === '') {
                                if ($rate['id'] > 0) $this->Delivery_Rate->delete($rate['id']);
                            } else {
                                $this->saveItem($rate, $this->Delivery_Rate);
                            }
                        }
                    }
                }
            }

            // save zone countries
            $this->saveCountries($zone['id'], $zone['countries']);
        }
    }

    protected function saveItem($data, $model) {

        $result = array();

        foreach ($model->_metaData as $columnName => $attributes) {
            if (isset($data[$columnName])) {
                if ($attributes['validation'] == 'int')
                    $result[$columnName] = (int) $data[$columnName];
                else if ($attributes['validation'] == 'decimal')
                    $result[$columnName] = (float) $data[$columnName];
                else
                    $result[$columnName] = (string) $data[$columnName];
            }
        }

        return $model->save($result);
    }

    protected function saveCountries($zone_id, $countries) {

        if (!is_numeric($zone_id)) return false;
        $countries = (array) $countries;
        foreach ($countries as $id => $country) if ($country) $country_ids[] = $id;
        $this->Delivery_Zone_To_Country->batchUpdate($zone_id, $country_ids);
    }
}
