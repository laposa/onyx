<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
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
		
		$node_conf = common_node::initConfiguration();
		
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
		 * check permission
		 */
		 
		if ($node_data['publish'] == 0 && ($node_data['node_group'] == 'page' || $node_data['node_group'] == 'news') && $_SESSION['authentication']['authenticity'] < 1) {
			msg("Unauthorized access to {$this->request}", 'error', 2);
			onxshopGoTo(ONXSHOP_DEFAULT_LAYOUT . '.' . ONXSHOP_PAGE_TEMPLATE . '.sys/401', 1);//will exit immediatelly
		}
		
		/**
		 * force login
		 */
		 
		if ($node_data['require_login'] == 1 && $_SESSION['client']['customer']['id'] == 0) {
			//msg('You must be logged in first.');
			$_SESSION['to'] = "page/{$node_id}";
			onxshopGoTo("page/" . $node_conf['id_map-login']);//will exit immediatelly
		}
		
		/**
		 * force SSL
		 */
		
		if ($node_data['require_ssl'] == 1) {
			if (!($_SERVER['SSL_PROTOCOL'] || $_SERVER['HTTPS']) && ONXSHOP_CUSTOMER_USE_SSL) {
				//don't exit in this case, just say "next time use SSL"
				header("Location: https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}");
			}
		}
		
		/**
		 * process subcontroller
		 */
		 
		if (file_exists(ONXSHOP_DIR . "templates/node/{$node_data['node_group']}/{$node_data['node_controller']}.html")) {

			$controller = "node/{$node_data['node_group']}/{$node_data['node_controller']}";
			
		} else {
			$controller = "node/default";
		}
		
		msg("Node process: $controller", 'ok', 2);
		$_nSite = new nSite("$controller&id={$node_data['id']}&parent_id={$node_data['parent']}");
		$node_data['content'] = $_nSite->getContent();
		
		/**
		 * Substitute constants in the output for logged in users
		 * TODO: highlight in documentation!
		 */
		
		if ($_SESSION['client']['customer']['id'] > 0) {
			$node_data['content'] = preg_replace("/{CUSTOMER_FIRST_NAME}/", $_SESSION['client']['customer']['first_name'], $node_data['content']);
			$node_data['content'] = preg_replace("/{CUSTOMER_LAST_NAME}/", $_SESSION['client']['customer']['last_name'], $node_data['content']);
			$node_data['content'] = preg_replace("/{CUSTOMER_EMAIL}/", $_SESSION['client']['customer']['email'], $node_data['content']);
		}
		
		/**
		 * check visibility and than display
		 */
		
		if ($this->checkVisibility($node_data)) {
			
			if ($this->_checkPermissionForExtraCSS($node_data)) {
			
				//TODO: add and icon with status
				// we cannot add this css_class to normal node.css_class, because of inheritance
				if ($node_data['display_permission'] == 1) $node_data['extra_css_class'] = $node_data['extra_css_class'] . ' show_at_normal_login';
				else if ($node_data['display_permission'] == 2) $node_data['extra_css_class'] = $node_data['extra_css_class'] . ' hide_at_normal_login';
				else if ($node_data['display_permission'] == 3) $node_data['extra_css_class'] = $node_data['extra_css_class'] . ' show_at_trade_login';
				else if ($node_data['display_permission'] == 4) $node_data['extra_css_class'] = $node_data['extra_css_class'] . ' hide_at_trade_login';
				if (is_array($node_data['display_permission_group_acl'])) $node_data['extra_css_class'] = $node_data['extra_css_class'] . ' acl_in_use';
				if ($node_data['publish'] == 0) $node_data['extra_css_class'] = $node_data['extra_css_class'] . ' not_public';
			}
			
			$this->tpl->assign("NODE", $node_data);
		
		}
		
		
		
		/**
		 * front-end node edit and node move (sort) icons
		 * node add are inserted in controller/node/default
		 */
		 
		// don't show edit icons when shared parameter is 1 (passed from shared content)
		// and not authenticated for the backend
		if ($this->GET['shared'] == 0 && $_SESSION['authentication']['authenticity'] > 0) {
			if ($node_data['node_group'] == 'content' && $_SESSION['fe_edit_mode'] == 'edit') {
				if ($node_data['node_controller'] == 'shared')  {
					$this->tpl->assign("SOURCE", $source);
					$this->tpl->parse('content.fe_edit.edit_source');
				}
				$this->tpl->parse('content.fe_edit');
			} else if ($node_data['node_group'] == 'layout' && $_SESSION['fe_edit_mode'] == 'edit') {
				if ($node_data['node_controller'] == 'shared')  {
					$this->tpl->parse('content.fe_layout_property.edit_shared');
				}
				$this->tpl->parse('content.fe_layout_property');
				//$this->tpl->parse('content.layout_add');
			} else if ($node_data['node_group'] == 'page' && $_SESSION['fe_edit_mode'] == 'edit') {
				$this->tpl->parse('content.fe_page_properties');
			}
		}

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
			if ($node_data['publish'] == 0 && $_SESSION['authentication']['authenticity'] > 0 && $_SESSION['fe_edit_mode'] == 'preview' ) $visibility1 = false;
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
		
		if ($visibility1 && $visibility2) return true;
		else return false;
	
	}
	
	/**
	 * check if add CSS highlight
	 */
	 
	public function _checkPermissionForExtraCSS($node_data) {
	
		//add css class when when logged in and using edit or move mode
		if ($_SESSION['authentication']['authenticity'] > 0 && ($_SESSION['fe_edit_mode'] == 'edit' || $_SESSION['fe_edit_mode'] == 'move')) return true;
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
