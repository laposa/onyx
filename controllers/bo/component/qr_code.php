<?php
/** 
 * Copyright (c) 2024 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

use chillerlan\QRCode\{QRCode, QROptions};

class Onyx_Controller_Bo_Component_Qr_Code extends Onyx_Controller {

    const CACHE_DIRECTORY = ONYX_PROJECT_DIR . "var/qr-code/";

    /**
     * main action
     */
     
    public function mainAction() {

        $Node = new common_node();
        
        // make sure latest data are used during form save action
        if (array_key_exists('node', $_POST) && is_array($_POST['node'])) $node_detail = $_POST['node'];
        else $node_detail = $Node->nodeDetail($this->GET['node_id']);

        //nodeDetail returns custom_fields as an object and $_POST returns array
        $node_detail['custom_fields'] = (array) $node_detail['custom_fields'];

        $options = [
            'imageTransparent' => false,
            'scale' => 20,
        ];
        $qr_options = new QROptions($options);

        if (strlen(getenv('ONYX_MOBILE_APP_ASSOCIATED_HOSTNAME')) > 0) $hostname = getenv('ONYX_MOBILE_APP_ASSOCIATED_HOSTNAME');
        else $hostname = $_SERVER['HTTP_HOST'];

        $url = 'https://' . $hostname . '/'. $this->GET['node_id'];

        // QR Code regenerating
        if($this->GET['regenerate'] == 'true') {
            $url = $this->GET['params'] ? $url . '?' . urldecode($this->GET['params']) : $url;
        } else if ($node_detail['custom_fields']['qrcode_params']) {
            $url .= '?' . $node_detail['custom_fields']['qrcode_params'];
        }

        // Check for valid URL
        if(filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            $this->tpl->parse('content.fail');
            return true;
        }

        $filename = base64_encode($url . implode(',', $options)) . ".png";
        $cached_file_path = self::CACHE_DIRECTORY . $filename;

        if (!file_exists(self::CACHE_DIRECTORY)) mkdir(self::CACHE_DIRECTORY);

        if (!file_exists($cached_file_path) && is_writeable(self::CACHE_DIRECTORY)) {
            $qrcode = new QRCode($qr_options);
            $qrcode = $qrcode->render($url, $cached_file_path);
        }
        
        $this->tpl->assign('NODE', $node_detail);
        $this->tpl->assign('QRCODE_URL', $url);
        $this->tpl->assign('QRCODE', 'var/qr-code/' . $filename);
        return true;
    }
}
