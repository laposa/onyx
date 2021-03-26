<?php
/**
 * Protx (SagePay, Opayo) configuration
 *
 * Copyright (c) 2009-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */


define('ECOMMERCE_TRANSACTION_PROTX_URL', getenv('ECOMMERCE_TRANSACTION_PROTX_URL'));
define('ECOMMERCE_TRANSACTION_PROTX_VENDOR', getenv('ECOMMERCE_TRANSACTION_PROTX_VENDOR'));
define('ECOMMERCE_TRANSACTION_PROTX_PASSWORD', getenv('ECOMMERCE_TRANSACTION_PROTX_PASSWORD'));
define('ECOMMERCE_TRANSACTION_PROTX_VENDOR_EMAIL', $GLOBALS['onyx_conf']['global']['admin_email']);
define('ECOMMERCE_TRANSACTION_PROTX_VPSPROTOCOL', '3.00');
define('ECOMMERCE_TRANSACTION_PROTX_TXTYPE', 'PAYMENT');
define('ECOMMERCE_TRANSACTION_PROTX_MAIL_MESSAGE', "Thank you for your order from {$GLOBALS['onyx_conf']['global']['title']}.");
