<?php
/** 
 * Copyright (c) 2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once 'controllers/component/flickr.php';

class Onyx_Controller_Component_Flickr_Photoset_Detail extends Onyx_Controller_Component_Flickr
{

    /**
     * main action 
     */

    public function mainAction() {

        $this->init();

        $photoset_id = $this->GET['photoset_id'];

        $list = $this->getPhotoset($photoset_id);

        foreach ($list['photoset']['photo'] as $item) {
            $this->tpl->assign('ITEM', $item);
            $this->tpl->parse('content.item');
        }

        return true;

    }

}
