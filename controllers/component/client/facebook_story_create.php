<?php
/** 
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once 'controllers/component/client/facebook.php';

class Onxshop_Controller_Component_Client_Facebook_Story_Create extends Onxshop_Controller_Component_Client_Facebook {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		$this->node_id = $this->GET['node_id'];
		$this->action = $this->GET['action'];
		if (empty($this->GET['object'])) $this->object = $this->getObjectName($this->node_id);
		else $this->object = $this->GET['object'];

		if (!$this->validateInput()) return true;

		$this->commonAction();
		$response = $this->makeFacebookPost();
		$this->processResponse($response);

		return true;
	}



	public function makeFacebookPost()
	{
		$response = $this->makeApiCall(
			'me/' . ONXSHOP_FACEBOOK_APP_NAMESPACE . ":" . $this->action, 'POST',
			array($this->object => $this->getShareUri())
		);
		
		return $response;
	}



	/**
	 * Get object name according to page type
	 */
	public function getObjectName($node_id)
	{
		require_once('models/common/common_node.php');
		$this->Node = new common_node();
		$node = $this->Node->detail($node_id);

		if ($node['node_group'] != 'page') return false;

		switch ($node['node_controller']) {
			case 'product':
			case 'store':
			case 'recipe':
			case 'article':
				return $node['node_controller'];
		}

		return false;
	}



	/**
	 * Validate input date
	 */
	public function validateInput() {

		if (!is_numeric($this->node_id)) {
			msg("facebook_story_create: node_id is not numeric", 'error', 1);
			return false;
		}

		if (empty($this->action)) {
			msg("facebook_story_create: action is missing", 'error', 1);
			return false;
		}

		if (!$this->object) {
			msg("facebook_story_create: invalid object", 'error', 1);
			return false;
		}

		return true;
	}



	/**
	 * Process the response from facebook and save action to local database
	 */
	public function processResponse($response)
	{
		if (isset($response['id'])) {

			msg("Story created with id = {$response['id']}", 'ok', 1);

			$request = new Onxshop_Request("component/client/action_add~" 
				. "node_id=" . $this->node_id
				. ":action_id=" . $response['id']
				. ":network=facebook"
				. ":action_name=" . $this->action
				. ":object_name=" . $this->object
				. "~");

		} else {
			msg("Unable to create story on Facebook " . print_r($response, TRUE), 'error', 1);
		}
	} 	



	/**
	 * Create URL to share
	 */
	public function getShareUri()
	{
		$share_uri = "http://".$_SERVER['HTTP_HOST']."/page/{$this->node_id}";
		return $share_uri;
	}

}
