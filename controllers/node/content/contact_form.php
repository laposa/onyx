<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onxshop_Controller_Node_Content_Contact_Form extends Onxshop_Controller_Node_Content_Default {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/common/common_node.php');
		
		$Node = new common_node();
		
		$node_data = $Node->nodeDetail($this->GET['id']);

		if (!is_array($node_data['component'])) {
			$node_data['component'] = array();
			$node_data['component']['node_controller'] = 'common_simple';
		}

		if ($node_data['component']['sending_failed'] == '') $node_data['component']['sending_failed'] = 'The provided data is not valid! Required items are marked with an asterisk (*)';
		if ($node_data['component']['text'] == '') $node_data['component']['text'] = "Thank you for your feedback.";
		
		$this->tpl->assign("NODE", $node_data);
		
		$Form = new nSite("component/contact_form@component/_contact_form/{$node_data['component']['node_controller']}&amp;node_id={$node_data['id']}&amp;mail_to={$node_data['component']['mail_to']}&amp;mail_toname={$node_data['component']['mail_toname']}&amp;spam_protection={$node_data['component']['spam_protection']}");
		$this->tpl->assign("FORM", $Form->getContent());
		
		if (Zend_Registry::isRegistered('notify')) {
			if (Zend_Registry::get('notify') == 'sent') {
				//forward
				msg($node_data['component']['text'], 'ok');
				if ($node_data['component']['href'] != '') onxshopGoTo($node_data['component']['href']);
			} else if (Zend_Registry::get('notify')  == 'failed') {
				msg($node_data['component']['sending_failed'], 'error');
				$this->tpl->parse('content.form');
			} else {
				$this->tpl->parse('content.form');
			}
		} else {
			$this->tpl->parse('content.form');
		}
		
		if ($node_data['display_title'])  $this->tpl->parse('content.title');

		return true;
	}
}
