<?php
/** 
 * Copyright (c) 2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/ecommerce/recipe_review.php');

class Onxshop_Controller_Component_Ecommerce_Recipe_Review_Approve extends Onxshop_Controller_Component_Ecommerce_Recipe_Review {

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
            
                if ($publish == 1) msg("Recipe review ID $comment_id approved by client ID {$_SESSION['client']['customer']['id']}");
                else if ($publish == -1) msg("Recipe review ID $comment_id rejected by client ID {$_SESSION['client']['customer']['id']}");
                onxshopGoTo($_SESSION['referer'], 2);
            }
            
        }
        
        return true;
    }
    
}