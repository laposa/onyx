<?php
/** 
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Locale_Switcher extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		$locale = $_SESSION['locale'];
		
		switch ($locale) {
			case'en_GB.UTF-8':
				$locale2 = 'gb';
			break;
			case 'en_US.UTF-8':
				$locale2 = 'us';
			break;
			case 'en_IE.UTF-8':
				$locale2 = 'eu';
			break;
			case 'en_AU.UTF-8':
				$locale2 = 'au';
			break;
			case 'ja_JP.UTF-8':
				$locale2 = 'jp';
			break;
			case 'en_CA.UTF-8':
				$locale2 = 'ca';
			break;
			case 'en_HK.UTF-8':
				$locale2 = 'hk';
			break;
			case 'en_NZ.UTF-8':
				$locale2 = 'nz';
			break;
		}
		
		$this->tpl->assign("SELECTED_$locale2", 'selected="selected"');
		return true;
	}
	
	/**
	 * other way
	 */
	 
	public function XXXmainAction() {
	
		require_once('models/international/international_currency.php');
		$Currency = new international_currency();
		
		if ($_POST['client']['customer']['currency_code']) $_SESSION['client']['customer']['currency_code'] = $_POST['client']['customer']['currency_code'];
		else $_SESSION['client']['customer']['currency_code'] = $Currency->conf['default'];
		
		$selected = $_SESSION['client']['customer']['currency_code'];
		
		
		
		$allowed = $Currency->conf['allowed'];
		$allowed_count = count($allowed);
		
		if ($allowed[0] == 'all') {
			$where = '';
		} else {
			$where = "code=";
			for ($i=0; $i<$allowed_count; $i++) {
				if ($i == ($allowed_count-1)) {
					$where = $where . "'{$allowed[$i]}'";
				} else {
					$where = $where . "'{$allowed[$i]}' OR code=";
				}
			}
		}
		
		$currencies = $Currency->listing($where,'name ASC');
		
		foreach ($currencies as $c) {
			if ($c['code'] == $selected) $c['selected'] = "selected='selected'";
			else $c['selected'] = '';
			$this->tpl->assign('currency', $c);
			$this->tpl->parse("content.item");
		}

		return true;
	}
}
