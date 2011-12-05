<?php
/**
 * Paypal payment configuration
 *
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

define('ECOMMERCE_TRANSACTION_PAYPAL_URL', 'https://www.sandbox.paypal.com/cgi-bin/webscr');
//define('ECOMMERCE_TRANSACTION_PAYPAL_URL', 'https://www.paypal.com/cgi-bin/webscr');
define('ECOMMERCE_TRANSACTION_PAYPAL_VENDOR_EMAIL', $GLOBALS['onxshop_conf']['global']['admin_email']);
define('ECOMMERCE_TRANSACTION_MAIL_TO', $GLOBALS['onxshop_conf']['global']['admin_email']);
define('ECOMMERCE_TRANSACTION_MAIL_TONAME', $GLOBALS['onxshop_conf']['global']['admin_email_name']);
define('ECOMMERCE_TRANSACTION_PAYPAL_DESCRIPTION', "Payment for order at {$GLOBALS['onxshop_conf']['global']['title']}.");
define('ECOMMERCE_TRANSACTION_PAYPAL_RETURN', "http://" . $_SERVER['SERVER_NAME'] . "/page/12");//PAYMENT SUCCESS
define('ECOMMERCE_TRANSACTION_PAYPAL_CANCEL', "http://" . $_SERVER['SERVER_NAME'] . "/page/11");//PAYMENT FAILURE

define('ECOMMERCE_TRANSACTION_PAYPAL_API_USERNAME', '');
define('ECOMMERCE_TRANSACTION_PAYPAL_API_PASSWORD', '');
define('ECOMMERCE_TRANSACTION_PAYPAL_API_SIGNATURE', '');
