<?php
/**
 * GetConnect Sage Export
 * 
 * Copyright (c) 2007-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Get_Connect extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('lib/getConnect.class.php');
		
		//set the output header
		header('Content-Type: text/xml;charset=utf-8');
		
		$GetConnect = new GetConnect($Conn);
		
		
		// Check to see if xml has been posted to this script	
		if (!empty($_POST)) {
			$GetConnect->setFeedBack($_POST);
		} else {
			//cleant status
			$GetConnect->cleanStatus();
			
			// process customers
			//$GetConnect->getCustomers();
			$GetConnect->getVirtualCurrencyCustomers();
			
			// process orders
			$GetConnect->getInvoices();
			
			// process orders
			//$GetConnect->getSalesOrders();
		
		}
		
		
		$output = smart_utf8_decode($GetConnect->getOutput());
		//$xmlDoc = strtr(iconv('ISO-8859-1', 'UTF-8', $xmlDoc), array('', ''), array('', ''));
		
		echo $output;

		return true;
	}
}
