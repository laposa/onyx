<?php
/**
 * Copyright (c) 2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/comment_edit.php');

class Onxshop_Controller_Bo_Component_Ecommerce_Recipe_Review_Edit extends Onxshop_Controller_Bo_Component_Comment_Edit {

    /**
     * initialize comment
     */
     
    public function initializeComment() {
        
        require_once('models/ecommerce/ecommerce_recipe_review.php');
        $Comment = new ecommerce_recipe_review();
        
        return $Comment;
    }

    /**
     * save
     */
     
    public function saveComment($comment_data) {
    
        if ($this->Comment->updateComment($comment_data)) msg("Recipe review id={$comment_data['id']} updated");
        else msg("Recipe review id={$comment_data['id']} Update failed", 'error');

    }
    
}

