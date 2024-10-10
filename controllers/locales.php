<?php
/**
 * Copyright (c) 2009-2024 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Locales extends Onyx_Controller {

    /**
     * main action
     */
    public function mainAction()
    {
        // Get input variables
        if (preg_match("/\.bo\/backoffice\./", $this->GET['request'] ?? '')) {
            $locale = $GLOBALS['onyx_conf']['global']['locale'];
        } else {
            if (is_array($_POST) && array_key_exists('locale', $_POST)) {
                $locale_map = ['', 'en_GB.UTF-8', 'en_US.UTF-8', 'en_IE.UTF-8', 'cs_CZ.UTF-8', 'de_DE.UTF-8', 'en_AU.UTF-8', 'ja_JP.UTF-8', 'en_CA.UTF-8', 'en_HK.UTF-8', 'en_NZ.UTF-8', 'ru_RU.UTF-8', 'he_IL.UTF-8'];
                $locale = $locale_map[$_POST['locale']];
            } elseif (isset($_SESSION) && is_array($_SESSION) && array_key_exists('locale', $_SESSION)) {
                $locale = $_SESSION['locale'];
            } else {
                if (isset($GLOBALS['onyx_conf']['global']['locale']) && $GLOBALS['onyx_conf']['global']['locale'] != '') $locale = $GLOBALS['onyx_conf']['global']['locale'];
                else $locale = 'en_GB.UTF-8';
            }
        }

        // Check input variables
        $allowed_locales = ['en_GB.UTF-8', 'en_US.UTF-8', 'en_IE.UTF-8', 'cs_CZ.UTF-8', 'de_DE.UTF-8', 'en_AU.UTF-8', 'ja_JP.UTF-8', 'en_CA.UTF-8', 'en_HK.UTF-8', 'en_NZ.UTF-8', 'ru_RU.UTF-8', 'he_IL.UTF-8'];

        if (!in_array($locale, $allowed_locales)) {
            msg("Invalid Locale", "error", 1);
            if ($GLOBALS['onyx_conf']['global']['locale'] != '') $locale = $GLOBALS['onyx_conf']['global']['locale'];
            else $locale = 'en_GB.UTF-8';
        }

        // store across app and in session if different
        define('LOCALE', $locale);
        define('LOCALE_OPENGRAPH', $this->mapToFacebook($locale));

        if (!isset($_SESSION['locale']) || $_SESSION['locale'] != LOCALE) $_SESSION['locale'] = LOCALE;
        $this->setLocale($locale);

        return true;
    }

    /**
     * Set the locale
     * @param mixed|string $locale
     */
    function setLocale($locale = LOCALE)
    {
        //detect if local file with language string constants is available and load it
        $local_constants_file = ONYX_PROJECT_DIR . "locales/$locale/constants.php";
        if (file_exists($local_constants_file)) require_once($local_constants_file);

        //load global language string constants file with fallback to en_GB
        $global_constants_file = ONYX_DIR . "locales/$locale/constants.php";
        if (!file_exists($global_constants_file)) $global_constants_file = ONYX_DIR . "locales/en_GB.UTF-8/constants.php";
        require_once($global_constants_file);

        //now set system locale
        setlocale(LC_ALL, LOCALE);
        //but for numbers keep english
        setlocale(LC_NUMERIC, 'en_GB.UTF-8');

        if (LOCALE == 'cs_CZ.UTF-8') {
            putenv("TZ=Europe/Prague");
            putenv("LANG=cs_CZ.UTF-8");
            date_default_timezone_set("Europe/Prague");
        } else {
            putenv("TZ=Europe/London");
            putenv("LANG=en_GB.UTF-8");
            date_default_timezone_set("Europe/London");
        }

        $currency = (new NumberFormatter(substr($locale, 0, 5), NumberFormatter::CURRENCY))
            ->getTextAttribute(NumberFormatter::CURRENCY_CODE);
        define('GLOBAL_LOCALE_CURRENCY', $currency);
    }

    /**
     * mapToFacebook
     */
    public function mapToFacebook($locale)
    {
        switch ($locale) {
            case 'en_IE.UTF-8';
                $facebook_locale = 'en_GB';
                break;
            default:
                $facebook_locale = substr($locale, 0, 5);
                break;
        }

        return $facebook_locale;
    }
}
