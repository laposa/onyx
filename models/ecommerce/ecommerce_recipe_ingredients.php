<?php
/**
 * class ecommerce_recipe_ingredients
 * link products to recipe as ingredients
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_recipe_ingredients extends Onxshop_Model {

	/**
	 * @access private
	 */
	var $id;

	/**
	 * @access private
	 */
	var $recipe_id;

	/**
	 * @access private
	 */
	var $product_variety_id;

	/**
	 * @access private
	 */
	var $quantity;

	/**
	 * @access private
	 */
	var $units;

	/**
	 * @access private
	 */
	var $notes;
	

	var $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'recipe_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'product_variety_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'quantity'=>array('label' => '', 'validation'=>'decimal', 'required'=>true),
		'units'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'notes'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'group_title'=>array('label' => '', 'validation'=>'string', 'required'=>false)
	);

	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE ecommerce_recipe_ingredients (
    id integer NOT NULL PRIMARY KEY,
    recipe_id integer,
    product_variety_id integer NOT NULL,
    quantity real,
    units integer,
    notes text,
    group_title character varying(255)
);
		";
		
		return $sql;
	}
	
	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {

		if (array_key_exists('ecommerce_recipe_ingredients', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['ecommerce_recipe_ingredients'];
		else $conf = array();

		if (!is_numeric($conf['units_taxonomy_tree_id'])) $conf['units_taxonomy_tree_id'] = 74;

		return $conf;
	}

	public function getUnits() {

		$conf = self::initConfiguration();
		$parent = $conf['units_taxonomy_tree_id'];

		if (!is_numeric($parent)) return false;

		$sql = "SELECT common_taxonomy_tree.id, common_taxonomy_label.title
			FROM common_taxonomy_tree
			LEFT JOIN common_taxonomy_label ON common_taxonomy_label.id = common_taxonomy_tree.label_id
			WHERE common_taxonomy_tree.publish = 1 AND common_taxonomy_tree.parent = $parent
			ORDER BY common_taxonomy_tree.priority DESC, common_taxonomy_label.title ASC";

		return $this->executeSql($sql);
	}

}