<?php
/** 
 * Copyright (c) 2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/ecommerce/product_review.php');

class Onyx_Controller_Component_Ecommerce_Product_Review_Approve extends Onyx_Controller_Component_Ecommerce_Product_Review {

    /**
     * custom comment action
     */
     
    public function customCommentAction($data, $options) {
    
        if ($this->checkEditPermission($data)) {
        
            if (is_numeric($this->GET['comment_id'])) $comment_id = $this->GET['comment_id'];
            else return false;
            
            if (is_numeric($this->GET['publish'])) $publish = $this->GET['publish'];
            else return false;
            
            $comment_data = $this->Comment->getDetail($comment_id);
            $comment_data['publish'] = $publish;
            
            if ($this->Comment->updateComment($comment_data)) {
            
                if ($publish == 1) msg("Product review ID $comment_id approved by client ID {$_SESSION['client']['customer']['id']}");
                else if ($publish == -1) msg("Product review ID $comment_id rejected by client ID {$_SESSION['client']['customer']['id']}");
                
                if ($publish == 1) $this->runCustomApproveAction($comment_id);
                onyxGoTo($_SESSION['referer'], 2);
            }
            
        }
        
        return true;
    }

    public function runCustomApproveAction($comment_id)
    {
        $request = new Onyx_Request("component/ecommerce/product_review_approve_action~comment_id=$comment_id~");
        return $request->getContent();
    }
    
}