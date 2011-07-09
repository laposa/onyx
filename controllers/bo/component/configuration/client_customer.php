<?php
/**
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/component/configuration.php');

class Onxshop_Controller_Bo_Component_Configuration_Client_Customer extends Onxshop_Controller_Bo_Component_Configuration {
	
	/**
	 * custom action
	 */
	 
	public function customAction($conf) {
	
		//specifix to "client_customer" object
		$selected[$conf['global']['login_type']] = "selected='selected'";
		$this->tpl->assign("SELECTED", $selected);
		
		return $conf;
	
	}
	
}
