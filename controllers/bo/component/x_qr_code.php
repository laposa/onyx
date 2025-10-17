<?php
/** 
 * Copyright (c) 2024-2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

use chillerlan\QRCode\{QRCode, QROptions};
require_once('controllers/bo/component.php');

class Onyx_Controller_Bo_Component_X_Qr_Code extends Onyx_Controller_Bo_Component {

    const CACHE_DIRECTORY = ONYX_PROJECT_DIR . "var/qr-code/";

    /**
     * main action
     */
     
    public function mainAction() {

        parent::assignNodeData();
        
        // make sure latest data are used during form save action
        if (array_key_exists('node', $_POST) && is_array($_POST['node'])) $this->node_data = $_POST['node'];
        else $this->node_data = $this->Node->nodeDetail($this->GET['node_id']);

        //nodeDetail returns custom_fields as an object and $_POST returns array
        $this->node_data['custom_fields'] = (array) $this->node_data['custom_fields'];

        $options = [
            'imageTransparent' => false,
            'scale' => 20,
        ];
        $qr_options = new QROptions($options);

        if (strlen(getenv('ONYX_MOBILE_APP_ASSOCIATED_HOSTNAME')) > 0) $hostname = getenv('ONYX_MOBILE_APP_ASSOCIATED_HOSTNAME');
        else $hostname = $_SERVER['HTTP_HOST'];

        $url = 'https://' . $hostname . '/'. $this->GET['node_id'];

        if($this->GET['params']) {
            $this->node_data['custom_fields']['qrcode_params'] = urldecode($this->GET['params']);
        }

        if ($this->node_data['custom_fields']['qrcode_params'] ?? false) {
            $url .= '?' . $this->node_data['custom_fields']['qrcode_params'];
        }

        // Check for valid URL
        if(filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            $this->tpl->parse('content.fail');
            return true;
        }

        $filename = md5($url . implode(',', $options)) . ".png";
        $cached_file_path = self::CACHE_DIRECTORY . $filename;

        if (!file_exists(self::CACHE_DIRECTORY)) mkdir(self::CACHE_DIRECTORY);

        if (!file_exists($cached_file_path) && is_writeable(self::CACHE_DIRECTORY)) {
            $qrcode = new QRCode($qr_options);
            $qrcode = $qrcode->render($url, $cached_file_path);
        }
        
        $this->tpl->assign('QRCODE_URL', $url);
        $this->tpl->assign('QRCODE', 'var/qr-code/' . $filename);

        parent::parseTemplate();
        
        return true;
    }
}
