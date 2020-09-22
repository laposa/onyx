<?php
/** 
 * Copyright (c) 2010-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Component_Rating_Stars extends Onyx_Controller {

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
