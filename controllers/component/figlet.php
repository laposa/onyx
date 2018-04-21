<?php
/**
 * Copyright (c) 2009-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Component_Figlet extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * Get input variables
         */
        
        $text = $this->GET['text'];
        
        /**
         * Check input variables
         */
        
        if (!$text || trim($text) === '' || !preg_match('/[a-zA-Z0-9\ _-]/', $text) || strlen($text) > 256) $text = "Invalid String";
        
        /**
         * Initialize
         */
        
        require_once('Zend/Text/Figlet.php');
        $figlet = new Zend_Text_Figlet();
        
        /**
         * Process
         */
        
        $figlet_result  = $figlet->render($text);
        
        /**
         * Output
         */
        
        //we need to prevent striping of white spaces by xtemplate->assign function
        $figlet_result = "<pre>$figlet_result</pre>";
        
        $this->tpl->assign('RESULT', $figlet_result);

        return true;
    }
}
