<?php
/**
 * Copyright (c) 2008-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Node_Edit extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        if (is_numeric($this->GET['id'])) $node_id = $this->GET['id'];
        else {
            msg('node_edit: node_id is not numeric', 'error');
            return false;
        }
    
        require_once('models/common/common_node.php');
        
        $Node = new common_node();
        $node_data = $Node->detail($node_id);
        $this->tpl->assign("NODE", $node_data);
        
        $_SESSION['active_pages'] = $Node->getActiveNodes($node_id, array('page', 'container'));
        $_SESSION['full_path'] = $Node->getFullPath($node_id);
        
        if ($_POST['node']['node_controller'] != '') $node_controller = $_POST['node']['node_controller'];
        else $node_controller = $node_data['node_controller'];
        
        $controller = "bo/node/{$node_data['node_group']}/{$node_controller}";
        
        if (getTemplateDir($controller . ".html") == '') {
            $controller_html = "bo/node/{$node_data['node_group']}/default";
        } else {
            $controller_html = $controller;
        }
        
        if (file_exists(ONXSHOP_DIR . "controllers/{$controller}.php") || file_exists(ONXSHOP_PROJECT_DIR . "controllers/{$controller}.php")) {
            $controller_php = $controller;
        } else {
            $controller_php = "bo/node/{$node_data['node_group']}/default";
        }
        
        $_Onxshop_Request = new Onxshop_Request("{$controller_php}@{$controller_html}&id={$node_id}&orig={$this->GET['orig']}&popup={$this->GET['popup']}", $this);
                
        $this->setContent($_Onxshop_Request->getContent());
        $this->tpl->assign("SUB_CONTENT", $this->content);

        if ($this->GET['ajax'] == 0) $this->tpl->parse('content.form');
        
        return true;
    }
    
}

