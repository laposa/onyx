<?php
/**
 *
 * Copyright (c) 2013-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
require_once('models/common/common_comment.php');

class ecommerce_recipe_review extends common_comment {

    /**
     * NOT NULL REFERENCES ecommerce_recipe(id) ON UPDATE CASCADE ON DELETE RESTRICT
     * @access private
     */
    var $node_id;


    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "CREATE TABLE ecommerce_recipe_review (
            id serial PRIMARY KEY NOT NULL,
            parent int REFERENCES ecommerce_recipe_review ON UPDATE CASCADE ON DELETE CASCADE,
            node_id int REFERENCES ecommerce_recipe ON UPDATE CASCADE ON DELETE RESTRICT,
            title varchar(255),
            content text,
            author_name varchar(255),
            author_email varchar(255),
            author_website varchar(255),
            author_ip_address varchar(255),
            customer_id int NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
            created timestamp(0) default now(),
            publish smallint,
            rating smallint default 0,
            relation_subject text
        );
        CREATE INDEX ecommerce_recipe_review_node_id_key1 ON ecommerce_recipe_review USING btree (node_id);
        ";
        
        return $sql;
    }
    
    /**
     * get tree
     */
    
    function getTree($node_id, $public = 1, $sort = 'ASC') {

        $sql = "SELECT id, parent, title as name, title as title, content, author_name, author_email, author_website, author_ip_address, customer_id, created, rating, relation_subject FROM ecommerce_recipe_review WHERE publish >= $public AND node_id='$node_id' ORDER BY parent, created $sort";

        $records = $this->executeSql($sql);

        return $records;
    }

    /**
     * Return all node ids that were used to add a comment
     */
    function getUsedNodes() {
        $sql = "SELECT distinct(node_id) AS node_id, ecommerce_recipe.title FROM ecommerce_recipe_review
            LEFT JOIN ecommerce_recipe ON ecommerce_recipe.id = node_id
            ORDER BY ecommerce_recipe.title ASC";
        $records = $this->executeSql($sql);
        return $records;
    }

}
