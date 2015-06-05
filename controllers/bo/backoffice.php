<?php
/** 
 * Copyright (c) 2006-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Backoffice extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		//force SSL
		if (!$_SERVER['HTTPS'] && ONXSHOP_EDITOR_USE_SSL) {
			header("Location: https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}");
		}

		return true;
	}
}
