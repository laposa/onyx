<?php
/** 
 * Copyright (c) 2006-2019 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onxshop_Controller_Node_Content_Contact_Form extends Onxshop_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_node.php');
        
        $Node = new common_node();
        
        $node_data = $Node->nodeDetail($this->GET['id']);

        /* default values */
        if (!is_array($node_data['component'])) {
            $node_data['component'] = array();
            $node_data['component']['node_controller'] = $Node->conf['contact_form_default_template'];
            $node_data['component']['spam_protection'] = 'captcha_text_js';
        }

        if ($node_data['component']['sending_failed'] == '') $node_data['component']['sending_failed'] = 'The provided data are not valid!';
        if ($node_data['component']['text'] == '') $node_data['component']['text'] = "";
        
        $this->tpl->assign("NODE", $node_data);
         
        $template_folder = "component/contact_form/";
        $template_name = "$template_folder{$node_data['component']['node_controller']}";
        
        /**
         * check template file exists
         */
        
        
        if (!templateExists($template_name)) {
            
            // try fallback to old _contact_form folder in case it was locally created (used prior to Onxshop 1.7.6)
            $template_folder = "component/_contact_form/";
            $template_name = "$template_folder{$node_data['component']['node_controller']}";
            
            if (!templateExists($template_name)) {
                msg("selected template $template_name was not found");
                return false;
            }
        }
        
        
        /**
         * execute controller
         */
        $template_name = trim($template_name);
        $node_id = $node_data['id'];
        $mail_to = trim($node_data['component']['mail_to']);
        $mail_toname = trim($node_data['component']['mail_toname']);
        $spam_protection = trim($node_data['component']['spam_protection']);

        $Form = new Onxshop_Request("component/contact_form@$template_name&amp;node_id={$node_id}&amp;mail_to={$mail_to}&amp;mail_toname={$mail_toname}&amp;spam_protection={$spam_protection}");
        
        $this->tpl->assign("FORM", $Form->getContent());
        
        $reg_key = "form_notify_" . $node_data['id'];
        
        if (Zend_Registry::isRegistered($reg_key)) {
        
            if (Zend_Registry::get($reg_key) == 'sent') {
                //forward
                if ($node_data['component']['text']) msg($node_data['component']['text'], 'ok');
                if ($node_data['component']['href'] != '') onxshopGoTo($node_data['component']['href']);
            } else if (Zend_Registry::get($reg_key)  == 'failed') {
                msg($node_data['component']['sending_failed'], 'error');
                $this->tpl->parse('content.form');
            } else {
                $this->tpl->parse('content.form');
            }
        
        } else {
            
            $this->tpl->parse('content.form');
        
        }
        
        if ($node_data['display_title'])  $this->tpl->parse('content.title');

        return true;
    }
}
