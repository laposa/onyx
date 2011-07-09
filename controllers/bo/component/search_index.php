<?php
/**
 * Zend Search Index
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('Zend/Http/Client.php');
require_once('Zend/Search/Lucene.php'); 
require_once('Zend/Search/Lucene/Document/Html.php'); 

class Onxshop_Controller_Bo_Component_Search_Index extends Onxshop_Controller {	
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		$index_info = $this->getIndexInfo();

		$this->tpl->assign("INDEX_INFO", $index_info);
		
		//$this->updateDocument("/faq");
		return true;
		
	}
	
	/**
	 * get profile
	 */
	
	public function getProfile() {
		$profile = array(
			'uri'           => "http://{$_SERVER['SERVER_NAME']}/",
			'path'          => ONXSHOP_PROJECT_DIR . 'var/index/',
		);
		
		return $profile;
	}
	
	/**
	 * open index
	 */
	
	public function openIndex() {
		
		$profile = $this->getProfile();
		
		// Open existing index
		try {
			$index = Zend_Search_Lucene::open($profile['path']);
		} catch(Exception $e) {
			msg("cannot open index", 'error');
			$index = false;
		}
		
		return $index;
	
	}
	
	/**
	 * get index info
	 */
	 
	public function getIndexInfo() {
		
		$index = $this->openIndex();
		
		if (is_object($index)) {
			$info['indexSize'] = $index->count();
			$info['documents'] = $index->numDocs();
		} else {
			$info = array();
		}
		
		return $info;
	}
	
	/**
	 * optimize index
	 */
	
	public function indexOptimize() {
		$index = $this->openIndex();

		// Optimize index.
		$index->optimize();
	}
	
	/**
	 * update document
	 */
	
	public function updateDocument($uri) {
	
		$index = $this->openIndex();
		
		$hits = $index->find('uri:' . $uri);
		
		foreach ($hits as $hit) {
   		 	msg($hit->id . $hit->uri);
   		 	//$index->delete($hit->id);
		}
	}
	
}
