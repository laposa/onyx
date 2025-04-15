<?php
/** 
 * Copyright (c) 2007-2024 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Node_Site_Default extends Onyx_Controller {

    public $Node;

    /**
     * main action
     */
     
    public function mainAction() {
        
        require_once('models/common/common_node.php');
        $this->Node = new common_node();
        
        /**
         * detail of the page underneath
         */
         
        $node_data = $this->Node->nodeDetail($this->GET['id'] ?? null);
        if ($node_data == false) $node_data = null;
        if (is_array($node_data) && $node_data['page_title'] == '') $node_data['page_title'] = $node_data['title'] ?? '';
        
        /**
         * prepare fallback page_title and browser_title
         */

         if (trim($node_data['page_title'] ?? '') == '') $node_data['page_title'] = $node_data['title'] ?? '';
         if (trim($node_data['browser_title'] ?? '') == '') $node_data['browser_title'] = $node_data['page_title'] ?? '';
         
        /**
         * when display_secondary_navigation is used, add extra css class "secondary-navigation"
         */
         
        $node_data['css_class'] = $node_data['css_class'] ?? '';
        if (!isset($node_data['display_secondary_navigation'])) $node_data['display_secondary_navigation'] = $GLOBALS['onyx_conf']['global']['display_secondary_navigation'];
        if ($node_data['display_secondary_navigation'] == 1) $node_data['css_class'] = "{$node_data['css_class']} secondary-navigation";
        else $node_data['css_class'] = "{$node_data['css_class']} no-secondary-navigation";
        
        /**
         * get node conf
         */
         
        $node_conf = $this->getNodeConfiguration();
        
        /**
         * assign to template
         */
            
        $this->tpl->assign("NODE", $node_data);
        $this->tpl->assign("NODE_CONF", $node_conf);
        
        /**
         * global navigation
         */
         
        if ($this->checkTemplateVariableExists('GLOBAL_NAVIGATION')) {
            
            $_Onyx_Request = new Onyx_Request("component/menu~id=" . $node_conf['id_map-global_navigation'] . ":level=1:open={$this->GET['id']}~");
            $this->tpl->assign('GLOBAL_NAVIGATION', $_Onyx_Request->getContent());
        
        }
        /**
         * primary navigation will show all sub items to the active page only if secondary navigation is hidden
         */
        
        if ($this->checkTemplateVariableExists('PRIMARY_NAVIGATION')) {
            
            if ($GLOBALS['onyx_conf']['global']['display_secondary_navigation'] == 1) {
                $_Onyx_Request = new Onyx_Request("component/menu~id=" . $node_conf['id_map-primary_navigation'] . ":level=1:expand_all=0:display_strapline=0:open={$this->GET['id']}~");
            } else {
                $_Onyx_Request = new Onyx_Request("component/menu~id=" . $node_conf['id_map-primary_navigation'] . ":level=3:expand_all=0:display_strapline=0:open={$this->GET['id']}~");
            }
            
            $this->tpl->assign('PRIMARY_NAVIGATION', $_Onyx_Request->getContent());
            
        }
        
        /**
         * footer navigation
         */
        
        if ($this->checkTemplateVariableExists('FOOTER_NAVIGATION')) {
             
            $_Onyx_Request = new Onyx_Request("component/menu~id=" . $node_conf['id_map-footer_navigation'] . ":level=1:open={$this->GET['id']}~");
            $this->tpl->assign('FOOTER_NAVIGATION', $_Onyx_Request->getContent());
        
        }
        /**
         * content side
         */
         
        if ($GLOBALS['onyx_conf']['global']['display_content_side'] == 1) {
            $_Onyx_Request = new Onyx_Request("node~id={$node_conf['id_map-content_side']}~");
            $this->tpl->assign('CONTENT_SIDE', $_Onyx_Request->getContent());
        }
        
        /**
         * content foot
         */
         
        if ($GLOBALS['onyx_conf']['global']['display_content_foot'] == 1) {
            $_Onyx_Request = new Onyx_Request("node~id={$node_conf['id_map-content_foot']}~");
            $this->tpl->assign('CONTENT_FOOT', $_Onyx_Request->getContent());
        }

        /**
         * fe_edit
         */
         
        if (Onyx_Bo_Authentication::getInstance()->isAuthenticated()) {
            $node_id = $this->GET['id'] ?? '';
            $_Onyx_Request = new Onyx_Request("bo/fe_edit~node_id={$node_id}~");
            $this->tpl->assign('FE_EDIT', $_Onyx_Request->getContent());
        }

        return true;
    }
    
    /**
     * get configuration
     */
     
    public function getNodeConfiguration() {
        
        $node_conf = $this->Node->conf;
        
        $node_conf = $this->localeOverwriteConfiguration($node_conf);
        
        return $node_conf;
        
    }
    
    /**
     * locale ovewrites for conf
     */
     
    public function localeOverwriteConfiguration($node_conf) {
        
        /*if (in_array(1049, $_SESSION['active_pages'])) {
            $node_conf['id_map-global_navigation']
            $node_conf['id_map-primary_navigation']
            $node_conf['id_map-footer_navigation']
            $node_conf['id_map-content_side']
            $node_conf['id_map-content_foot']
        }*/
    
        return $node_conf;  
    }
    
}
