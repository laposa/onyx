<?php
/**
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/default.php');
require_once('models/education/education_survey.php');

class Onxshop_Controller_Bo_Node_Content_Survey extends Onxshop_Controller_Bo_Node_Default {

	/**
	 * pre action
	 */
	function pre() {

	}

	/**
	 * post action
	 */
	 
	function post() {

		// survey dropdown	
		$Survey = new education_survey();

		$surveys = $Survey->getSurveyList("publish = 1");

		foreach ($surveys as $survey) {
			$survey['selected'] = ($this->node_data['component']['survey_id'] == $survey['id']) ? "selected='selected'" : '';
			$this->tpl->assign("SURVEY_ITEM", $survey);
			$this->tpl->parse("content.survey_item");
		}

		// template dropdown	
		$this->tpl->assign("SELECTED_template_{$this->node_data['component']['template']}", "selected='selected'");

		// restriction dropdown	
		$this->tpl->assign("SELECTED_restriction_{$this->node_data['component']['restriction']}", "selected='selected'");

		// limit dropdown	
		$this->tpl->assign("SELECTED_limit_{$this->node_data['component']['limit']}", "selected='selected'");

		$this->tpl->assign("SPAM_PROTECTION", array(
			'captcha_text_js' => ($this->node_data['component']['spam_protection'] == 'captcha_text_js' ? 'selected="selected"' : '')
		));

	}
}
