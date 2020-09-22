<?php

/**
 * class ecommerce_product_taxonomy
 *
 * Copyright (c) 2009-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
require_once('models/common/common_node_taxonomy.php');

class ecommerce_product_taxonomy extends common_node_taxonomy {

    /**
     * NOT NULL REFERENCES ecommerce_product ON UPDATE CASCADE ON DELETE CASCADE
     * @access private
     */
    var $node_id;

    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "
CREATE TABLE ecommerce_product_taxonomy ( 
    id serial NOT NULL PRIMARY KEY,
    node_id int NOT NULL REFERENCES ecommerce_product ON UPDATE CASCADE ON DELETE CASCADE,
    taxonomy_tree_id int NOT NULL REFERENCES common_taxonomy_tree ON UPDATE CASCADE ON DELETE CASCADE
);

ALTER TABLE ecommerce_product_taxonomy ADD CONSTRAINT product_node_id_taxonomy_tree_id_key UNIQUE (node_id, taxonomy_tree_id);
        ";
        
        return $sql;
    }
    
    /**
     * init configuration
     */
     
    static function initConfiguration() {
        if (array_key_exists('ecommerce_product_taxonomy', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['ecommerce_product_taxonomy'];
        else $conf = array();
        
        // any categories associated to this taxonomy ID will be drawn as a dropdown option list in component/ecommerce/product_options
        if (!is_numeric($conf['options_id'])) $conf['options_id'] = 2; // this value is taxonomy_tree_id
        
        return $conf;
    }
    
    /**
     * get relations
     */
    
    function getRelationsToProduct($product_id) {
    
        if (!is_numeric($product_id)) return false;
        
        $relations_list = $this->listing("node_id = $product_id");
        
        foreach($relations_list as $item) {
            $relations[] = $item['taxonomy_tree_id'];
        }
        
        return $relations;
        
    }
}
