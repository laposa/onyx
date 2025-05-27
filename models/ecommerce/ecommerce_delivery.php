<?php
/**
 * class ecommerce_delivery
 *
 * Copyright (c) 2009-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * This table will say, which shipping_type (shipping controller) is possible with
 * an product type.
 * UPS, royal mail, CityLink, email, download
 * -fixed rate (if amount > x) shipping = 0)
 * -weight and zone (WZ)
 * -size
 */

class ecommerce_delivery extends Onyx_Model {

    /**
     * PRIMARY KEY
     * @access private
     */
    var $id;
    /**
     * REFERENCES ecommerce_order ON UPDATE CASCADE ON DELETE RESTRICT
     * @access private
     */
    var $order_id;
    /**
     *
     * @access private
     */
    var $carrier_id;
    /**
     * @access private
     */
    var $value_net;
    /**
     * @access private
     */
    var $vat;
    /**
     * @access private
     */
    var $vat_rate;
    /**
     * @access private
     */
    var $required_datetime;
    /**
     * @access private
     */
    var $note_customer;
    /**
     * @access private
     */
    var $note_backoffice;
    /**
     * @access private
     */
    var $other_data;

    var $weight;

    var $_metaData = array(
        'id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'order_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'carrier_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'value_net'=>array('label' => '', 'validation'=>'decimal', 'required'=>true),
        'vat'=>array('label' => '', 'validation'=>'decimal', 'required'=>true),
        'vat_rate'=>array('label' => '', 'validation'=>'decimal', 'required'=>true),
        'required_datetime'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
        'note_customer'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'note_backoffice'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'other_data'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'weight'=>array('label' => '', 'validation'=>'int', 'required'=>true)
        );

    /**
     * create table sql
     */

    private function getCreateTableSql() {

        $sql = "CREATE TABLE ecommerce_delivery (
            id serial NOT NULL PRIMARY KEY,
            order_id int REFERENCES ecommerce_order ON UPDATE CASCADE ON DELETE RESTRICT,
            carrier_id integer REFERENCES ecommerce_delivery_carrier ON UPDATE CASCADE ON DELETE CASCADE ,
            value_net decimal(12,5) ,
            vat decimal(12,5) ,
            vat_rate decimal(12,5) ,
            required_datetime timestamp(0) without time zone,
            note_customer text ,
            note_backoffice text ,
            other_data text,
            weight integer NOT NULL DEFAULT 0
        );
        ";

