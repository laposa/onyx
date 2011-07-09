<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Uri_Mapping extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		if ($this->GET['translate'] != "/") $this->GET['translate'] = rtrim($this->GET['translate'], '/');
		
		if ($this->GET['generate'] == 1) $update = 1;
		else $update = 0;
		
		require_once('models/common/common_uri_mapping.php');
		$Mapper = new common_uri_mapping($update);

		$Onxshop_Router = new Onxshop_Router();
		
		if ($this->GET['translate']) {
			
			$request = $Mapper->translate($this->GET['translate']);
			
			if ($request == '' || $request == '/home') $request = "/page/" . $Mapper->conf['homepage_id'] ;
			
			$_SESSION['orig'] = $request;
			msg("uri_mapping: Orig=" . $_SESSION['orig'], 'ok', 3);
			
			$request = explode('/', $request);
			
			if ($request[1] == 'node' || $request[1] == 'page') {
			
				$node_id = intval($request[2]);
				//save node_id to last record in history
				$_SESSION['history'][count($_SESSION['history'])-1]['node_id'] = $node_id;
			
			} else if ($redirect_uri = $Mapper->getRedirectURI($this->GET['translate'])) {
			
				$seo_redirect_uri = $Mapper->stringToSeoUrl("/page/{$redirect_uri['node_id']}");
				header("Location: $seo_redirect_uri", true, 301);
				exit;
			
			} else {
			
				$node_id = false;
				msg("{$this->GET['translate']} not found! (linked from {$_SERVER['HTTP_REFERER']})", 'error');
			
			}
			
			if ($node_id > 0 && is_numeric($node_id)) {
			
				$r = $Mapper->getRequest($node_id);
				
				$Onxshop = $Onxshop_Router->processAction($r);
				
			} else {
			
				$r = $Mapper->getRequest($Mapper->conf['404_id']);
		
				$Onxshop = $Onxshop_Router->processAction($r);
			
				$this->http_status = '404';
			}
		} else if ($this->GET['page']) {
		
			// is this case still used?
			//yes, for logout :)
			echo "uri_mapping"; exit;
		
		} else if ($this->GET['controller_request']) {
		
			// used for /request/ handling to allow translating URLs
			$Onxshop = $Onxshop_Router->processAction(trim($this->GET['controller_request']));
		
		}
		
		
		if (is_object($Onxshop)) $page_data['content'] = $Onxshop->getContent();
		
		if ($page_data['content'] == "") $page_data['content'] = $this->content;
		
		$page_data['content'] = $Mapper->system_uri2public_uri($page_data['content']);
		
		$this->content = $page_data['content'];

		return true;
	}
}	
	
