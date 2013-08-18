<?php
require_once('models/common/common_session.php');

/**
 * class common_session_archive
 *
 * Copyright (c) 2009-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class common_session_archive extends common_session {

	//session_id can be repeated in this table (don't use UNIQUE constraint)
	
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE common_session_archive ( 
	id serial NOT NULL PRIMARY KEY,
	session_id varchar(32) ,
	session_data text ,
	customer_id int REFERENCES client_customer ON UPDATE CASCADE ON DELETE CASCADE, 
	created timestamp(0) without time zone,
	modified timestamp(0) without time zone,
	ip_address varchar(255),
	php_auth_user varchar(255),
	http_referer text,
	http_user_agent varchar(255)
);
		";
		
		return $sql;
	}
	
	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {
		if (array_key_exists('common_session_archive', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['common_session_archive'];
		else $conf = array();
		
		//time to live of anonymouse users: 1 week
		$conf['keep_anonymouse'] = false;
	
		return $conf;
	}
	
	/**
	 * insert session
	 */
	 
	function insertSession($session) {
		
	 	if ($this->conf['keep_anonymouse'] == true) {
        	//archive all
        	$id = $this->insert($session);
        } else {
        	//archive only users and editors sessions
        	if ($session['customer_id'] > 0 || $session['php_auth_user'] != '') {
        		$id = $this->insert($session);
        	}
        }
        
        return $id;
	}

	/**
	 * This function can be useful for garbage collection
	 * if we keep in archive all the session
	 * 
	 * @param int max_lifetime 	 * @return 
	 * @access public
	 */
	 
	function deleteAnonymouse( $max_lifetime = 604800 ) {
	
		//DELETE FROM `common_session_archive` WHERE `customer_id` =0 AND `php_auth_user` = ''
		/*
		ASHMORE EXAMPLE
		2594archived-2186not users= 408users log
		3.2MB size of archived in one week
		28.8MB of users log/year
		*/
        $real_now = time();

        $dt1 = $real_now - $max_lifetime;

        $dt2 = date('Y-m-d H:i:s', $dt1);
		
        
        //delete them from common_session_archive table
		$q = "DELETE FROM common_session_archive WHERE modified < '$dt2' AND customer_id = 0 AND php_auth_user = ''";
        $this->executeSql($q);
        //echo($q);
        
        return true;

	}
}
