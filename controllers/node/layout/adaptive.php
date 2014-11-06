<?php
/**
 * Copyright (c) 2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/node/layout/default.php');

class Onxshop_Controller_Node_Layout_Adaptive extends Onxshop_Controller_Node_Layout_Default {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * check input: node id value must be numeric
		 */
		 
		if (!is_numeric($this->GET['id'])) {
			msg("node/content/adaptive: id not numeric", 'error');
			return false;
		}

		$node_id = $this->GET['id'];
		$this->loadNode($node_id);

		if ($this->canDisplay()) {
		 
			$this->processContainers($node_id);
			$this->processLayout();

			$this->tpl->parse("content.subcontent");

		}

		return true;
	}

	/**
	 * can display adaptive content as per configured conditions?
	 */

	public function canDisplay() {

		$condition = $this->node_data['component']['condition'];

		//force visibility for admin, only when in edit or preview mode
		if ($_SESSION['fe_edit_mode'] == 'edit' || $_SESSION['fe_edit_mode'] == 'move') return true;
		else $force_admin_visibility = false;

		switch ($condition) {

			case "always":
				return true;

			case "customer_returning":
				return $this->isReturningCustomer();

			case "customer_new":
				return !$this->isReturningCustomer();

			case "customer_newsletter_subscribed":
				return $this->isSubscribed();
	
			case "customer_newsletter_not_subscribed":
				return !$this->isSubscribed();

		}

		msg("node/content/adaptive: unknown display condition '$condition'", 'error');
		return false;

	}

	/**
	 * is current user a returning customer?
	 */

	public function isReturningCustomer() {

		$period = 24 * 3600; // 24-hours
		$logged_in = ($_SESSION['client']['customer']['id'] > 0);
		$account_is_old = (time() - strtotime($_SESSION['client']['customer']['created']) > $period);

		$cookie_status = ($_COOKIE['visited_status'] > 0 && time() - $_COOKIE['visited_status'] > $period);

		return $logged_in && $account_is_old || $cookie_status;
	}

	/**
	 * is current user subscribed to newletter?
	 */
	
	public function isSubscribed() {

		$logged_in = ($_SESSION['client']['customer']['id'] > 0);
		$customer_newsletter = ($_SESSION['client']['customer']['newsletter'] > 0);

		$cookie_status = ($_COOKIE['newletter_status'] & 1 == 1);

		return $logged_in && $customer_newsletter || $cookie_status;
	}

	/**
	 * load node data
	 */
	public function loadNode($node_id) {

		require_once('models/common/common_node.php');
		$this->Node = new common_node();
		$this->node_data = $this->Node->nodeDetail($node_id);

	}

	/**
	 * process containers
	 */
	
	public function processContainers($node_id) {
	
		//find child nodes
		$contentx = $this->Node->parseChildren($node_id);
		
		//assign to this controller as CONTAINER variable
		if (is_array($contentx)) {
			foreach ($contentx as $content) {
				$container[$content['container']] .= $content['content'];
			}
		}
		
		/**
		 * node add icons
		 * front-end node edit and node move (sort) icons are inserted in controller/node
		 *  
		 */
		 
		if ($_SESSION['fe_edit_mode'] == 'edit' || $_SESSION['fe_edit_mode'] == 'move') {
			//normally we support container.0 to container.6 in default templates, but why not to have some reserve, e.g. 20
			$min_container_id = 0;
			$max_container_id = 20;
			for ($key = $min_container_id; $key < ($max_container_id + 1); $key++) {
				$container[$key] = "<div class='onxshop_layout_container' id='onxshop_layout_container_{$node_id}_{$key}'>{$container[$key]}</div>";	
			}
		}
			
		$this->tpl->assign("CONTAINER", $container);
		$this->tpl->assign("NODE", $node_data);	
	}

	/**
	 * process layout
	 */
	 
	public function processLayout() {
		
		if ($this->node_data['page_title'] == '') {
			$this->node_data['page_title'] = $this->node_data['title'];
		}
		
		if (!isset($this->node_data['display_title'])) $this->node_data['display_title'] = $GLOBALS['onxshop_conf']['global']['display_title'];
		
		$this->tpl->assign("NODE", $this->node_data);
		
		/**
		 * display title
		 */
		 
		$this->displayTitle($this->node_data);
		
	}

}