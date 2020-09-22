<?php
/**
 * class ecommerce_delivery_carrier_zone
 *
 * Copyright (c) 2009-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_delivery_carrier_zone extends Onyx_Model {

    /**
     * PRIMARY KEY
     * @access private
     */
    public $id;
    /**
     * @access private
     */
    public $name;

    var $_metaData = array(
        'id' => array('label' => '', 'validation' => 'int', 'required' => true), 
        'name' => array('label' => '', 'validation' => 'string', 'required' => true)
    );
    
    /**
     * create table sql
     */
     
    private function getCreateTableSql()
    {
    
        $sql = "CREATE TABLE ecommerce_delivery_carrier_zone (
            id serial PRIMARY KEY,
            name varchar(255)
        );";
        
        return $sql;
    }

    public function getList()
    {

        require_once('models/ecommerce/ecommerce_delivery_carrier_zone_to_country.php');
        $Zone2Country = new ecommerce_delivery_carrier_zone_to_country();

        $zones = $this->listing();
        $conn = $Zone2Country->listing();

        foreach ($zones as &$zone) {
            $zone['countries'] = array();
            foreach ($conn as $c) {
                if ($c['zone_id'] == $zone['id']) $zone['countries'][$c['country_id']] = true;
            }
        }

        return $zones;
    }

    /**
     * Get Zone Id By Country
     */
    public function getZoneIdByCountry($country_id)
    {
        if (!is_numeric($country_id)) return false;

        require_once('models/ecommerce/ecommerce_delivery_carrier_zone_to_country.php');
        $Zone2Country = new ecommerce_delivery_carrier_zone_to_country();

        $zones = $Zone2Country->listing("country_id = $country_id");

        if (isset($zones[0]['zone_id'])) return $zones[0]['zone_id'];
        else return false;
    }

    /**
     * Get Zone Id by Address Id
     */

    public function getZoneIdByAddress($address_id)
    {
        if (!is_numeric($address_id)) return false;

        require_once('models/client/client_address.php');
        $Address = new client_address();
        $address = $Address->getDetail($address_id);

        return $this->getZoneIdByCountry($address['country_id']);
    }
}
