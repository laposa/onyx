<?php
/** 
 * Copyright (c) 2006-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Uri_Mapping extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		/**
		 * check main domain
		 */
	
		if (defined('ONXSHOP_MAIN_DOMAIN')) {
			if (array_key_exists('HTTPS', $_SERVER)) $protocol = 'https';
			else $protocol = 'http';
			
			if ($_SERVER['HTTP_HOST'] != ONXSHOP_MAIN_DOMAIN) {
			    Header( "HTTP/1.1 301 Moved Permanently" );
			    Header( "Location: $protocol://" . ONXSHOP_MAIN_DOMAIN . "{$_SERVER['REQUEST_URI']}" );
			    //exit the application immediately 
			    exit;
			}
		}

		/**
		 * input data
		 */
		
		$translate = trim($this->GET['translate']);
		if ($translate != "/") $translate = rtrim($translate, '/');
		
		if ($this->GET['controller_request']) $controller_request = trim($this->GET['controller_request']);
		
		/**
		 * initialize
		 */
		 
		require_once('models/common/common_uri_mapping.php');
		$this->Mapper = new common_uri_mapping();
		
		/**
		 * translate request to $action_to_process
		 */
		 
		if ($translate) {
			
			if (is_numeric($node_id = trim($translate, '/'))) { // URL like /1234
				
				/**
				 * short URL redirects
				 * TODO: allow to pass GET parameters
				 */
				 
				$this->redirectToSeoURLAndExit($node_id);
				
			} else if (preg_match('/^\/\b(page|node)\b\/([0-9]*)$/', $translate, $match)) { // URL like /page/1234 or /node/1234
				
				$mapped_node_id = $match[2];
				$action_to_process = $this->getActionToProcessForExistingPage($mapped_node_id);
				
			} else if ($mapped_node_id = $this->Mapper->translate($translate)) { // URL like /abc-cbs
			
				$action_to_process = $this->getActionToProcessForExistingPage($mapped_node_id);
				
			} else if ($redirect_uri = $this->Mapper->getRedirectURI($translate)) { // URL like /abc-cbs
				
				/**
				 * explicit redirects
				 */
				
				$this->redirectToSeoURLAndExit($redirect_uri['node_id']);
			
			} else if ($translate == '/home') {
				
				$action_to_process = $this->getActionToProcessForExistingPage($this->Mapper->conf['homepage_id']);
				
			} else {
				
				/**
				 * page not found
				 */
				
				msg("{$translate} not found! (linked from {$_SERVER['HTTP_REFERER']})", 'error');
				 
				$action_to_process = $this->Mapper->getRequest($this->Mapper->conf['404_id']);
		
				$this->http_status = '404';
					
			}
			
			
		} else if ($controller_request) {
		
			// used for /request/ and /api/ handling to allow translating URLs
			$action_to_process = $controller_request;
		
		}
		
		/**
		 * process
		 */
		
		if ($action_to_process) {
		
			$page_data = $this->processMappedAction($action_to_process);
					
			/**
			 * URI mapping iself will become output of mapped page
			 */
			 
			$this->content = $page_data['content'];

		} else {
			
			msg("Cannot find action to process", 'error');
			
		}
		
		return true;
	}
	
	/**
	 * redirectToSeoURL
	 */
	
	public function redirectToSeoURLAndExit($node_id) {
		
		if (!is_numeric($node_id)) return false;
		
		$seo_redirect_uri = $this->Mapper->stringToSeoUrl("/page/{$node_id}");
		header("Location: $seo_redirect_uri", true, 301);
		exit;
	}
	
	/**
	 * getActionToProcessForExistingPage
	 */
	 
	public function getActionToProcessForExistingPage($node_id) {
		
		if (!is_numeric($node_id)) return false;
		
		//save node_id to last record in history
		$_SESSION['orig'] = "/page/$node_id";
		$_SESSION['history'][count($_SESSION['history'])-1]['node_id'] = $node_id;
		
		$action_to_process = $this->Mapper->getRequest($node_id);
		
		return $action_to_process;
	}
	 
	/**
	 * processMappedAction
	 */
	 
	public function processMappedAction($action_to_process) {
		
		/**
		 * process action
		 */
		
		$Onxshop_Router = new Onxshop_Router();
		
		$Onxshop = $Onxshop_Router->processAction($action_to_process);
		
		if (is_object($Onxshop)) $page_data['content'] = $Onxshop->getContent();
		
		if ($page_data['content'] == "") $page_data['content'] = $this->content;
		
		$page_data['content'] = $this->Mapper->system_uri2public_uri($page_data['content']);

		/**
		 * CDN rewrites for URLs (a.k.a. output filter)
		 */
		 
		if (ONXSHOP_CDN && (ONXSHOP_CDN_USE_WHEN_SSL || !isset($_SERVER['HTTPS']))) {
			require_once('lib/onxshop.cdn.php');
			$CDN = new Onxshop_Cdn();
			$page_data['content'] = $CDN->processOutputHtml($page_data['content']);
		}

		return $page_data;
	}

}	
	
