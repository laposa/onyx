<?php
/** 
 * Copyright (c) 2010-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/file_list.php');

class Onxshop_Controller_Bo_Component_File_List_Icon extends Onxshop_Controller_Bo_Component_File_List {

    /**
     * parse item
     */
    
    public function parseItem($file_detail, $type, $relation) {
                    
        $this->tpl->assign('FILE', $file_detail);
            
        switch ($type) {
        
            case'RTE':
                $this->tpl->parse("content.list.item.image_zoom");
                $this->tpl->parse("content.list.item.image_view_full");
                $this->tpl->parse("content.list.item.RTE_select");
            break;
            case 'CSS':
                $this->tpl->parse("content.list.item.image_zoom");
                $this->tpl->parse("content.list.item.image_view_full");
                $this->tpl->parse("content.list.item.CSS_select");
            break;
            case 'file':
                //nothing
            break;
            default:
                $this->tpl->parse("content.list.item.image_zoom");
                $this->tpl->parse("content.list.item.image_view_full");
                $this->tpl->parse("content.list.item.default");
            break;
        }
            
        $this->tpl->parse("content.list.item");
    }
    
}
