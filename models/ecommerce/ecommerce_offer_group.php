<?php
/**
 *
 * Copyright (c) 2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_offer_group extends Onxshop_Model {

	var $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'title'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'description'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'schedule_start'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'schedule_end'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
		'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false)
		);

	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "CREATE TABLE ecommerce_offer_group (
			id serial NOT NULL PRIMARY KEY,
			title character varying(255),
			description text,
			schedule_start timestamp(0) without time zone,
			schedule_end timestamp(0) without time zone,
			publish integer DEFAULT 0 NOT NULL,
			created timestamp(0) without time zone,
			modified timestamp(0) without time zone,
			other_data text
		)";
		
		return $sql;
	}
	
}
