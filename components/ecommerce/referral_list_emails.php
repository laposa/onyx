<?php
/** 
 * Copyright (c) 2012 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once "controllers/component/ecommerce/referral.php";

class Onyx_Controller_Component_Ecommerce_Referral_List_Emails extends Onyx_Controller_Component_Ecommerce_Referral {

    /**
     * main action
     */
     
    public function mainAction()
    {
        $this->init();
        if (!$this->securityCheck($this->customer_id)) return false;

        $this->displaySentEmails();

        return true;

    }

    protected function displaySentEmails()
    {
        $EmailForm = new common_email();
        $EmailForm->setCacheable(false);
        $email = pg_escape_string($_SESSION['client']['customer']['email']);
        $emails = $EmailForm->listing("email_from = '$email' AND template = 'referral_invite'", "created DESC");

        if (is_array($emails) && count($emails) > 0) {

            foreach ($emails as $email) {
                $this->tpl->assign("ITEM", $email);
                $this->tpl->parse("content.email_list.email");
            }

            $this->tpl->parse("content.email_list");

        }

    }

}
