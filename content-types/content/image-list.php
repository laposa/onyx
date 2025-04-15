<?php
/**
 * Copyright (c) 2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onyx_Controller_Node_Content_Image_List extends Onyx_Controller_Node_Content_Default {

    /**
     * main action
     */

    public function mainAction() {
        
        parent::mainAction();

        // get list
        require_once('models/common/common_image.php');
        $this->Image = new common_image();
        
        $list = $this->Image->listFiles($this->GET['id']);

        foreach ($list as $item) {
            $this->tpl->assign('ITEM', $item);
            $this->tpl->parse('content.item');
        }

        return true;
    }
}
