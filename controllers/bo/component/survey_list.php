<?php
/**
 * Copyright (c) 2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/survey.php');

class Onxshop_Controller_Bo_Component_Survey_List extends Onxshop_Controller_Bo_Component_Survey {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		$list = $this->getList();
		
		$this->parseList($list);

		return true;
	}
	
	/**
	 * get list
	 */
	 
	public function getList() {
	
		require_once('models/education/education_survey.php');
		$Survey = new education_survey();
		
		$list = $Survey->getSurveyList('', 'id DESC');

		return $list;
	}

	/**
	 * parse
	 */
	
	public function parseList($list) {
	
		if (count($list) > 0) {
			foreach ($list as $item) {
                
				$this->tpl->assign('ITEM', $item);
				$this->tpl->parse('content.item');
			}
		} else {
			$this->tpl->parse('content.empty');
		}
	}
}

