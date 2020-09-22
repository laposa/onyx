<?php
/**
 * class ecommerce_promotion_type
 * link to document with vat rates
 *
 * Copyright (c) 2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_promotion_type extends Onxshop_Model {

    /**
     * @access private
     */
    public $id;

    /**
     * @access private
     */
    public $title;

    /**
     * @access private
     */
    public $description;

    /**
     * @access private
     */
    public $taxable;

    /**
     * @access private
     */
    public $publish;

    /**
     * @access private
     */
    public $created;

    /**
     * @access private
     */
    public $modified;

    /**
     * @access private
     */
    public $other_data;


    var $_metaData = array(
        'id' => array('label' => '', 'validation'=>'int', 'required'=>true), 
        'title' => array('label' => '', 'validation'=>'string', 'required'=>true),
        'description' => array('label' => '', 'validation'=>'string', 'required'=>false),
        'taxable' => array('label' => '', 'validation'=>'int', 'required'=>true),
        'publish' => array('label' => '', 'validation'=>'int', 'required'=>true),
        'created' => array('label' => '', 'validation'=>'datetime', 'required'=>false),
        'modified' => array('label' => '', 'validation'=>'datetime', 'required'=>false),
        'other_data' => array('label' => '', 'validation'=>'int', 'required'=>false)
    );

    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "CREATE TABLE ecommerce_promotion_type (
            id serial NOT NULL PRIMARY KEY,
            title character varying(255),
            description text,
            taxable smallint DEFAULT 0 NOT NULL,
            publish smallint DEFAULT 1 NOT NULL,
            created timestamp(0) without time zone DEFAULT now() NOT NULL,
            modified timestamp(0) without time zone DEFAULT now() NOT NULL,
            other_data text
        )";
        
        return $sql;
    }
    
    /**
     * init configuration
     */
     
    static function initConfiguration() {
        if (array_key_exists('ecommerce_promotion', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['ecommerce_promotion'];
        else $conf = array();
        return $conf;
    }
}