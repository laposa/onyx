<?php
/** 
 * Copyright (c) 2008-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Component_CSS_Edit extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        /**
         * CSS MANAGEMENT
         */
        
        if ($_POST['configuration']['global']['css']) {
            require_once('models/common/common_configuration.php');
            $Configuration = new Common_configuration();
            
            if ($Configuration->saveConfig('global', 'css', $_POST['configuration']['global']['css'])) {
                $GLOBALS['onxshop_conf']['global']['css'] = $_POST['configuration']['global']['css'];
                msg("CSS has been updated");
            }
        } else {
            if (is_numeric($this->GET['id'])) $this->tpl->parse('content.form.refresh_opener');
            $this->tpl->parse("content.form");
        }
        
        

        return true;
    }
}   

