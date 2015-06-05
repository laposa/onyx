<?php
/**
 * Copyright (c) 2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/common/common_node.php');
require_once('models/common/common_image.php');
require_once('models/ecommerce/ecommerce_store.php');

class Onxshop_Controller_Component_Ecommerce_Store_Notice extends Onxshop_Controller {

	/**
	 * main action
	 */
	public function mainAction()
	{
		$this->Store = new ecommerce_store();
		$this->Node = new common_node();
		$this->Image = new common_image();
		$this->Store->setCacheable(false);
		$this->Node->setCacheable(false);
		$this->Image->setCacheable(false);

		$node_id = (int) $this->GET['node_id'];
		$store = $this->Store->findStoreByNode($node_id);
		$isStoreManager = $this->isStoreManager($store);

		$notices = $this->Node->listing("parent = $node_id AND node_group = 'content' AND node_controller = 'notice'",
			'priority DESC', '0,6');

		if (count($notices) > 0) {

			$displayed = 0;

			foreach ($notices as $notice) {
				
				if ($notice['publish'] == 1 || $isStoreManager) {
					$notice['classes'] = 'notice_style_' . rand(1, 3);
					if (strlen($notice['other_data']['text']) < 65 && !$notice['other_data']['image']) $notice['classes'] .= ' notice_layout_less_text';
					if ($notice['other_data']['image']) $notice['classes'] .= ' notice_layout_with_image';
					if ($notice['publish'] == 0 && $isStoreManager) $notice['classes'] .= ' unpublished';
					$this->tpl->assign('NOTICE', $notice);
					$this->tpl->parse('content.notice_list.notice');
					$displayed++;
				}
			}

			if ($displayed > 0) $this->tpl->parse('content.notice_list');
		}

		if ($isStoreManager) {

			$this->processNewNotice($store, $node_id);
			$this->tpl->parse('content.store_manager');

		}

		return true;
	}


	/**
	 * is logged user given store manager?
	 */
	public function isStoreManager(&$store) {

		return (
			$_SESSION['client']['customer']['id'] > 0 && // is logged and
			$store['email'] == $_SESSION['client']['customer']['email'] // his email is store email
			|| // or
			$_SESSION['authentication']['authenticity'] // is admin (bo user)
		);
	}


	/**
	 * save submitted form
	 */
	public function processNewNotice(&$store, $node_id) {

		if (empty($_POST['notice']['text'])) return false;

		$image_path = false;

		if (is_uploaded_file($_FILES['image']['tmp_name'])) {
			$_FILES['image']['name'] = $store['title'] . '-' . $_FILES['image']['name'];
			$upload = $this->Image->getSingleUpload($_FILES['image'], 'var/files/notices/', true);
			if (is_string($upload)) $image_path = $upload;
		}

		$parts = explode("/", $_POST['notice']['visible_from'], 3);
		$unix_date = strtotime($parts[2] . "-" . $parts[1] . "-" . $parts[0]);

		$html = '';
		$html .= '<div class="date">' . date("d/m/y", $unix_date) . "</div>\n";
		$html .= '<div class="text">'  . nl2br($_POST['notice']['text']) . "</div>\n";
		if ($image_path) $html .= '<div class="image"><img src="/thumbnail/200x110/' . $image_path . '?method=crop" alt=""/></div>';

		$node = array(
			'title' => 'Store Notice',
			'node_group' => 'content',
			'node_controller' => 'notice',
			'parent' => $node_id,
			'parent_container' => 4,
			'priority' => $unix_date,
				/*
					SQL to set the priority retroactively

					UPDATE common_node SET priority = 
						extract(
							epoch from to_timestamp(
								(regexp_matches(other_data, 'visible_from.;s:10:.(..........)'))[1], 
								'DD/MM/YYYY'
							)
						)::integer 
					WHERE node_controller = 'notice'
				 */
			'content' => $html,
			'publish' => 0,
			'display_in_menu' => 0,
			'css_class' => 'store',
			'other_data' => array(
				'type' => 'store_notice',
				'text' => $_POST['notice']['text'],
				'visible_from' => $_POST['notice']['visible_from'],
				'visible_to' => $_POST['notice']['visible_to'],
				'image' => $image_path
			)
		);

		$new_node_id = $this->Node->nodeInsert($node);

		if ($new_node_id && $image_path) $this->Image->insertFile(array(
			'src' => $image_path,
			'role' => 'main',
			'node_id' => $new_node_id,
			'title' => 'Store Notice Image'
		));

		$this->sendAlertEmail($store, $node['other_data']);

		if ($new_node_id) onxshopGoto("/page/$node_id");
	}

	/**
	 * Send email to administrator
	 */
	public function sendAlertEmail($store, $notice)
	{
		require_once('models/common/common_email.php');
		$EmailForm = new common_email();

		$GLOBALS['common_email']['store'] = $store;
		$GLOBALS['common_email']['notice'] = $notice;

		$to_email = false; // admin
		$to_name = false;
		$email_from = $store['email'];
		$name_from = $store['manager_name'];

		$_FILES = array(); // remove to attach uploaded file

		$EmailForm->sendEmail('store_notice_notify', 'n/a', $to_email, $to_name);
	}

}
