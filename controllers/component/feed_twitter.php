<?php
/** 
 * Copyright (c) 2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/component/feed.php');

class Onxshop_Controller_Component_Feed_Twitter extends Onxshop_Controller_Component_Feed {

	/**
	 * prepare item
	 */
	 
	public function prepareItem($item) {
	
		$item['title'] = preg_replace('/\w*: /', '', $item['title']);
		$item['content'] = preg_replace('/\w*: /', '', $item['content']);
		
		return $item;
	}
}
