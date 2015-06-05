<?php
/**
 * Copyright (c) 2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/survey.php');

class Onxshop_Controller_Bo_Component_Survey_Question_Add extends Onxshop_Controller_Bo_Component_Survey {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		$this->Question = $this->initializeQuestion();
		
		/**
		 * Save on request
		 */
		 
		if ($_POST['save'] && is_array($_POST['question'])) {
		
			$this->saveQuestion($_POST['question']);
			
		}
				

		/**
		 * destroy
		 */
		 
		$this->Question = false;
		
		return true;
	}
	
}

