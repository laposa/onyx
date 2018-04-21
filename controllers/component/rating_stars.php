<?php
/** 
 * Copyright (c) 2010-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Component_Rating_Stars extends Onxshop_Controller {

    /** 
     * main action 
     */
     
    public function mainAction() {
    
        if (is_numeric($this->GET['rating'])) {
            $rating = $this->GET['rating'];
        } else {
            msg('component/comment_rating: GET.rating must be provided');
            return false;
        }
        
        for ($i = 0; $i < $rating; $i++) {
            $this->tpl->parse('content.star');
        }
        
        $this->tpl->assign('RATING', $rating);
                
        return true;
    }
    
}
