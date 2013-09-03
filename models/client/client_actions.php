<?php
/**
 *
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
require_once('models/common/common_node_taxonomy.php');

class client_actions extends Onxshop_Model {

	/**
	 * @private
	 */
	var $id;

	/**
	 * @private
	 */
	var $customer_id;

	/**
	 * @private
	 */
	var $node_id;

	/**
	 * @private
	 */
	var $action_id;

	/**
	 * @private
	 */
	var $network;

	/**
	 * @private
	 */
	var $action_name;

	/**
	 * @private
	 */
	var $object_name;

	/**
	 * @private
	 */
	var $created;

	/**
	 * @private
	 */
	var $modified;

	/**
	 * @private
	 */
	var $other_data;

	var $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'customer_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'node_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'action_id'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'network'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'action_name'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'object_name'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false),
	);

	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "CREATE TABLE client_actions (
			id serial NOT NULL PRIMARY KEY,
			customer_id integer NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE CASCADE,
			node_id integer NOT NULL REFERENCES common_node ON UPDATE CASCADE ON DELETE CASCADE,
			action_id varchar(255),
			network varchar(255),
			action_name varchar(255),
			object_name varchar(255),
			created timestamp without time zone NOT NULL,
			modified timestamp without time zone NOT NULL,
			other_data text
		);

		CREATE INDEX client_actions_customer_id_key ON client_actions USING btree (customer_id);
		CREATE INDEX client_actions_network_key ON client_actions USING btree (network);
		";
			
		return $sql;
	}
	
	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {
		if (array_key_exists('client_actions', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['client_actions'];
		else $conf = array();
		
		return $conf;
	}


	/**
	 * Has Facebook app defined given Open Graph story?
	 * @param  string  $action Action name
	 * @param  string  $object Objectname name
	 * @return boolean
	 */
	static function hasOpenGraphStory($action, $object)
	{
		if (defined('ONXSHOP_FACEBOOK_APP_OG_STORIES')) {

			$stories = explode(',', ONXSHOP_FACEBOOK_APP_OG_STORIES);
			$action = strtolower(trim($action));
			$object = strtolower(trim($object));
	
			if (is_array($stories)) {
				foreach ($stories as $story) {
					$story = explode("-", trim($story));
					if (is_array($story) && count($story) == 2
						&& $action == strtolower(trim($story[0]))
						&& $object == strtolower(trim($story[1]))) return true;
				}
			}
		}

		return false;
	}

}
