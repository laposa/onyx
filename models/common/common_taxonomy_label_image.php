<?php
require_once('models/common/common_image.php');

/**
 * Copyright (c) 2009-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */
 
class common_taxonomy_label_image extends common_image {

    /**
     * @access private
     */
    var $node_id;

    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "
CREATE TABLE common_taxonomy_label_image ( 
    id serial NOT NULL PRIMARY KEY,
    src character varying(255),
    role character varying(255),
    node_id int NOT NULL REFERENCES common_taxonomy_label ON UPDATE CASCADE ON DELETE CASCADE,
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
    
}