        return $sql;
    }

    /**
     * insert delivery
     */

    function insertDelivery($delivery_data) {
        $delivery_data['other_data'] = serialize($delivery_data['other_data']);

        if ($id = $this->insert($delivery_data)) return $id;
        else return false;
    }

    /**
     * get delivery list for an order
     */

    function getDeliveryListByOrderId($order_id) {
        if (!is_numeric($order_id)) {
            msg("ecommerce_delivery.getDeliveryListByOrderId(): order_id is not numeric", 'error', 1);
            return false;
        }

        $list = $this->listing("order_id = $order_id");
        foreach ($list as $key=>$val) {
            $list[$key]['carrier_detail'] = $this->getCarrierDetail($val['carrier_id']);
        }
        return $list;
    }

    /**
     * get delivery by order id
     */

    function getDeliveryByOrderId($order_id) {

        $list = $this->getDeliveryListByOrderId($order_id);

        $delivery = $list[0];
        $delivery['value'] = $delivery['value_net'] + $delivery['vat'];

        return $delivery;
    }

    /**
     * get carrier detail
     */

    function getCarrierDetail($carrier_id) {
        require_once('models/ecommerce/ecommerce_delivery_carrier.php');
        $Carrier = new ecommerce_delivery_carrier();
        $detail = $Carrier->getDetail($carrier_id);

        return $detail;
    }

    /**
     * Calculate delivery rate for given carrier and basket content
     *
     * @param  Array  $basket              Basket content
     * @param  int    $carrier_id          Carrier id
     * @param  int    $delivery_address_id Delivery address id
     * @return Array                       Delivery rate and VAT
     */
    function calculateDelivery($basket, $carrier_id, $delivery_address_id, $promotion_detail)
    {
        require_once('models/client/client_address.php');
        $Address = new client_address();
        $address_detail = $Address->detail($delivery_address_id);
        $country_id = $address_detail ? (int) $address_detail['country_id'] : null;

        return $this->calculateDeliveryForCountry($basket, $carrier_id, $country_id, $promotion_detail);
    }

    /**
     * Calculate delivery rate for given carrier and basket content
     *
     * @param  Array  $basket              Basket content
     * @param  int    $carrier_id          Carrier id
     * @param  int    $country_id          Delivery Country id
     * @return Array                       Delivery rate and VAT
     */
    function calculateDeliveryForCountry($basket, $carrier_id, $country_id, $promotion_detail)
    {
        //if there is a product with vat rate > 0, add vat to the shipping
        $add_vat = $this->findVATEligibility($basket);

        require_once('models/ecommerce/ecommerce_delivery_carrier.php');
        $Delivery_Carrier = new ecommerce_delivery_carrier();

        // first check if there are restricted items in the basket
        if (!$this->checkDeliveryRestrictions($basket, $country_id)) return false;

        // check if the delivery is available for given order value and weight
        $price = $Delivery_Carrier->getDeliveryRate(
            $carrier_id,
            $basket['sub_total']['price'],
            $basket['total_weight_gross']
        );

        // false means method is not available for given weight and amount
        if ($price === false) return false;

        // zero weight means free delivery
        if ($basket['total_weight_gross'] == 0) return $this->getFreeDelivery();

        // check free delivery promotion
        require_once('models/ecommerce/ecommerce_promotion.php');
        $Promotion = new ecommerce_promotion();
        $Promotion->setCacheable(false);

        if ($Promotion->freeDeliveryAvailable($carrier_id, $country_id, $promotion_detail))
            return $this->getFreeDelivery($basket['total_weight_gross']);

        return array(
            'value_net' => sprintf("%0.2f", $price),
            'weight' => $basket['total_weight_gross'],
            'vat_rate' => $add_vat,
            'vat' => $price * $add_vat / 100,
            'value' => sprintf("%0.2f", $price * ($add_vat + 100) / 100)
        );

    }

    /**
     * Get free delivery array
     */
    function getFreeDelivery($weight = 0)
    {
        return array(
            'value_net' => sprintf("%0.2f", 0),
            'weight' => $weight,
            'vat_rate' => 0,
            'vat' => 0,
            'value' => sprintf("%0.2f", 0)
        );
    }

    /**
     * If basket contains at least one VAT item, return VAT rate
     *
     * @param unknown_type $basket
     * @return unknown
     */

    function findVATEligibility($basket) {

        if (!is_array($basket)) return false;
        foreach ($basket['items'] as $item) {
            if ($item['vat_rate'] > 0) {
                $vat_rate = (string) $item['vat_rate'];
                return $vat_rate;
            }
        }

        return 0;
    }

    /**
     * check if all items in the basket can be delivered
     * requires basket full detail
     */
    public function checkDeliveryRestrictions(&$basket, $country_id)
    {
        if (!is_numeric($country_id) || $country_id == 0) return false;

        require_once('models/ecommerce/ecommerce_delivery_carrier_zone.php');
        $DeliveryZone = new ecommerce_delivery_carrier_zone();
        $delivery_zone_id = $DeliveryZone->getZoneIdByCountry($country_id);

        $products = array();

        foreach ($basket['items'] as &$item) {

            $zones = $item['product']['variety']['limit_to_delivery_zones'];

            if (!empty($zones)) {

                $zones = explode(",", $zones);

                if (is_array($zones)) {

                    if (!in_array($delivery_zone_id, $zones)) {
                        $products[] = $item['product']['name'];
                    }
                }
            }
        }

        if (count($products) > 0) {
            if (!$this->container->has('ecommerce_delivery:not_deliverable_products_message')) {
                msg("Sorry, we're not able to deliver the following products to your country: " . implode(" ,", $products), 'error');
                $this->container->set('ecommerce_delivery:not_deliverable_products_message', true);
            }

            return false;
        }

        return true;
    }

}
