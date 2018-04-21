<?php
/** 
 * Copyright (c) 2012-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Client_Notify extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $node_id = $this->GET['node_id'];
        if (!is_numeric($node_id)) return false;
        
        if ($this->GET['mail_template'] && trim($this->GET['mail_template']) != '') $this->mail_template = $this->GET['mail_template'];
        else $this->mail_template = 'notify_author';
        
        $this->tpl->assign('MAIL_TEMPLATE', $this->mail_template);
        
        require_once('models/common/common_node.php');
        $Node = new common_node();
        
        $node_detail = $Node->getDetail($node_id);
        
        $this->tpl->assign('NODE', $node_detail);
        $this->tpl->assign('CONF', $GLOBALS['onxshop_conf']);
        
        if ($this->GET['confirm'] == 1) {
            $this->sendEmail($node_detail);
        } else {
            $this->previewEmail($node_detail);
        }
        
        $this->showPreviouslySentEmails($node_detail['author_detail']['email']);
        
        return true;
        
    }
    
    /**
     * showPreviouslySentEmails
     */
     
    public function showPreviouslySentEmails($email_address) {
        
        if (!$email_address) return false;
        
        require_once('models/common/common_email.php');
        $Email = new common_email();
        
        $list = $Email->listing("email_recipient = '{$email_address}'");
        
        foreach ($list as $item) {
            $this->tpl->assign('ITEM', $item);
            $this->tpl->parse('content.outbox.item');
        }
        
        if (count($list) > 0) $this->tpl->parse('content.outbox');
    }
    
    /**
     * sendEmail
     */
     
    public function sendEmail($node_detail) {
    
        if (!is_array($node_detail)) return false;
        
        require_once('models/common/common_email.php');
        $EmailForm = new common_email();
        
        //setup mail data
        $content = print_r($node_detail, true);
        $mail_to = $node_detail['author_detail']['email'];
        $mail_to_name = $node_detail['author_detail']['name'];
        $mail_from = false; // use system default
        $mail_from_name = false; // use system default
        
        $_GET['node_id'] = $node_detail['id'];
        
        //send mail
        if ($EmailForm->sendEmail($this->mail_template, $content, $mail_to, $mail_to_name, $mail_from, $mail_from_name)) {
            msg("Email to $mail_to has been sent");
            return true;
        } else {
            msg("Cannot send email", 'error');
            return false;
        }
        
    }
    
    /**
     * preview email
     */
     
    public function previewEmail($node_detail) {
        
        $this->tpl->parse('content.preview_email');
        
    }
}
