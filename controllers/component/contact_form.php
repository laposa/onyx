<?php
/** 
 * Copyright (c) 2005-2016 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Component_Contact_Form extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {

		$this->enableCaptcha = (($this->GET['spam_protection'] == "captcha_image" ||
			$this->GET['spam_protection'] == "captcha_text_js") && 
			strpos($this->tpl->filecontents, 'formdata-captcha_') !== FALSE);

		$formdata = $this->preProcessEmailForm($_POST['formdata']);

		if (isset($_POST['send']) && $_POST['node_id'] == $this->GET['node_id']) {
			
			$formdata = $this->processEmailForm($formdata);
			
		}

		$formdata = $this->postProcessEmailForm($formdata);

		$this->tpl->assign('FORMDATA', $formdata);
		
		if ($this->enableCaptcha) {
			if ($this->GET['spam_protection'] == "captcha_text_js") {
				$this->tpl->parse("content.invisible_captcha_field");
			} else {
				$this->tpl->parse("content.captcha_field");
			}
		}

		return true;
	}
	
	/**
	 * preprocess
	 */
	 
	public function preProcessEmailForm($formdata) {
		
		$this->tpl->assign('MAX_FILE_SIZE', ini_get('upload_max_filesize'));
	
		$this->parseStoreSelect($formdata['form']['store_id'], 'content');
	
		/**
		 * pre-populate with customer data if available
		 */
		 
		if ($_SESSION['client']['customer']['id'] > 0) {
			
			if (!$formdata['first_name']) $formdata['first_name'] = $formdata['required_first_name'] = $_SESSION['client']['customer']['first_name'];
			if (!$formdata['last_name']) $formdata['last_name'] = $formdata['required_last_name'] = $_SESSION['client']['customer']['last_name'];
			if (!$formdata['name']) $formdata['name'] = $formdata['required_name'] = $formdata['first_name'] . " " . $formdata['last_name'];
			if (!$formdata['email']) $formdata['email'] = $formdata['required_email'] = $_SESSION['client']['customer']['email'];
			if (!$formdata['telephone']) $formdata['telephone'] = $formdata['required_telephone'] = $_SESSION['client']['customer']['telephone'];
			
			$formdata['required_first_name'] = $formdata['first_name'];
			$formdata['required_last_name'] = $formdata['last_name'];
			$formdata['required_name'] = $formdata['name'];
			$formdata['required_email'] = $formdata['email'];
			$formdata['required_telephone'] = $formdata['telephone'];
			
		}
		
		return $formdata;
			
	}
	
	/**
	 * postprocess
	 */
	 
	public function postProcessEmailForm($formdata) {
		
		return $formdata;
		
	}
	
	
	/**
	 * process form send action
	 */
	 
	public function processEmailForm($formdata) {
	
			if (!is_array($formdata)) return false;
			
			require_once('models/common/common_email.php');
		    
			$Email = new common_email();
		    
			$content = $Email->exploreFormData($formdata);

			$node_id = (int) $this->GET['node_id'];
			$reg_key = "form_notify_" . $node_id;

			if ($this->GET['mail_to'] == '') {
				$mail_to = $Email->conf['mail_recipient_address'];
				$mail_toname = $Email->conf['mail_recipient_name'];
			} else {
				$mail_to = $this->GET['mail_to'];
				$mail_toname = $this->GET['mail_toname'];
			}

			if ($this->enableCaptcha) {
				$word = strtolower($_SESSION['captcha'][$node_id]);
				$isCaptchaValid = strlen($formdata['captcha']) > 0 &&  $formdata['captcha'] == $word;
				$Email->setValid("captcha", $isCaptchaValid);
			}

			if ($Email->sendEmail('contact_form', $content, $mail_to, $mail_toname, $formdata['required_email'], $formdata['required_name'])) {
				Zend_Registry::set($reg_key, 'sent');
			} else {
				Zend_Registry::set($reg_key, 'failed');
			}

			return $formdata;
			
	}
	
	/**
	 * parse country list
	 */
	 
	public function parseCountryList($template_block = 'content.country') {
		
		require_once('models/international/international_country.php');
		$Country = new international_country();
		$countries = $Country->listing();
		
		foreach ($countries as $c) {
			if ($c['id'] == $_POST['formdata']['country_id']) $c['selected'] = "selected='selected'";
			else $c['selected'] = '';
			$this->tpl->assign('COUNTRY', $c);
			$this->tpl->parse("{$template_block}.item");
		}
		
		$this->tpl->parse($template_block);
		
	}
	
	
	/**
	 * parseStoreSelect
	 */

	protected function parseStoreSelect($selected_id, $template_block_path = 'content.form')
	{
		
		require_once('models/ecommerce/ecommerce_store.php');
		$Store = new ecommerce_store();
		
		$provinces = $this->getTaxonomyBranch($GLOBALS['onxshop_conf']['global']['province_taxonomy_tree_id']);

		$total_store_count = 0;
		
		foreach ($provinces as $province) {

			$this->tpl->assign("PROVINCE_NAME", $province['label']['title']);

			$counties = $this->getTaxonomyBranch($province['id']);

			foreach ($counties as $county) {
				$county['selected'] = ($selected_id == $county['id'] ? 'selected="selected"' : '');
				$this->tpl->assign("COUNTY", $county);
				// get all stores in this count
				$store_list = $Store->getFilteredStoreList($county['id'], false, 1, false, false, 1000); //limit to 1000 records per county and type_id=1
				
				foreach ($store_list as $store_item) {
					if ($store_item['publish']) {
						$this->tpl->assign('STORE', $store_item);
						$this->tpl->parse("$template_block_path.store.county_dropdown.province.store");
						$total_store_count++;
					}
				}
			}

			$this->tpl->parse("$template_block_path.store.county_dropdown.province");

		}

		$this->tpl->parse("$template_block_path.store.county_dropdown");
		
		// show only if there is at least one store
		if (count($total_store_count) > 0) $this->tpl->parse("$template_block_path.store");
	}

	/**
	 * getTaxonomyBranch
	 */
	 
	public function getTaxonomyBranch($parent)
	{
		require_once('models/common/common_taxonomy.php');
		$Taxonomy = new common_taxonomy();
		
		return $Taxonomy->getChildren($parent);
	}
}
