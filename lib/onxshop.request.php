<?php
/**
 * Copyright (c) 2005-2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

/**
 * Factory for creating new controller using request URI
 */
class Onxshop_Request {

	/**
	 * Construct
	 */

	public function __construct($request, &$subOnxshop = false)
	{
		$this->Onxshop = Onxshop_Controller::createController($request, $subOnxshop);
	}

	public function getContent()
	{
		return $this->Onxshop->getContent();
	}
}

/**
 * compatibility nSite class
 */
 
class nSite extends Onxshop_Request {
	
}
