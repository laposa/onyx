<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Component_Captcha_Js extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {

		/** 
		 * get input variables
		 */

		$node_id = (int) $this->GET['node_id'];

		if ($node_id > 0) {

			// confirm the noded uses javascript captcha
			require_once('models/common/common_node.php');
			$Node = new common_node();
			$node_data = $Node->nodeDetail($node_id);

			if ($node_data['component']['spam_protection'] =='captcha_text_js' || 
				$node_data['node_controller'] == 'news') {

				// captcha
				$word = $this->generateRandomWord();

				// save code to session
				if (!is_array($_SESSION['captcha'])) $_SESSION['captcha'] = array();
				$_SESSION['captcha'][$node_id] = $word;

				$this->tpl->assign("CODE", $word);
			}
		}

		return true;
	}

	protected function generateRandomWord($length = 5)
	{
		$str = '';
		for($i = 0; $i < $length; $i++) $str .= chr(rand(97, 122));
		return $str;
	}

}
