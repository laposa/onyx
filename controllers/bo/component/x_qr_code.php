<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

use chillerlan\QRCode\{QRCode, QROptions};
require_once('controllers/bo/component/x.php');
require_once('models/common/common_node.php');

class Onyx_Controller_Bo_Component_X_Qr_Code extends Onyx_Controller_Bo_Component_X {

    const CACHE_DIRECTORY = ONYX_PROJECT_DIR . "var/qr-code/";

    /**
     * main action
     */
     
    public function mainAction() {

        // get details
        $node = new common_node();
        $node_data = $node->nodeDetail($this->GET['node_id']);

        // nodeDetail returns custom_fields as an object and $_POST returns array
        $node_data['custom_fields'] = (array) $node_data['custom_fields'];

        // QR code options
        $options = [
            'imageTransparent' => false,
            'scale' => 20,
        ];
        $qr_options = new QROptions($options);

        // get proper URL
        if (strlen(getenv('ONYX_MOBILE_APP_ASSOCIATED_HOSTNAME')) > 0) $hostname = getenv('ONYX_MOBILE_APP_ASSOCIATED_HOSTNAME');
        else $hostname = $_SERVER['HTTP_HOST'];

        $url = 'https://' . $hostname . '/'. $this->GET['node_id'];

        // Additional parameters
        if($this->GET['params']) {
            $node_data['custom_fields']['qrcode_params'] = urldecode($this->GET['params']);
        }

        if ($node_data['custom_fields']['qrcode_params'] ?? false) {
            $url .= '?' . $node_data['custom_fields']['qrcode_params'];
        }

        // Check for valid URL
        if(filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            $this->tpl->parse('content.fail');
            return true;
        }

        // Create QR code
        $filename = md5($url . implode(',', $options)) . ".png";
        $cached_file_path = self::CACHE_DIRECTORY . $filename;

        if (!file_exists(self::CACHE_DIRECTORY)) mkdir(self::CACHE_DIRECTORY);

        if (!file_exists($cached_file_path) && is_writeable(self::CACHE_DIRECTORY)) {
            $qrcode = new QRCode($qr_options);
            $qrcode = $qrcode->render($url, $cached_file_path);
        }

        // save
        if (isset($_POST['save'])) {
            // TODO: messages
            if($node->nodeUpdate($_POST['node'])) {
                msg("{$node_data['node_group']} (id={$node_data['id']}) has been updated");
                // header('HX-Trigger: {"nodeUpdated":{"init" :"false"}}');
            } else {
                msg("Cannot update node {$node_data['node_group']} (id={$node_data['id']})", 'error');
            }
        }
        
        $this->tpl->assign('QRCODE_URL', $url);
        $this->tpl->assign('QRCODE', 'var/qr-code/' . $filename);
        $this->tpl->assign('NODE', $node_data);

        parent::parseTemplate();
        
        return true;
    }
}
