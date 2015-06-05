<?php

/**
 * class ecommerce_recipe_taxonomy
 *
 * Copyright (c) 2013-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
require_once('models/common/common_node_taxonomy.php');

class ecommerce_recipe_taxonomy extends common_node_taxonomy {

	/**
	 * NOT NULL REFERENCES ecommerce_recipe ON UPDATE CASCADE ON DELETE CASCADE
	 * @access private
	 */
	var $node_id;

	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE ecommerce_recipe_taxonomy (
    id serial PRIMARY KEY NOT NULL,
    node_id integer NOT NULL REFERENCES ecommerce_recipe(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    taxonomy_tree_id integer NOT NULL REFERENCES common_taxonomy_tree(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    UNIQUE (node_id, taxonomy_tree_id)
);
CREATE INDEX ecommerce_recipe_taxonomy_node_id_key1 ON ecommerce_recipe_taxonomy USING btree (node_id);
CREATE INDEX ecommerce_recipe_taxonomy_taxonomy_tree_id_key ON ecommerce_recipe_taxonomy USING btree (taxonomy_tree_id);
		";
		
		return $sql;
	}
	
	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {
	
		if (array_key_exists('ecommerce_recipe_taxonomy', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['ecommerce_recipe_taxonomy'];
		else $conf = array();
		
		return $conf;
	}
	
	/**
	 * get relations
	 */
	
	function getRelationsToRecipe($recipe_id) {
	
		if (!is_numeric($recipe_id)) return false;
		
		$relations_list = $this->listing("node_id = $recipe_id");
		
		$relations = array();
		foreach($relations_list as $item) {
			$relations[] = $item['taxonomy_tree_id'];
		}
		
		return $relations;
		
	}

}
