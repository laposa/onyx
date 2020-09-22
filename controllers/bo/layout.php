<?php
/** 
 * Copyright (c) 2006-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 * not used?
 */

class Onyx_Controller_Bo_Layout extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        /*
        $layout_mapping = unserialize(urldecode($this->GET['layout_mapping']));
        
        foreach ($layout_mapping as $id=>$lm) {
            $_Onyx_Request = new Onyx_Request("page&id=$lm");
            //$to = urlencode("xhtml.admin.bo/pages.bo/node_edit&id={$this->GET['id']}");
            //$content[$id] = $_Onyx_Request->getContent() . "<a class='onyx_button' href='index.php?request={$this->parent_request}.bo/pages.bo/content_edit&id=$lm&page_id={$this->GET['id']}'><span>edit</span></a>";
            $content[$id] = "<div class='onyx_editable'>" . $_Onyx_Request->getContent() . "</div>";
        }
        
        $this->tpl->assign("CONTENT", $content);
        */

        return true;
    }
}
