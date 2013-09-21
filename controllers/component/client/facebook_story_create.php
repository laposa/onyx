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

		$object_name = $this->getObjectName($this->node_id);

		if (empty($this->GET['object'])) {
			$this->object = $object_name;
		}
		else {
			$this->object = $this->GET['object'];
			if ($this->object != $object_name) msg("facebook_story_create: object name '{$this->object}' " .
				" and shared node (id: {$this->node_id}) type mismatch", "error", 1);
		}

		if (!$this->validateInput()) return true;

		$this->commonAction();

		if ($this->action == 'like') $response = $this->postFacebookLike();
		else $response = $this->postFacebookAction();
			
		$this->processResponse($response);

		return true;
	}



	public function postFacebookLike()
	{
		try {

			$response = $this->Facebook->api(
				'me/og.likes', 'POST',
				array('object' => $this->getShareUri())
			);

		} catch (FacebookApiException $e) {

			// object already liked
			if (strpos($e->getMessage(), "#3501")) {
				preg_match('/Action ID: (\d+)/', $e->getMessage(), $matches);
				// open activity so user can unlike it
				header("Location: https://www.facebook.com/me/activity/{$matches[1]}/");
				exit();
			}

			msg($e->getMessage(), 'error', 1);

			return null;
		}

		return $response;
	}


	public function postFacebookAction()
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
			case 'poll':
			case 'competition':
				return $node['node_controller'];
			case 'default':
				return 'article';
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

			if ($this->GET['redirect_to_post']) {
				header("Location: https://www.facebook.com/me/activity/{$response['id']}/");
				exit();
			}

		} else {

			msg("Unable to create story on Facebook " . print_r($response, TRUE), 'error', 1);

			if ($this->GET['redirect_to_post']) {
				echo("<script>window.close()</script>");
				exit();
			}

		}
	} 	



	/**
	 * Create URL to share
	 */
	public function getShareUri()
	{
		$share_uri = "http://".$_SERVER['HTTP_HOST']."/{$this->node_id}";
		return $share_uri;
	}

}
