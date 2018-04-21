<?php
/** 
 * Copyright (c) 2006-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');

class Onxshop_Controller_Bo_Node_Content_Print_article_list extends Onxshop_Controller_Bo_Node_Content_Default {

    /**
     * post action
     */

    function post() {

        parent::post();
        
        $node_id = $this->GET['id'];
        
        require_once('models/common/common_print_article.php');
        $File = new common_print_article();
        
        
        if ($_POST['add'] == 'add') {
            $_POST['file']['other'] = serialize($_POST['file']['other']);
            if ($File->insertFile($_POST['file'])) {
                msg('File inserted');
            }
        }
        
        if ($_POST['delete_file'] != '') {
            msg("Not implemented", "error");
            //can't use File->deleteFile(file_name),
            //use File->unlink(id) instead
            //if ($File->deleteFile($_POST['delete_file'])) $_POST['upload'] = 1;
        }
        
        
        if (is_numeric($_POST['update'])) {
            //$File->detail($_POST['update']);
            $_POST['file']['other'] = serialize($_POST['file']['other']);
            if ($File->update($_POST['file'])) msg('updated');
        }
        
        $files = $File->listFiles($node_id);
        
        foreach ($files as $f) {
            $this->tpl->assign('FILE', $f);
            if ($this->GET['type'] == 'RTE') $this->tpl->parse("content.item.RTE_select");
            $this->tpl->parse("content.item");
        }
        
        //print_r($_POST); exit;
        if ($_POST['upload'] && !$_POST['edit_file']) {
            $_POST['action'] = 'add';
        } else if ($_POST['edit_file'] || is_numeric($_POST['update'])) {
            $_POST['action'] = 'edit';
        }
        
        
        switch ($_POST['action']) {
            case 'edit';
            case 'update';
                $detail = $File->detail($_POST['edit_file']);
                if (is_array($detail)) {
                    $detail['other'] = unserialize($detail['other']);
                    if (!is_array($detail['other'])) $detail['other'] = 0;
                    $this->tpl->assign("SELECTED_{$detail['type']}", 'selected="selected"');
                    $this->tpl->assign('FILE', $detail);
                    $this->tpl->parse("content.file_edit");
                } else {
                    msg("Cant get info of {$_POST['edit_file']} from DB", 'error');
                }
            break;
            case 'add':
            default:
            if ($_POST['upload'] == 'new') {
                $_POST['file']['priority'] = 0;
                $_POST['file']['src'] = '';
            }
            $this->tpl->assign('FILE', $_POST['file']);
            $this->tpl->parse("content.file_upload");
            break;
        }

    
    }
}
