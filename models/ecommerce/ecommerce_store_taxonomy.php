<?php

/**
 * class ecommerce_store_taxonomy
 *
 * Copyright (c) 2009-2016 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
require_once('models/common/common_node_taxonomy.php');

class ecommerce_store_taxonomy extends common_node_taxonomy {

    /**
     * NOT NULL REFERENCES ecommerce_store ON UPDATE CASCADE ON DELETE CASCADE
     * @access private
     */
    var $node_id;

    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "
CREATE TABLE ecommerce_store_taxonomy ( 
    id serial NOT NULL PRIMARY KEY,
    node_id int NOT NULL REFERENCES ecommerce_store ON UPDATE CASCADE ON DELETE CASCADE,
    taxonomy_tree_id int NOT NULL REFERENCES common_taxonomy_tree ON UPDATE CASCADE ON DELETE CASCADE
);
ALTER TABLE ONLY ecommerce_store_taxonomy ADD CONSTRAINT ecommerce_store_taxonomy_node_id_key UNIQUE (node_id, taxonomy_tree_id);
        ";
        
        return $sql;
    }
    
    /**
     * init configuration
     */
     
    static function initConfiguration() {
        if (array_key_exists('ecommerce_store_taxonomy', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['ecommerce_store_taxonomy'];
        else $conf = array();
        
        if (!is_numeric($conf['options_id'])) $conf['options_id'] = 2;
        
        return $conf;
    }
    
    /**
     * get relations
     */
    
    function getRelationsToStore($store_id) {
    
        if (!is_numeric($store_id)) return false;
        
        $relations_list = $this->listing("node_id = $store_id");
        
        foreach($relations_list as $item) {
            $relations[] = $item['taxonomy_tree_id'];
        }
        
        return $relations;
        
    }
}
