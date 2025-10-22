<?php
/**
 * Copyright (c) 2014-2022 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Bo_Component_Revision_Compare extends Onyx_Controller {

    /**
     * main action
     */

    // TODO: potentially possible to merge with x_revision_list?
     
    public function mainAction() {
    
        require_once('models/common/common_revision.php');
        $Revision = new common_revision();
        $id = 0;

        if (in_array($this->GET['object'], common_revision::getAllowedRevisionObjects())) $object = $this->GET['object'];
        $selected_id = $this->GET['id'];
        $selected_id = explode("+", $selected_id);

        $this->parseList($selected_id);

        return true;
    }

    /**
     * parse
     */
    
    public function parseList($list) {
        
        if (count($list) > 0) {
            require_once('models/client/client_customer.php');
            require_once('models/common/common_revision.php');
            $Client_Customer = new client_customer();
            $Common_Revision = new common_revision();
            $prev = false;

            foreach ($list as $id) {
                $item['details'] = $Common_Revision->getRevisionById($id);
                $item['details']['content'] = unserialize($item['details']['content']);
                ksort($item['details']['content']);
                if($prev != false) {
                    // LOOK FOR NEW AND MODIFIED KEYS
                    foreach($item['details']['content'] as $key=>$row) {
                        $item['details']['content'][$key] = $row;
                        if(!isset($prev[$key])) {
                            $item['details']['content'][$key] = "*NEW* ".$item['details']['content'][$key];
                        } elseif($item['details']['content'][$key] != $prev[$key]) {
                            $item['details']['content'][$key] = "*MODIFIED* ".$item['details']['content'][$key];
                        }
                    }
                    // LOOK FOR REMOVED
                    foreach($prev as $key=>$row) {
                        if(!isset($item['details']['content'][$key])) {
                            $item['details']['content'][$key] = "*REMOVED* ".$prev[$key];
                        }
                    }
                }
                $prev = $item['details']['content'];

                $item['client_customer'] = $Client_Customer->getDetail($item['details']['customer_id']);
                foreach($item['details']['content'] as $key=>$line) {
                    if (strpos($line, "*NEW*") !== false) {
                        $this->tpl->assign('CSS_CLASS', 'highlight new');
                        $line = str_replace("*NEW* ", "", $line);
                    }
                    elseif (strpos($line, "*MODIFIED*") !== false) {
                        $this->tpl->assign('CSS_CLASS', 'highlight modified');
                        $line = str_replace("*MODIFIED* ", "", $line);
                    }
                    elseif (strpos($line, "*REMOVED*") !== false) {
                        $this->tpl->assign('CSS_CLASS', 'highlight removed');
                        $line = str_replace("*REMOVED* ", "", $line);
                    }
                    else {
                        $this->tpl->assign('CSS_CLASS', '');
                    }
                    $this->tpl->assign('LINE', $key.": ".$line);
                    $this->tpl->parse('content.item.line');

                }


                $this->tpl->assign('ITEM', $item);
                $this->tpl->assign('WIDTH', (100 / count($list)));
                $this->tpl->parse('content.item');
            }
        } else {
            $this->tpl->parse('content.empty');
        }
    }
    
}

