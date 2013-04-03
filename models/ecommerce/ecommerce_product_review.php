<?php

/**
 * class ecommerce_product_review
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
require_once('models/common/common_comment.php');

class ecommerce_product_review extends common_comment {

	/**
	 * NOT NULL REFERENCES ecommerce_product(id) ON UPDATE CASCADE ON DELETE RESTRICT
	 * @access private
	 */
	var $node_id;


	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE ecommerce_product_review (
    id serial PRIMARY KEY NOT NULL,
    parent int REFERENCES ecommerce_product_review ON UPDATE CASCADE ON DELETE CASCADE,
    node_id int REFERENCES ecommerce_product ON UPDATE CASCADE ON DELETE RESTRICT,
    title varchar(255) ,
    content text ,
    author_name varchar(255) ,
    author_email varchar(255) ,
    author_website varchar(255) ,
	author_ip_address varchar(255),
    customer_id int NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
    created timestamp(0) default now(),
    publish smallint,
	rating smallint default 0,
	relation_subject text
);
		";
		
		return $sql;
	}
	
	/**
	 * get tree
	 */
	
	function getTree($node_id, $public = 1, $sort = 'ASC') {

		$sql = "SELECT id, parent, title as name, title as title, content, author_name, author_email, author_website, author_ip_address, customer_id, created, rating, relation_subject FROM ecommerce_product_review WHERE publish >= $public AND node_id='$node_id' ORDER BY parent, created $sort";

		$records = $this->executeSql($sql);

		return $records;
	}

}
