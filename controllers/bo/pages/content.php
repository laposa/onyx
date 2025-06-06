<?php
/**
 * Content controller
 *
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 * Mostly duplicate from Pages Controller, but needed to load initial content
 */

class Onyx_Controller_Bo_Pages_Content extends Onyx_Controller {
    private $Node;
    
    /**
     * main action
     */
     
    public function mainAction() {

        return true;
    }
    
    /**
     * get content_id
     */
     
    public function getContentId() {
        
        if (is_numeric($this->GET['id'] ?? null)) {
            $content_id = $this->GET['id'];
        } else if (count($_SESSION['active_pages']) > 0) {
            $last_page_id = $this->Node->getLastParentPage($_SESSION['active_pages']);
            $content_id = $last_page_id;
        } else {
            $content_id = 0;
        }
        
        return $content_id;
        
    }
    /**
     * hook before content tags parsed
     */

    function parseContentTagsBeforeHook() {

        require_once('models/common/common_node.php');
        $this->Node = new common_node();
        
        $content_id = $this->getContentId();
        
        $_SESSION['active_pages'] = $this->Node->getActiveNodes($content_id);
        $_SESSION['full_path'] = $this->Node->getFullPath($content_id);
        
        return true;

    }
}
