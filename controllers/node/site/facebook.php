<?php
/** 
 * Copyright (c) 2013-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/node/site/default.php');

class Onxshop_Controller_Node_Site_Facebook extends Onxshop_Controller_Node_Site_Default {

	/**
	 * main action
	 */
	 
	public function mainAction() {
				
		$Facebook_Auth = new Onxshop_Request('component/client/facebook_auth');
		//echo $Facebook_Auth->getContent();
		
		return parent::mainAction();
		
	}
}
