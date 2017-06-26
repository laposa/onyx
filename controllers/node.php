<?php
/** 
 * Copyright (c) 2005-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Node extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		if ($this->processNode()) return true;
		else return false;
	}
	
	/**
	 * process node
	 */
	 
	public function processNode() {
	
		/**
		 * check node id value
		 */
		 
		if (!is_numeric($this->GET['id'])) {
		
			msg("Node.processNode(): id is not numeric", 'error');
			return false;
		
		} else {
		
			$node_id = $this->GET['id'];
			
		}
		
		/**
		 * initialize
		 */
		 
		require_once('models/common/common_node.php');
		$this->Node = new common_node();
		
		$node_data = $this->Node->nodeDetail($node_id);
		
		if (!is_array($node_data)) {
			msg("Node ID {$node_id} does not exists", 'error');
			return false;
		}
		
		$source = $node_data;
		
		$this->temp['node_group'] = $node_data['node_group'];
		
		/**
		 * Initialise node configuration overwrites 
		 */

		$global_conf_node_overwrites = $this->initGlobalNodeConfigurationOverwrites($node_id );
		
		/**
		 * merge
		 */
		
		$GLOBALS['onxshop_conf'] = $this->array_replace_recursive($GLOBALS['onxshop_conf'], $global_conf_node_overwrites);
		
		/**
		 * check if the page can be viewed
		 */
		 
		if ($node_data['node_group'] == 'page' && !$this->canViewPage($node_data)) {
			// send 403 HTTP code
			http_response_code(403);
			// don't allow to save this request to the cache
			Zend_Registry::set('omit_cache', true);
			// display 404 page content
			$_Onxshop_Request = new Onxshop_Request('node~id=' . $this->Node->conf['id_map-404'].'~'); 
			$node_data['content'] = $_Onxshop_Request->getContent();
			$this->tpl->assign('NODE', $node_data);
			$this->tpl->parse('content.wrapper');
			return true;
		}
		
		/**
		 * force login
		 */
		 
		if ($node_data['require_login'] == 1 && $_SESSION['client']['customer']['id'] == 0) {
			
			$parsed_url = parse_url($_SERVER['REQUEST_URI']);
			
			if ($parsed_url['query']) $_SESSION['to'] = "page/{$node_id}?{$parsed_url['query']}";
			else $_SESSION['to'] = "page/{$node_id}";
			
			onxshopGoTo("page/" . $this->Node->conf['id_map-login']);//will exit immediatelly
		}
		
		/**
		 * force SSL
		 * this is no longer effective since Onxshop 1.7 when we force SSL for all pages
		 * when ONXSHOP_CUSTOMER_USE_SSL is set to "true"
		 * kept here for transitional period
		 */
		
		if ($node_data['require_ssl'] == 1) {
			if (!($_SERVER['SSL_PROTOCOL'] || $_SERVER['HTTPS']) && ONXSHOP_CUSTOMER_USE_SSL) {
				//don't exit in this case, just say "next time use SSL"
				header("Location: https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}");
			}
		}
		
		
		/**
		 * check if template file exists in ONXSHOP_DIR or in ONXSHOP_PROJECT_DIR
		 */
		 
		$template_path = "node/{$node_data['node_group']}/{$node_data['node_controller']}";
		
		$template_file_path = "templates/{$template_path}.html";
		
		if (file_exists(ONXSHOP_DIR . $template_file_path) || file_exists(ONXSHOP_PROJECT_DIR . $template_file_path)) {

			$template = $template_path;
			
		} else {
		
			msg("missing $template_file_path", 'error', 1);
			
			//fallback to default
			$template = "node/{$node_data['node_group']}/default";
		
		}
		
		/**
		 * check if controller exits
		 */
		 
		$controller_path = $template_path; //try the same filename as for the template
		
		$controller_file_path = "controllers/{$controller_path}.php";
		
		if (file_exists(ONXSHOP_DIR . $controller_file_path) || file_exists(ONXSHOP_PROJECT_DIR . $controller_file_path)) {

			$controller = $controller_path;
			
		} else {
		
			//fallback to default
			$controller = "node/{$node_data['node_group']}/default";
		
		}
		
		/**
		 * check if we need to use default_controller@specic_template
		 */
		 
		if ($template == $controller) $controller_request = $template;
		else $controller_request = "{$controller}@{$template}";
		
		/**
		 * process controller referred from this node
		 */
		
		msg("Node process: $controller", 'ok', 2);
		if (isset($GLOBALS['components'])) {
			$GLOBALS['components'][count($GLOBALS['components']) - 1]['node'] = $node_data['title'];
		}

		$_Onxshop_Request = new Onxshop_Request("$controller_request&id={$node_data['id']}&parent_id={$node_data['parent']}");
		$node_data['content'] = $_Onxshop_Request->getContent();
		
		/**
		 * add extra_css_class
		 */
		 
		$node_data['extra_css_class'] = '';
		
		/*
		if (trim($node_data['css_class']) != '') {
			
			$css_classes = explode(" ", $node_data['css_class']);
			foreach ($css_classes as $css_class_item) {
				$node_data['extra_css_class'] .= "node-$css_class_item ";
			}
			
		}
		*/
		/**
		 * check visibility and than display
		 */
		
		if ($node_visibility = $this->checkVisibility($node_data)) {
			
			if ($this->_checkPermissionForExtraCSS($node_data)) {
			
				//TODO: add and icon with status
				// we cannot add this css_class to normal node.css_class, because of inheritance
				if ($node_data['display_permission'] == 1) $node_data['extra_css_class'] = $node_data['extra_css_class'] . ' show-at-normal-login';
				else if ($node_data['display_permission'] == 2) $node_data['extra_css_class'] = $node_data['extra_css_class'] . ' hide-at-normal-login';
				else if ($node_data['display_permission'] == 3) $node_data['extra_css_class'] = $node_data['extra_css_class'] . ' show-at-trade-login';
				else if ($node_data['display_permission'] == 4) $node_data['extra_css_class'] = $node_data['extra_css_class'] . ' hide-at-trade-login';
				if (is_array($node_data['display_permission_group_acl'])) $node_data['extra_css_class'] = $node_data['extra_css_class'] . ' acl-in-use';
				if ($node_data['publish'] == 0) $node_data['extra_css_class'] = $node_data['extra_css_class'] . ' not-public';
				if ($node_data['node_controller'] == 'adaptive') $node_data['extra_css_class'] = $node_data['extra_css_class'] . ' adaptive';
			}
			
			$this->tpl->assign("NODE", $node_data);
		
		}
		
		
		
		/**
		 * front-end node edit and node move (sort) icons
		 * node add are inserted in controller/node/default
		 */
		 
		// don't show edit icons when shared parameter is 1 (passed from shared content)
		// and not authenticated for the backend
		if ($this->GET['shared'] == 0 && Onxshop_Bo_Authentication::getInstance()->isAuthenticated()) {
    		
    		if ($node_data['node_group'] == 'variable' && $_SESSION['fe_edit_mode'] == 'edit') {
				$this->tpl->parse('content.wrapper.variable_edit');
			} else if ($node_data['node_group'] == 'content' && $_SESSION['fe_edit_mode'] == 'edit') {
				if ($node_data['node_controller'] == 'shared')  {
					$this->tpl->assign("SOURCE", $source);
					$this->tpl->parse('content.wrapper.fe_edit.edit_source');
				}
				$this->tpl->parse('content.wrapper.fe_edit');
			} else if ($node_data['node_group'] == 'layout' && $_SESSION['fe_edit_mode'] == 'edit') {
				if ($node_data['node_controller'] == 'shared')  {
					$this->tpl->parse('content.wrapper.fe_layout_property.edit_shared');
				}
				$this->tpl->parse('content.wrapper.fe_layout_property');
				//$this->tpl->parse('content.wrapper.layout_add');
			} else if ($node_data['node_group'] == 'page' && $_SESSION['fe_edit_mode'] == 'edit') {
				$this->tpl->parse('content.wrapper.fe_page_properties');
			}
			
			// show extra wrappers when in edit or sorting mode
			if ($_SESSION['fe_edit_mode'] == 'edit' || $_SESSION['fe_edit_mode'] == 'move') {
    			
    			// node_group variable doesn't have any wrapper
    			if ($node_data['node_group'] !== 'variable') {
				    $this->tpl->parse('content.wrapper.backend_wrapper_before');
                    $this->tpl->parse('content.wrapper.backend_wrapper_after');
				}
			}
		}

		
		if ($node_visibility) $this->tpl->parse('content.wrapper');
		
		return true;
		
	}
	
	/**
	 * checkVisibility
	 */
	 
	public function checkVisibility($node_data) {
	
		$visibility = false;
		//force visibility for admin, only when in edit or preview mode
		if ($_SESSION['fe_edit_mode'] == 'edit' || $_SESSION['fe_edit_mode'] == 'move') $force_admin_visibility = true;
		else $force_admin_visibility = false;
		
		/**
		 * CONDITIONAL DISPLAY OPTION BY display_permission
		 */
		 
		if ($this->Node->checkDisplayPermission($node_data, $force_admin_visibility)) {
			
			//don't display hidden node in preview mode
			if ($node_data['publish'] == 0 && Onxshop_Bo_Authentication::getInstance()->isAuthenticated() && $_SESSION['fe_edit_mode'] == 'preview' ) $visibility1 = false;
			else $visibility1 = true;
		}
		
		/**
		 * check permission from group_acl
		 */
		 
		if ($this->Node->checkDisplayPermissionGroupAcl($node_data, $force_admin_visibility)) {
			$visibility2 = true;
		} else {
			$visibility2 = false;
		}
		
		/**
		 * visible only if preview token is provided and all permissions are correct
		 */
		 
		if ($this->checkForValidPreviewToken($node_data)) return true;
		else if ($visibility1 && $visibility2) return true;
		else return false;
	
	}
	
	/**
	 * check if add CSS highlight
	 */
	 
	public function _checkPermissionForExtraCSS($node_data) {
	
		//add css class when when logged in and using edit or move mode
		if (Onxshop_Bo_Authentication::getInstance()->isAuthenticated() && ($_SESSION['fe_edit_mode'] == 'edit' || $_SESSION['fe_edit_mode'] == 'move')) return true;
		else return false;
		
	}
	
	/**
	 * Initialise configuration overwrites from database
	 */
	 
	function initGlobalNodeConfigurationOverwrites($node_id) {
	
		if (!is_numeric($node_id)) return false;
		
		$conf = array();

		require_once ('models/common/common_configuration.php');
		$Configuration = new common_configuration();
		
		$conf = $Configuration->getConfiguration($node_id);
		
		return $conf;
	}
	
	/**
	 * canViewPage
	 * check if page is published, but keep it available in edit mode
	 * and allow to see when provided GET.preview_token
	 */
	 
	public function canViewPage($node_data) {
		
		if ($this->checkForValidPreviewToken($node_data)) {
			msg("This page is waiting for approval");
			return true;
		} else if (Onxshop_Bo_Authentication::getInstance()->isAuthenticated()) {
			return true;
		} else if ($this->Node->isInBin($node_data['id'])) {
			return false;
		} else {
			return $node_data['publish'];
		}
	}
	
	/**
	 * checkForValidPreviewToken
	 */
	 
	private function checkForValidPreviewToken($node_data) {
		
		// currently applies only to pages
		if ($node_data['node_group'] != 'page') return;
		
		if (array_key_exists('preview_token', $_GET)) {
			
			if ($this->Node->verifyPreviewToken($node_data, $_GET['preview_token'])) {
			
				return true;
			
			} else {
			
				msg("Invalid preview_token", 'error');
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * merge array with overwrites (for local configuration overwrites)
	 * TEMP: native array_replace_recursive function available in PHP 5.3
	 */
	 
	function array_replace_recursive($Arr1, $Arr2) {
	
		foreach($Arr2 as $key => $Value) {
			
			if(array_key_exists($key, $Arr1) && is_array($Value)) $Arr1[$key] = $this->array_replace_recursive($Arr1[$key], $Arr2[$key]);
			else $Arr1[$key] = $Value;
	
		}
		
		return $Arr1;
		
	}
	
}
