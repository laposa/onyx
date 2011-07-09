<?php
require_once('models/common/common_file.php');

/**
 * class common_print_article
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class common_print_article extends common_file {

	/**
	 * @access private
	 */
	var $type;
	/**
	 * @access private
	 */
	var $authors;
	/**
	 * @access private
	 */
	var $issue_number;
	/**
	 * @access private
	 */
	var $page_from;
	/**
	 * @access private
	 */
	var $date;
	/**
	 * @access private
	 * 
	 * serialized other attributes, ie JTCM_date, JTCM_issue, JTCM_page_from
	 * 
	 */
	var $other;
	
	
	/*
	 * NOTE: we need whole _hashMap
	 */
	var $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'src'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'role'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'node_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'title'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'description'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'priority'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'author'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'type'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'authors'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'issue_number'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'page_from'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'date'=>array('label' => '', 'validation'=>'date', 'required'=>true),
		'other'=>array('label' => '', 'validation'=>'string', 'required'=>false)
	);
	
	/**
	 * get issues
	 */

	function getIssues() {
	
		$sql = "SELECT issue_number, date FROM common_print_article GROUP BY issue_number, date ORDER BY issue_number DESC";

		if ($records = $this->executeSql($sql)) {
			return $records;
		} else {
			return false;
		}
	}
}
