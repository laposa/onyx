<?php
/**
 * Copyright (c) 2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/node/content/default.php');

class Onxshop_Controller_Node_Content_Adaptive extends Onxshop_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * check input: node id value must be numeric
         */

        $node_id = $this->GET['id'];
                 
        if (!is_numeric($node_id)) {
            msg("node/content/adaptive: id not numeric", 'error');
            return false;
        }

        /**
         * load $this->node_data
         */
         
        $this->loadNode($node_id);

        /**
         * show only if conditions met
         */
         
        if ($this->canDisplay()) {
         
            $this->processSubContent();
            $this->processContent();
            
        }

        return true;
    }

    /**
     * can display adaptive content as per configured conditions?
     */

    public function canDisplay() {

        $condition = $this->node_data['component']['condition'];

        //force visibility for admin, only when in edit or preview mode
        if ($_SESSION['fe_edit_mode'] == 'edit' || $_SESSION['fe_edit_mode'] == 'move') return true;
        else $force_admin_visibility = false;

        switch ($condition) {

            case "always":
                return true;

            case "random":
                $this->tpl->parse("content.random");
                return true;

            case "rotate":
                $this->tpl->parse("content.rotate");
                return true;

            case "customer_returning":
                return $this->isReturningCustomer();

            case "customer_new":
                return !$this->isReturningCustomer();

            case "customer_newsletter_subscribed":
                return $this->isSubscribed();
    
            case "customer_newsletter_not_subscribed":
                return !$this->isSubscribed();

        }

        msg("node/content/adaptive: unknown display condition '$condition'", 'error');
        return false;

    }

    /**
     * is current user a returning customer?
     */

    public function isReturningCustomer() {

        $period = 24 * 3600; // 24-hours
        $logged_in = ($_SESSION['client']['customer']['id'] > 0);
        $account_is_old = (time() - strtotime($_SESSION['client']['customer']['created']) > $period);

        $cookie_status = ($_COOKIE['visited_status'] > 0 && time() - $_COOKIE['visited_status'] > $period);

        return $logged_in && $account_is_old || $cookie_status;
    }

    /**
     * is current user subscribed to newletter?
     */
    
    public function isSubscribed() {

        $logged_in = ($_SESSION['client']['customer']['id'] > 0);
        $customer_newsletter = ($_SESSION['client']['customer']['newsletter'] > 0);

        $cookie_status = ($_COOKIE['newsletter_status'] & 1 == 1);

        return $logged_in && $customer_newsletter || $cookie_status;
    }

    /**
     * load node data
     */
     
    public function loadNode($node_id) {

        require_once('models/common/common_node.php');
        $this->Node = new common_node();
        $this->node_data = $this->Node->nodeDetail($node_id);

    }

    /**
     * processSubContent
     */
     
    public function processSubContent() {
        
        $children = $this->Node->parseChildren($this->node_data['id']);
        
        if (!is_array($children)) {
            
            msg("Adaptive content does't have any children", 'error', 1);
            return false;
        }
        
        $sub_content = '';
        
        foreach ($children as $item) {
            
            $sub_content = $sub_content . $item['content'];
            
        }
        
        $this->tpl->assign('SUB_CONTENT', $sub_content);
        
    }
}
