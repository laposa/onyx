<?php
/** 
 *
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
require_once('controllers/bo/component/x.php');

class Onyx_Controller_Bo_Component_X_Node_Type_Menu extends Onyx_Controller_Bo_Component_X {
    
    public function mainAction() {

        require_once('models/common/common_file.php');
        $file = new common_file();
        $node = new common_node();

        $node_types = [];
        $node_data = $node->nodeDetail($this->GET['node_id']);
        //add new node settings
        if(!$node_data) {
            $node_data['node_group'] = 'page';
            $node_data['node_controller'] = 'default';
        }
        
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
                $this->tpl->assign('SELECTED', ($node_data['node_group'] == $key && $node_data['node_controller'] == $item) ? "selected='selected'" : '');
                $this->tpl->parse("content.group.item");
            }

            
            $this->tpl->parse("content.group");
        }

        $this->tpl->assign('NODE', $node_data);

        return  true;
    } 
}
