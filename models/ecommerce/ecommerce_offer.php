<?php
/**
 *
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_offer extends Onxshop_Model {

	var $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'description'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'product_variety_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'schedule_start'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'schedule_end'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
		'campaign_category_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'roundel_category_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'price_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'quantity'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'saving'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false)
		);

	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE ecommerce_offer (
	id serial NOT NULL PRIMARY KEY,
	description text,
	product_variety_id integer REFERENCES ecommerce_product_variety ON UPDATE CASCADE ON DELETE RESTRICT,
	schedule_start timestamp(0) without time zone,
	schedule_end timestamp(0) without time zone,
	campaign_category_id integer REFERENCES common_taxonomy_tree ON UPDATE CASCADE ON DELETE RESTRICT,
	roundel_category_id integer REFERENCES common_taxonomy_tree ON UPDATE CASCADE ON DELETE RESTRICT,
	price_id integer REFERENCES ecommerce_price ON UPDATE CASCADE ON DELETE RESTRICT,
	quantity integer,
	saving integer,
	created timestamp(0) without time zone,
	modified timestamp(0) without time zone,
	other_data text
);
		";
		
		return $sql;
	}
	
}
