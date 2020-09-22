<?php
/**
 * Sage Pay (Protx) configuration
 *
 * Copyright (c) 2009-2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

define('ECOMMERCE_TRANSACTION_PROTX_URL', 'https://test.sagepay.com/gateway/service/vspform-register.vsp');
define('ECOMMERCE_TRANSACTION_PROTX_VENDOR', 'testvendor');
define('ECOMMERCE_TRANSACTION_PROTX_PASSWORD', 'testvendor');
define('ECOMMERCE_TRANSACTION_PROTX_VENDOR_EMAIL', $GLOBALS['onyx_conf']['global']['admin_email']);
define('ECOMMERCE_TRANSACTION_PROTX_VPSPROTOCOL', '3.00');
define('ECOMMERCE_TRANSACTION_PROTX_TXTYPE', 'PAYMENT');
define('ECOMMERCE_TRANSACTION_PROTX_MAIL_MESSAGE', "Thank you for your order from {$GLOBALS['onyx_conf']['global']['title']}.");
