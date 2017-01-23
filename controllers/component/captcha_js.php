<?php
/** 
 * Copyright (c) 2005-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Component_Captcha_Js extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {

		$node_id = (int) $this->GET['node_id'];

		if ($node_id > 0 && $this->nodeUsesCaptcha($node_id)) {

			$this->showCode($node_id);

		}

		return true;
	}

	protected function nodeUsesCaptcha($node_id)
	{
		if ($this->GET['nocheck']) return true;

		// confirm the noded uses javascript captcha
		require_once('models/common/common_node.php');
		$Node = new common_node();
		$node_data = $Node->nodeDetail($node_id);

		return ($node_data['component']['spam_protection'] =='captcha_text_js' || 
				$node_data['component']['parameter'] == 'spam_protection=captcha_text_js' ||
				$node_data['component']['parameter'] == 'spam_protection=captcha_image' ||
				$node_data['node_controller'] == 'news');
	}

	protected function showCode($node_id)
	{
		// captcha
		$word = $this->generateRandomWord();

		// save code to session
		if (!is_array($_SESSION['captcha'])) $_SESSION['captcha'] = array();
		$_SESSION['captcha'][$node_id] = $word;

		$this->tpl->assign("CODE", $word);
	}

	protected function generateRandomWord($length = 5)
	{
		$str = '';
		for($i = 0; $i < $length; $i++) $str .= chr(rand(97, 122));
		return $str;
	}

}
