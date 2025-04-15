<?php
/** 
 * Copyright (c) 2014-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once 'controllers/bo/backoffice_menu.php';

class Onyx_Controller_Bo_Backoffice_Menu_Advanced extends Onyx_Controller_Bo_Backoffice_Menu {

    /**
     * main action
     */
     
    public function mainAction() {

        /**
         * Manage Advanced Menu
         */
        if (preg_match('/backoffice/', $_SERVER['REQUEST_URI'])) {
            $active_array = explode("/", $_SERVER['REQUEST_URI']);
            $active = preg_replace("/\?.*$/", "", $active_array[3] ?? '');
        } else {
            $active = 'configuration';
        }

        $this->tpl->assign("ACTIVE_{$active}", 'active');

        /**
         * ACL
         */

        $auth = Onyx_Bo_Authentication::getInstance();
        $isEcommerce = $auth->isEcommerce();

        if ($auth->hasAnyPermission('database')) $this->tpl->parse('content.database');
        if ($auth->hasAnyPermission('templates')) $this->tpl->parse('content.templates');
        if ($auth->hasAnyPermission('scheduler')) $this->tpl->parse('content.scheduler');
        if ($auth->hasAnyPermission('search_index')) $this->tpl->parse('content.search_index');
        if ($auth->hasAnyPermission('tools')) $this->tpl->parse('content.tools');
        if ($auth->hasAnyPermission('logs')) $this->tpl->parse('content.logs');
        if ($auth->hasAnyPermission('configuration')) $this->tpl->parse('content.configuration');
        if ($auth->hasAnyPermission('currency') && $isEcommerce) $this->tpl->parse('content.currency');
        if ($auth->hasAnyPermission('api') && $isEcommerce) $this->tpl->parse('content.api');

        return true;
        
    }
}
