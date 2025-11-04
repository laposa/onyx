<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component/x.php');
require_once('models/common/common_node.php');
class Onyx_Controller_Bo_Component_X_General_Info extends Onyx_Controller_Bo_Component_X {

    /**
     * main action
     */

    private $node_data;
     
    public function mainAction() {

        // get details
        $node = new common_node();
        $this->node_data = $node->nodeDetail($this->GET['node_id'] ?? $_POST['node']['id']);

        // display title
        if (!is_numeric($this->node_data['display_title'])) $this->node_data['display_title'] = $GLOBALS['onyx_conf']['global']['display_title'];

        if ($this->node_data['display_title'] == 1) {
            $this->node_data['display_title_check'] = 'checked="checked"';
        } else {
            $this->node_data['display_title_check'] = '';
        }

        // save
        if (isset($_POST['save'])) {
            // TODO: messages
            if($node->nodeUpdate($_POST['node'])) {
                msg("{$this->node_data['node_group']} (id={$this->node_data['id']}) has been updated");
            } else {
                msg("Cannot update node {$this->node_data['node_group']} (id={$this->node_data['id']})", 'error');
            }

            //trigger page refresh if node type changed
            if($_POST['node']['node_group'] != $this->node_data['node_group'] || $_POST['node']['node_controller'] != $this->node_data['node_controller']) {
                header("HX-Trigger: pageRefresh");
            }
        }

        $node_type = 
            ucwords(str_replace(['-', '_'], ' ', $this->node_data['node_group'])) . ' - ' . 
            ucwords(str_replace(['-', '_'], ' ', $this->node_data['node_controller']));
        $this->tpl->assign('NODE_TYPE', $node_type);

        $this->tpl->assign('NODE', $this->node_data);

        $this->parseNodeTypeList();

        parent::parseTemplate();

        return true;
    }

    /**
     * parse node type list
     */
    protected function parseNodeTypeList() {
    
        require_once('models/common/common_file.php');
        $file = new common_file();
        $node_types = [];
        
        // getting list of templates, joing project and onyx node dir
        $list = $file->getFlatArrayFromFsJoin("templates/node/", true);

        // create array of node types
        foreach ($list as $k=>$item) {

            // Groups
            if($list[$k]['parent'] == null && $list[$k]['node_group'] == 'folder') {
                $node_types[] = preg_replace('/\.html$/', '', $list[$k]['name']);
            }
            
            // Items
            if($list[$k]['parent'] != null && $list[$k]['node_group'] == 'file') {
                $node_types[$list[$k]['parent']][] = preg_replace('/\.html$/', '', $list[$k]['name']);
            }
        }

        //parse to template as select options
        foreach($node_types as $key => $value) {

            if($value == '' || $key == '' || is_numeric($key)) continue;

            sort($value);

            $this->tpl->assign('GROUP_LABEL', ucwords(str_replace(['-', '_'], ' ', $key)));
            $this->tpl->assign('GROUP_VALUE', $key);

            foreach($value as $item) {
                $this->tpl->assign('LABEL', ucwords(str_replace(['-', '_'], ' ', $item)));
                $this->tpl->assign('VALUE', $item);
                $this->tpl->assign('SELECTED', ($this->node_data['node_group'] == $key && $this->node_data['node_controller'] == $item) ? "selected='selected'" : '');
                $this->tpl->parse("content.edit.group.item");
            }


            $this->tpl->parse("content.edit.group");
        }

    }

}   

