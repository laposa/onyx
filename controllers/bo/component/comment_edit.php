<?php
/**
 * Copyright (c) 2010-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_Comment_Edit extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $this->Comment = $this->initializeComment();
        
        /**
         * Save on request
         */
         
        if ($_POST['save']) {
        
            $this->saveComment($_POST['comment']);
            
        }
        
        /**
         * Display Detail
         */
         
        $this->displayComment($this->GET['id']);
        

        /**
         * destroy
         */
         
        $this->Comment = false;
        
        return true;
    }
    
    /**
     * initialize comment
     */
     
    public function initializeComment() {
        
        require_once('models/common/common_comment.php');
        $Comment = new common_comment();
        
        return $Comment;
    }

    /**
     * save
     */
     
    public function saveComment($comment_data) {
    
        if ($this->Comment->updateComment($comment_data)) msg("Comment id={$comment_data['id']} updated");
        else msg("Comment id={$comment_data['id']} Update failed", 'error');

    }
    
    /**
     * 
     */
    
    public function displayComment($id) {
     
        $comment_detail = $this->Comment->getDetail($id);

        if (count($comment_detail) > 0) {
            
            $this->tpl->assign("SELECTED_{$comment_detail['publish']}", "selected='selected'");
            
            $this->tpl->assign('COMMENT', $comment_detail);
        }
        
    }
}

