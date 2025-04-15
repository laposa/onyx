<?php
/** 
 * Copyright (c) 2009-2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/comment.php');

class Onyx_Controller_Component_Comment_Add extends Onyx_Controller_Component_Comment {

    /**
     * custom comment action
     */
     
    public function customCommentAction($data, $options) {

        // enable captcha if captcha field is present in the template
        $this->enableCaptcha = (strpos($this->tpl->filecontents, 'comment-captcha_') !== FALSE);
        if ($this->enableCaptcha) $this->tpl->parse("content.comment_insert.invisible_captcha_field");
    
        $data['rating'] = 0;
        $this->displaySubmitForm($data, $options);
        
    }
    
}
