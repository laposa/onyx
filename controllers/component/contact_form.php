<?php
/** 
 * Copyright (c) 2005-2013 Laposa Ltd (http://laposa.co.uk)
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

		$this->preProcessEmailForm();
		
		if (isset($_POST['send']) && $_POST['node_id'] == $this->GET['node_id']) {
			
			$this->processEmailForm($_POST['formdata']);
			
		}

		$this->postProcessEmailForm();

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
	 
	public function preProcessEmailForm() {
		
		$this->tpl->assign('MAX_FILE_SIZE', ini_get('upload_max_filesize'));
	
	}
	
	/**
	 * postprocess
	 */
	 
	public function postProcessEmailForm() {
		
		return true;
		
	}
	
	
	/**
	 * process form send action
	 */
	 
	public function processEmailForm($formdata) {
	
			if (!is_array($formdata)) return false;
			
			require_once('models/common/common_email.php');
		    
			$EmailForm = new common_email();
		    
			$content = $EmailForm->exploreFormData($formdata);

			if ($this->GET['mail_to'] == '') {
				$mail_to = $EmailForm->conf['mail_recipient_address'];
				$mail_toname = $EmailForm->conf['mail_recipient_name'];
			} else {
				$mail_to = $this->GET['mail_to'];
				$mail_toname = $this->GET['mail_toname'];
			}

			if ($this->enableCaptcha) {
				$node_id = (int) $this->GET['node_id'];
				$word = strtolower($_SESSION['captcha'][$node_id]);
				$isCaptchaValid = strlen($formdata['captcha']) > 0 &&  $formdata['captcha'] == $word;
				$EmailForm->setValid("captcha", $isCaptchaValid);
			}

			if ($EmailForm->sendEmail('contact_form', $content, $mail_to, $mail_toname, $formdata['required_email'], $formdata['required_name'])) {
				Zend_Registry::set('notify', 'sent');
			} else {
				Zend_Registry::set('notify', 'failed');
				$this->tpl->assign('FORMDATA', $formdata);
			}

			return true;
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
}
