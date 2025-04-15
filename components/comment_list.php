<?php
/** 
 * Copyright (c) 2010-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/comment.php');

class Onyx_Controller_Component_Comment_List extends Onyx_Controller_Component_Comment {
    
    /**
     * custom comment action
     */
     
    public function customCommentAction($data, $options) {
        
        /**
         * list comments
         */
        
        $this->listComments($data['node_id'], $options);
        
    }
    
}
