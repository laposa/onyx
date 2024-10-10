<?php
/**
 * class common_watchdog
 *
 * Copyright (c) 2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class common_watchdog extends Onyx_Model {

    /**
     * @access public
     */
    var $id;

    /**
     * @access public
     */
    var $name;

    /**
     * @access public
     */
    var $watched_item_id;

    /**
     * @access public
     */
    var $customer_id;

    /**
     * @access public
     */
    var $created;

    /**
     * @access public
     */
    var $modified;

    /**
     * @access public
     */
    var $publish;

    /**
     * @access public
     */
    var $other_data;

    var $_metaData = array(
        'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
        'name'=>array('label' => '', 'validation'=>'string', 'required'=>true),
        'watched_item_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'customer_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
        'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
        'publish'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'other_data'=>array('label' => '', 'validation'=>'string', 'required'=>false)
    );

    var $cache_local = array();

    /**
     * create table sql
     */
     
    private function getCreateTableSql() {

        $sql = "CREATE TABLE common_watchdog (
            id serial PRIMARY KEY NOT NULL,
            name character varying(255),
            watched_item_id integer,
            customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
            created timestamp without time zone,
            modified timestamp without time zone DEFAULT now(),
            publish smallint,
            other_data text
        )

        CREATE INDEX common_watchdog_combined_idx
            ON common_watchdog
            USING btree
            (name, watched_item_id, publish);
        ";

        return $sql;
    }
    
    /**
     * init configuration
     */
     
    static function initConfiguration()
    {
    
        if (array_key_exists('common_watchdog', $GLOBALS['onyx_conf'])) $conf = $GLOBALS['onyx_conf']['common_watchdog'];
        else $conf = array();

        return $conf;
    }

    /**
     * Check watchdog for given property
     *
     * If corresponding watchdog records are found, appropriate action is taken
     * 
     */
    public function checkWatchDog($name, $id, $old_value, $new_value, $no_action = false)
    {
        //TODO: pg_escape_string() function deprecated? may cause security issues?

        if (is_numeric($id)) $where = "AND (watched_item_id = $id OR watched_item_id IS NULL)";
        else $where = "AND watched_item_id IS NULL";

        $records = $this->listing("name = '$name' $where AND publish = 1");

        $numSent = 0;

        foreach ($records as $record)
        {
            switch ($name)
            {
                case 'back_in_stock_customer':

                    if (is_numeric($id) && $old_value == 0 && $new_value > 0) {

                        require_once('models/common/common_node.php');
                        $node_conf = common_node::initConfiguration();

                        $params = array(
                            'product' => $this->getProductInfo($id),
                            'unsubscribe' => array(
                                'page_id' => $node_conf['id_map-notifications'],
                                'key' => $this->generateKey($record['id']),
                                'id' => $record['id']
                            )
                        );
                        
                        if ($no_action) {
                            $numSent++;
                        } else {
                            $numSent += $this->sendNotification($record['customer_id'], 'notification_back_in_stock_customer', $params);
                            $this->setPublish($record['id'], 0);
                        }

                    }

                    break;

                case 'back_in_stock_admin':

                    if (is_numeric($id) && $old_value == 0 && $new_value > 0) {

                        $params = array(
                            'product' => $this->getProductInfo($id),
                            'old_value' => $old_value,
                            'new_value' => $new_value
                        );
                        
                        if ($no_action) $numSent++;
                        else $numSent += $this->sendNotification($record['customer_id'], 'notification_back_in_stock_admin', $params);
                    }

                    break;

                case 'out_of_stock_admin':

                    if (is_numeric($id) && $old_value > 0 && $new_value == 0) {

                        $params = array(
                            'product' => $this->getProductInfo($id),
                            'old_value' => $old_value,
                            'new_value' => $new_value
                        );
                        
                        if ($no_action) $numSent++;
                        else $numSent += $this->sendNotification($record['customer_id'], 'notification_out_of_stock_admin', $params);
                    }
                    break;

            }

        }

        return $numSent;
    }

    /**
     * Send email to customer
     * Return number of emails sent
     * 
     */
    public function sendNotification($customer_id, $template, $params)
    {
        require_once('models/common/common_email.php');
        require_once('models/client/client_customer.php');
        $Email = new common_email();
        $Customer = new client_customer();

        $customer = $Customer->getDetail($customer_id);
        if ($customer['id'] == 0 && $customer['status'] > 3) return 0;
        $params['customer'] = $customer;
        if (strlen($params['customer']['first_name']) == 0) $params['customer'] = 'Customer';

        //this allows use customer data and company data in the mail template
        //is passed as DATA to template in common_email->_format
        $GLOBALS['common_email'] = $params;

        $email_recipient = $customer['email']; 
        $name_recipient = $customer['first_name'] . ' ' . $customer['last_name'];

        $result = $Email->sendEmail($template, serialize($params), $email_recipient, $name_recipient);

        return $result ? 1 : 0;
    }

    protected function getProductInfo($product_variety_id)
    {
        // cache_local product info
        $key = "product_variety_id_{$product_variety_id}";
        if (isset($this->cache_local[$key])) return $this->cache_local[$key];

        require_once('models/ecommerce/ecommerce_product.php');
        $Product = new ecommerce_product();

        $variety = $Product->getProductVarietyDetail($product_variety_id);
        $product = $Product->productDetail($variety['product_id']);
        $homepage = $Product->getProductHomepage($variety['product_id']);
        $product['url'] = translateURL('page/' . $homepage['id']);
        $Image = new Onyx_Request("component/image&relation=product&role=main&width=120&node_id={$product['id']}&limit=0,1");

        $product['variety'] = $variety;
        $product['image'] = $Image->getContent();

        $this->cache_local[$key] = $product;

        return $product;

    }

    public function setPublish($id, $publish = 1)
    {
        $detail = $this->detail($id);
        if ($detail) {
            $detail['publish'] = $publish;
            $this->update($detail);
        }
    }

    public function generateKey($id)
    {
        $detail = $this->detail($id);
        return md5("{$detail['created']}-{$detail['customer_id']}-{$detail['watched_item_id']}-{$detail['id']}");
    }

    public function getDataForReport($date_from, $date_to)
    {
        require_once('models/common/common_email.php');
        $Email = new common_email();

        $date_from = date("Y-m-d", strtotime($date_from));
        $date_to = date("Y-m-d", strtotime($date_to));

        $where = "created > '$date_from' AND created < '$date_to' ";
        $where .= "AND template IN ('notification_back_in_stock_admin', 'notification_out_of_stock_admin', 'notification_back_in_stock_customer')";
        $list = $Email->listing($where, "id DESC");

        $result = array();
        foreach ($list as $item) {

            $params = unserialize($item['content']);

            $variety_id = $params['product']['variety']['id'];

            if ($variety_id > 0) {

                $day = date("Y-m-d", strtotime($item['created']));
                $is_back_in_stock = strpos($item['template'], "back_in_stock") !== false;
                $is_customer = strpos($item['template'], "customer") !== false;
                $key = $variety_id . "-" . $day . "-" . ((int) $is_back_in_stock);

                if (isset($result[$key])) {

                    if ($is_customer) {

                        $result[$key]["Customer Notification Sent"]++;
                        if (!empty($result[$key]["Customer Receipent Addresses"])) $result[$key]["Customer Receipent Addresses"] .= ", ";
                        $result[$key]["Customer Receipent Addresses"] .= $item['email_recipient'];

                    } else {

                        $result[$key]["Admin Notification Sent"]++;
                        if (!empty($result[$key]["Admin Receipent Addresses"])) $result[$key]["Admin Receipent Addresses"] .= ", ";
                        $result[$key]["Admin Receipent Addresses"] .= $item['email_recipient'];

                    }

                } else {

                    $product = $this->getProductInfo($variety_id);

                    $row = array(
                        "Type" => $is_back_in_stock ? "Back in Stock" : "Out of Stock",
                        "SKU" => $product['variety']['sku'],
                        "Product Id" => $params['product']['id'],
                        "Variety Id" => $variety_id,
                        "Product Name" => $product['name'],
                        "Variety Name" => $product['variety']['name'],
                        "Date and Time" => date("d/m/Y H:i:s", strtotime($item['created']))
                    );

                    if ($is_customer) {

                        $row["Admin Notification Sent"] = 0;
                        $row["Customer Notification Sent"] = 1;
                        $row["Admin Receipent Addresses"] = "";
                        $row["Customer Receipent Addresses"] = $item['email_recipient'];

                    } else {

                        $row["Admin Notification Sent"] = 1;
                        $row["Customer Notification Sent"] = 0;
                        $row["Admin Receipent Addresses"] = $item['email_recipient'];
                        $row["Customer Receipent Addresses"] = "";

                    }

                    $result[$key] = $row;
                }
            }
        }

        return $result;

    }
}
