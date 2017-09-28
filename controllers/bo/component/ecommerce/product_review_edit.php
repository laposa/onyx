<?php
/**
 * Copyright (c) 2010-2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/comment_edit.php');

class Onxshop_Controller_Bo_Component_Ecommerce_Product_Review_Edit extends Onxshop_Controller_Bo_Component_Comment_Edit {

    /**
     * initialize comment
     */
     
    public function initializeComment() {
        
        require_once('models/ecommerce/ecommerce_product_review.php');
        $Comment = new ecommerce_product_review();

        return $Comment;
    }

    /**
     * save
     */
     
    public function saveComment($comment_data) {
    
        $original_comment = $this->Comment->detail($comment_data['id']);

        if ($this->Comment->updateComment($comment_data)) {

            msg("Product review id={$comment_data['id']} updated");

            if ($this->hasBeenPublished($original_comment['publish'], $comment_data['publish'])) {

                $this->runCustomApproveAction($comment_data['id']);
            }

        } else {

            msg("Product review id={$comment_data['id']} Update failed", 'error');

        }

    }

    public function hasBeenPublished($original_state, $new_state)
    {
        return ($original_state != 1 && $new_state == 1);
    }

    public function runCustomApproveAction($comment_id)
    {
        $request = new Onxshop_Request("component/ecommerce/product_review_approve_action~comment_id=$comment_id~");
        return $request->getContent();
    }
}
