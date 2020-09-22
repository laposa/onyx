<?php
require_once('models/common/common_image.php');

/**
 * class ecommerce_product_image
 *
 * Copyright (c) 2009-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_product_image extends common_image {

    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "
CREATE TABLE ecommerce_product_image ( 
    id serial NOT NULL PRIMARY KEY,
    src character varying(255),
    role character varying(255),
    node_id int NOT NULL REFERENCES ecommerce_product ON UPDATE CASCADE ON DELETE CASCADE,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone,
    author integer,
    content text,
    other_data text,
    link_to_node_id integer,
    customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT
);
        ";
        
        return $sql;
    }
    
    /**
     * init configuration
     */
    /*
    static function initConfiguration() {
    
        $image_default_conf = common_image::initImageDefaultConfiguration();
        
        if (array_key_exists('ecommerce_product_image', $GLOBALS['onyx_conf'])) $conf = $GLOBALS['onyx_conf']['ecommerce_product_image'];
        else $conf = array();

        if (is_array($conf)) $conf = array_merge($image_default_conf, $conf);
        else $conf = $image_default_conf;

        if ($conf['cycle_fx'] == '') $conf['cycle_fx'] = $image_default_conf['cycle_fx'];
        if ($conf['cycle_easing'] == '') $conf['cycle_easing'] = $image_default_conf['cycle_easing'];
        if (!is_numeric($conf['cycle_timeout'])) $conf['cycle_timeout'] = $image_default_conf['cycle_timeout'];
        if (!is_numeric($conf['cycle_speed'])) $conf['cycle_speed'] = $image_default_conf['cycle_speed'];
        
        return $conf;
    }*/
}
