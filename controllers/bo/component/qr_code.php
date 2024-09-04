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

        $options = [
            'imageTransparent' => false,
            'scale' => 20,
        ];

        $qr_options = new QROptions($options);

        if (strlen(getenv('ONYX_MOBILE_APP_ASSOCIATED_HOSTNAME')) > 0) $hostname = getenv('ONYX_MOBILE_APP_ASSOCIATED_HOSTNAME');
        else $hostname = $_SERVER['HTTP_HOST'];

        $url = 'https://' . $hostname . '/'. $this->GET['node_id'];
        $filename = base64_encode($url . implode(',', $options)) . ".png";

        $cached_file_path = self::CACHE_DIRECTORY . $filename;

        if (!file_exists(self::CACHE_DIRECTORY)) mkdir(self::CACHE_DIRECTORY);

        if (!file_exists($cached_file_path) && is_writeable(self::CACHE_DIRECTORY)) {
            $qrcode = new QRCode($qr_options);
            $qrcode = $qrcode->render($url, $cached_file_path);
        }
        
        $this->tpl->assign('QRCODE_URL', $url);
        $this->tpl->assign('QRCODE', 'var/qr-code/' . $filename);
        return true;
    }
}
