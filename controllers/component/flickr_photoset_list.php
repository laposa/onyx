<?php
/** 
 * Copyright (c) 2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once 'controllers/component/flickr.php';

class Onxshop_Controller_Component_Flickr_Photoset_List extends Onxshop_Controller_Component_Flickr
{

    /**
     * main action 
     */

    public function mainAction() {

        $this->init();

        $user_id = $this->getUserIdByUserName($this->GET['username']);

        if (is_numeric($this->GET['limit_per_page'])) $limit_per_page = $this->GET['limit_per_page'];
        else $limit_per_page = 9;

        if (is_numeric($this->GET['limit_from'])) $limit_from = floor($this->GET['limit_from'] / $limit_per_page) + 1;
        else $limit_from = 1;

        $list = $this->getPhotosetList($user_id, $limit_from, $limit_per_page);

        if (is_array($list)) {
            foreach ($list['photoset'] as $item) {
                $this->tpl->assign('ITEM', $item);
                $this->tpl->parse('content.item');
            }
            if ($list['pages'] > $limit_from && $limit_per_page == 9) {
                $this->tpl->assign('NEXT_PAGE', $limit_from * $limit_per_page);
                $this->tpl->parse('content.more');  
            }
        }

        return true;

    }

}
