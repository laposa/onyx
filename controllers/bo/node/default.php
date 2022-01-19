<?php
/**
 * Copyright (c) 2008-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Node_Default extends Onyx_Controller {

    var $name;
    var $Node;
    var $node_data;
    var $component_data;
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_node.php');
        $this->Node = new common_node();    
        $this->pre();
        if ($_POST['save']) $this->save();
        $this->detail();
        $this->post();
        $this->assign();

        return true;
    }
    

    /**
     * save
     *
     */
     
    function save() {
        
        $node_data = $_POST['node']; 

        if ($node_data['publish'] == 'on' || $node_data['publish'] == 1) $node_data['publish'] = 1;
        else $node_data['publish'] = 0;

        if ($node_data['display_title'] == 'on' || $node_data['display_title'] == 1) $node_data['display_title'] = 1;
        else $node_data['display_title'] = 0;
        
        if ($node_data['require_login'] == 'on' || $node_data['require_login'] == 1) $node_data['require_login'] = 1;
        else $node_data['require_login'] = 0;
        
        if ($node_data['require_ssl'] == 'on' || $node_data['require_ssl'] == 1) $node_data['require_ssl'] = 1;
        else $node_data['require_ssl'] = 0;
        
        if($this->Node->nodeUpdate($node_data)) {
            msg("{$node_data['node_group']} (id={$node_data['id']}) has been updated");
        } else {
            msg("Cannot update node {$node_data['node_group']} (id={$node_data['id']})", 'error');
        }
        

        //get whole detail
        $this->detail($_POST['node']['id']);
        //overwrite posted data

        $this->node_data = array_merge($this->node_data, $_POST['node']);
    }
    
    /**
     * get node detail
     *
     */
     
    function detail() {
        $this->node_data = $this->Node->nodeDetail($this->GET['id']);
        $this->tpl->assign('NODE_URL', translateURL("page/".$this->GET['id']));
        $CommonUriMapping = new common_uri_mapping();
        $this->tpl->assign('NODE_URL_LAST_SEGMENT', $CommonUriMapping->cleanTitle($this->node_data['title']));

        if (Onyx_Bo_Authentication::getInstance()->hasAnyPermission('advance_settings')) $this->tpl->assign('ALLOW_ADVANCE_SETTINGS_CSS_DISPLAY_PROPERTY', '');
        else $this->tpl->assign('ALLOW_ADVANCE_SETTINGS_CSS_DISPLAY_PROPERTY', 'none');
    }
    
    /**
     * assign to template
     *
     */
     
    function assign() {
        //display
        if ($this->node_data['publish'] == 1) {
            $this->node_data['publish_check'] = 'checked="checked"';
        } else {
            $this->node_data['publish_check'] = '';
        }

        //display title
        if (!is_numeric($this->node_data['display_title'])) $this->node_data['display_title'] = $GLOBALS['onyx_conf']['global']['display_title'];

        if ($this->node_data['display_title'] == 1) {
            $this->node_data['display_title_check'] = 'checked="checked"';
        } else {
            $this->node_data['display_title_check'] = '';
        }
        
        //require_login
        if ($this->node_data['require_login'] == 1) {
            $this->node_data['require_login_check'] = 'checked="checked"';
        } else {
            $this->node_data['require_login_check'] = '';
        }
        
        //require_ssl
        if ($this->node_data['require_ssl'] == 1) {
            $this->node_data['require_ssl_check'] = 'checked="checked"';
        } else {
            $this->node_data['require_ssl_check'] = '';
        }
        
        
        //display in menu
        $this->node_data["display_in_menu_select_" . $this->node_data['display_in_menu']] = "selected='selected'";

        //display permission
        if ($this->node_data['display_permission'] > 0) $this->node_data["display_permission_select_{$this->node_data['display_permission']}"] = "selected='selected'";
        else $this->node_data['display_permission_select_0'] = "selected='selected'";
        
        //display_permission_group_acl
        $this->renderDisplayPersmissionGroupAcl($this->node_data);
        

        // get the list of node types
        $Node_type = new Onyx_Request("bo/component/node_type_menu~id={$this->node_data['id']}:open={$this->node_data['node_controller']}:node_group={$this->node_data['node_group']}:expand_all=1~");
        $this->tpl->assign("NODE_TYPE", $Node_type->getContent());
        
        // item_image_properties.html
        $this->initiateImagePropertiesItem();
        
        // preview_token
        $this->tpl->assign('PREVIEW_TOKEN', $this->Node->getPreviewToken($this->node_data));
        
        // node_data
        $this->tpl->assign('NODE', $this->node_data);

    }
    
    /**
     * renderDisplayPersmissionGroupAcl
     */
     
    public function renderDisplayPersmissionGroupAcl($node_data) {
        
        require_once('models/client/client_group.php');
        $ClientGroup = new client_group();
        $client_group_list = $ClientGroup->listGroups();
        
        
        if (count($client_group_list) > 0) {
            
            foreach($client_group_list as $item) {
                
                //selected option for each group
                $selected = array();
                if (is_array($node_data['display_permission_group_acl'])) {
                
                    $item_value = $node_data['display_permission_group_acl'][$item['id']];
                    $selected['item_' . $item_value] = 'selected="selected"';
                
                } else {
                
                    $selected['item_-1'] = 'selected="selected"';
                    
                }
                
                $this->tpl->assign("SELECTED", $selected);
                
                $this->tpl->assign('GROUP', $item);
                
                $this->tpl->parse('content.display_permission_group_acl.item');
                
            }
        
            
            //selected option for Everyone
            $selected = array();
            if (is_array($node_data['display_permission_group_acl'])) {
                $item_value = $node_data['display_permission_group_acl'][0];
                $selected['item_' . $item_value] = 'selected="selected"';
            } else {
                $selected['item_-1'] = 'selected="selected"';
            }
            $this->tpl->assign("SELECTED", $selected);
            
            
        } else {
            $this->tpl->assign('DISABLE_EMPTY', 'disabled="disabled"');
            $this->tpl->parse('content.display_permission_group_acl.empty');
        }
        
        $this->tpl->parse('content.display_permission_group_acl');
    }
    
    /**
     * pre action
     */
     
    function pre() {
    
        if ($_POST['node']['display_secondary_navigation'] == 'on' || $_POST['node']['display_secondary_navigation'] == 1) $_POST['node']['display_secondary_navigation'] = 1;
        else $_POST['node']['display_secondary_navigation'] = 0;
        
        if (is_array($_POST['node']['component']) && array_key_exists('allow_comment', $_POST['node']['component'])) {
            if ($_POST['node']['component']['allow_comment'] == 'on') $_POST['node']['component']['allow_comment'] = 1;
            else $_POST['node']['component']['allow_comment'] = 0;
        }
    }
    
    /**
     * post (after) action
     */

    function post() {

        if (!is_numeric($this->node_data['display_secondary_navigation'])) $this->node_data['display_secondary_navigation'] = $GLOBALS['onyx_conf']['global']['display_secondary_navigation'];
        
        $this->node_data['display_secondary_navigation']        = ($this->node_data['display_secondary_navigation']) ? 'checked="checked"'      : '';
        $this->node_data['component']['allow_comment']        = ($this->node_data['component']['allow_comment']) ? 'checked="checked"'      : '';
        
        /**
         * checkbox status
         */
         
        foreach ($this->node_data['component'] as $k=>$c_item) {
            
            if ($c_item) $this->tpl->assign('CHECKED_node_component_'.$k, 'checked="checked"');
            
        }
        
        //layout styles
        $this->renderLayoutStyles();
    }
    
    /**
     * layout styles
     */
     
    private function renderLayoutStyles() {
    
        $styles = $this->getLayoutStyles();
        
        foreach ($styles as $k=>$style) {
            
            $style['key'] = $k;
            $this->tpl->assign("STYLE", $style);
            
            if ($this->node_data['layout_style'] == $style['key']) $this->tpl->assign("SELECTED", "selected='selected'");
            else $this->tpl->assign("SELECTED", "");
            
            $this->tpl->parse("content.style.item");
        }
        $this->tpl->parse("content.style");
    }
    
    /**
     * get styles
     */
     
    public function getLayoutStyles() {
    
        require_once('conf/node_layout_style.php');
        
        return $layout_style['page']['styles'];
    }
    
    
    /**
     * initiateImagePropertiesItem
     */
     
    public function initiateImagePropertiesItem() {
        
        require_once('models/common/common_image.php');
        $common_image_conf = common_image::initConfiguration();
        $this->tpl->assign('IMAGE_CONF', $common_image_conf);
        
        /**
         * image width
         */
        
        if ($this->node_data['component']['image_width'] == 0) {
            
            $this->tpl->assign("SELECTED_image_width_original", "selected='selected'");
            $this->node_data['component']['image_width'] = $this->getLargestAssociatedImageWidth($this->node_data['id']);;
        
        } else {
        
            $this->tpl->assign("SELECTED_image_width_custom", "selected='selected'");
        
        }
        
        /**
         * image ratio constrain
         */
         
        $this->tpl->assign("SELECTED_image_constrain_{$this->node_data['component']['image_constrain']}", "selected='selected'");
        
        
        
        /**
         * main image width (TODO merge with image_width)
         */
        
        if ($this->node_data['component']['main_image_width'] == 0) {
            
            $this->tpl->assign("SELECTED_main_image_width_original", "selected='selected'");
            $this->node_data['component']['main_image_width'] = $this->getLargestAssociatedImageWidth($this->node_data['id']);
            
        } else {
            
            $this->tpl->assign("SELECTED_main_image_width_custom", "selected='selected'");
        
        }
        
        /**
         * main image ratio constrain (TODO: merge with image_width)
         */
        
        $this->tpl->assign("SELECTED_main_image_constrain_{$this->node_data['component']['main_image_constrain']}", "selected='selected'");
        
        /**
         * fill option
         */
         
        $this->tpl->assign("SELECTED_image_fill_{$this->node_data['component']['image_fill']}", "selected='selected'");
        
    }
    
    /**
     * getLargestAssociatedImageWidth
     */
     
    public function getLargestAssociatedImageWidth($node_id) {
        
        if (!is_numeric($node_id)) return false;
        
        require_once('models/common/common_image.php');
        $Image = new common_image();
        
        /**
         * if not set, round to larges associated image with
         */
             
        $image_list = $Image->listFiles($this->node_data['id']);
        
        $image_width = 9999;
        
        foreach ($image_list as $item) {
            if ($item['imagesize']['width'] < $image_width) $image_width = ($item['imagesize']['width'] - $item['imagesize']['width'] % 5);
        }
        
        if ($image_width == 9999) $image_width = $this->getDefaultImageWidth(); // default value
        
        return $image_width;
        
    }
    
    /**
     * getDefaultImageWidth
     */
    
    public function getDefaultImageWidth() {
        
        return 800;
        
    }
}

        
