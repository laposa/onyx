<?php
/** 
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once 'models/client/client_action.php';

class Onxshop_Controller_Component_Client_Action_Add extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		$this->customer_id = (int) $_SESSION['client']['customer']['id'];

		if (is_numeric($this->GET['node_id']) 
			&& !empty($this->GET['action_id'])
			&& !empty($this->GET['network'])
			&& !empty($this->GET['action_name'])
			&& !empty($this->GET['object_name'])
			&& $this->customer_id > 0) {

			$Actions = new client_action();
			$Actions->insert(array(
				'customer_id' => $this->customer_id,
				'node_id' => $this->GET['node_id'],
				'action_id' => $this->GET['action_id'],
				'network' => $this->GET['network'],
				'action_name' => $this->GET['action_name'],
				'object_name' => $this->GET['object_name'],
				'created' => date("c"),
				'modified' => date("c"),
				'other_data' => null,
			));

			msg("Action inserted to the database", "ok", 1);

		} else msg("Unable to insert action to database, missing parameters " . print_r($this->GET, true), "error", 1);

		return true;
	}

}
