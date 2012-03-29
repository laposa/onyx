<?php
/**
 * Copyright (c) 2009-2012 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Locales extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * Get input variables
		 */
		if (preg_match("/\.bo\/backoffice\./", $this->GET['request'])) {
		
			$locale = $GLOBALS['onxshop_conf']['global']['locale'];
		
		} else {
		
			if ($_POST['locale']) {
				$locale_map = array('', 'en_GB.UTF-8', 'en_US.UTF-8', 'en_IE.UTF-8', 'cs_CZ.UTF-8', 'de_DE.UTF-8', 'en_AU.UTF-8', 'ja_JP.UTF-8', 'en_CA.UTF-8', 'en_HK.UTF-8', 'en_NZ.UTF-8', 'ru_RU.UTF-8', 'he_IL.UTF-8');
				$locale = $locale_map[$_POST['locale']];
			} else if ($_SESSION['locale']) {
				$locale = $_SESSION['locale'];
			} else {
				if ($GLOBALS['onxshop_conf']['global']['locale'] != '') $locale = $GLOBALS['onxshop_conf']['global']['locale'];
				else $locale = 'en_GB.UTF-8';
			}
		}

		
		/**
		 * Check input variables
		 */
		
		
		$allowed_locales = array('en_GB.UTF-8', 'en_US.UTF-8', 'en_IE.UTF-8', 'cs_CZ.UTF-8', 'de_DE.UTF-8', 'en_AU.UTF-8', 'ja_JP.UTF-8', 'en_CA.UTF-8', 'en_HK.UTF-8', 'en_NZ.UTF-8', 'ru_RU.UTF-8', 'he_IL.UTF-8');
		
		if (!in_array($locale, $allowed_locales)) {
		
			msg ("Invalid Locale", "error");
			$locale = 'en_GB.UTF-8';
		
		}
		
		/**
		 * store across app and in session if different
		 */
		
		define('LOCALE', $locale);
		
		if ($_SESSION['locale'] != LOCALE) $_SESSION['locale'] = LOCALE;
		
		/**
		 * Process
		 */
		
		$this->setLocale($locale);
		

		return true;
	}
	
	/**
	 * Set the locale
	 */
	
	function setLocale($locale = LOCALE) {
		
		//load language string constants
		$constants_file = ONXSHOP_DIR . "locales/$locale/constants.php";
		if (!file_exists($constants_file)) $constants_file = ONXSHOP_DIR . "locales/en_GB.UTF-8/constants.php";
		require_once($constants_file);
		
		setlocale(LC_ALL, LOCALE);
		//but for numbers keep english
		setlocale(LC_NUMERIC, 'en_GB.UTF-8');

		require_once('lib/Zend/Locale.php');
		require_once('lib/Zend/Currency.php');
		
		if (LOCALE == 'cs_CZ.UTF-8') {
			putenv("TZ=Europe/Prague");
			putenv("LANG=cs_CZ.UTF-8");
			date_default_timezone_set("Europe/Prague");
		} else {
			putenv("TZ=Europe/London");
			putenv("LANG=en_GB.UTF-8");
			date_default_timezone_set("Europe/London");
		}
		
		
		$Zend_locale = new Zend_Locale(substr($locale, 0, 5));
		
		$Zend_currency = new Zend_Currency($Zend_locale->toString());
		define('GLOBAL_LOCALE_CURRENCY', $Zend_currency->getShortName());
	}
}
