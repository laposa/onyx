<?php
/**
 *
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
require_once('models/common/common_node_taxonomy.php');

class client_customer_token extends Onxshop_Model {

	/**
	 * @access private
	 */
	var $id;

	/**
	 * @access private
	 */
	var $customer_id;

	/**
	 * @access private
	 */
	var $publish;

	/**
	 * @access private
	 */
	var $token;

	/**
	 * @access private
	 */
	var $oauth_data;

	/**
	 * @access private
	 */
	var $other_data;

	/**
	 * @access private
	 */
	var $ttl;

	/**
	 * @access private
	 */
	var $ip_address;

	/**
	 * @access private
	 */
	var $http_user_agent;

	/**
	 * @access private
	 */
	var $created;

	/**
	 * @access private
	 */
	var $modified;

	var $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'customer_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'publish'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'token'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'oauth_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false),
		'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false),
		'ttl'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'ip_address'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'http_user_agent'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>true)
	);

	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "CREATE TABLE client_customer_token (
			id serial NOT NULL PRIMARY KEY,
			customer_id integer NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE CASCADE,
			publish smallint DEFAULT 0 NOT NULL,
			token character(32),
			oauth_data text,
			other_data text,
			ttl integer,
			ip_address varchar(255),
			http_user_agent varchar(255),
			created timestamp without time zone NOT NULL,
			modified timestamp without time zone NOT NULL
		);

		CREATE INDEX client_customer_token_key ON client_customer_token USING btree (token);
		CREATE INDEX client_customer_token_publish_key ON client_customer_token USING btree (publish);
		";
		
		return $sql;
	}
	
	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {
		if (array_key_exists('client_customer_token', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['client_customer_token'];
		else $conf = array();
		
		return $conf;
	}

	/**
	 * Returns id for given token value
	 */
	
	function getIdForToken($token)
	{
		if (strlen($token) != 32) return false;
		$token_escaped = pg_escape_string($token);

		$result = $this->listing("token = '$token_escaped'");

		if ($result[0]['id'] > 0) return $result[0]['id'];

		return false;
		
	}

	/**
	 * Returns token detail for given token value
	 */
	
	function getTokenDetail($token)
	{
		if (strlen($token) != 32) return false;
		$token_escaped = pg_escape_string($token);

		return $this->listing("token = '$token_escaped'");
	}
	
	/**
	 * Returns customer detail if given token exists and is published
	 * otherwise return false
	 */
	
	function getCustomerDetailForToken($token)
	{
		if (strlen($token) != 32) return false;
		$token_escaped = pg_escape_string($token);

		$result = $this->listing("token = '$token_escaped' AND publish = 1");

		if ($result[0]['customer_id'] > 0) {
			require_once('models/client/client_customer.php');
			$Customer = new client_customer();
			$Customer->setCacheable(false);
			$customer_detail = $Customer->detail($result[0]['customer_id']);
			return $customer_detail;
		}

		return false;
		
	}

	/**
	 * Inserts new random token into database. Returns token value.
	 * 
	 * @param  int    $customer_id Customer
	 * @return String              Generated token value
	 */
	function generateToken($customer_id, $ttl = 0)
	{
		$token = '';
		$alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCEDFGHIJKLMNOPQRSTUVWXYZ';
		for ($i = 0; $i < 32; $i++) $token .= substr($alphabet, rand(0, strlen($alphabet) - 1), 1);

		$data = array(
			'customer_id' => $customer_id,
			'publish' => 1,
			'token' => $token,
			'oauth_data' => null,
			'other_data' => null,
			'ttl' => $ttl,
			'ip_address' => $_SERVER['REMOTE_ADDR'],
			'http_user_agent' => $_SERVER['HTTP_USER_AGENT'],
			'created' => date('c'),
			'modified' => date('c')
		);

		$this->insert($data);

		return $token;
	}

	/**
	 * Invalidate given token by seting publish to 0
	 * @param  String $token Token value
	 */
	function invalidateToken($token)
	{
		if (strlen($token) != 32) return false;
		$token_escaped = pg_escape_string($token);

		$sql = "UPDATE client_customer_token SET publish = 0 WHERE token = '$token'";
		$this->executeSql($sql);
	}

}
