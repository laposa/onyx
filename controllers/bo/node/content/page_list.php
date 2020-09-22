<?php
/**
 * Copyright (c) 2013-2019 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');

class Onxshop_Controller_Bo_Node_Content_Page_List extends Onxshop_Controller_Bo_Node_Content_Default {

    /**
     * post action
     */
     
    function post() {
        
        parent::post();
        
        //template
        $this->tpl->assign("SELECTED_template_{$this->node_data['component']['template']}", "selected='selected'");
        
    }

    function assign() {

        parent::assign();

        $this->renderPageListTemplatesDropdown();

    }

    /**
     * List existing page_list_*.html files in the template dropdown
     */
    function renderPageListTemplatesDropdown() {
        $page_list_templates = [];

        $dir = new DirectoryIterator(ONXSHOP_PROJECT_DIR . "/templates/component");
        foreach ($dir as $fileinfo) {
            $filename = $fileinfo->getFilename();
            if (!$fileinfo->isDot() && substr($filename, 0, 10) == "page_list_") {
                $page_list_templates[$filename] = str_replace("page_list_", "", str_replace(".html", "", $filename));
            }
        }

        $dir = new DirectoryIterator(ONXSHOP_DIR . "/templates/component");
        foreach ($dir as $fileinfo) {
            $filename = $fileinfo->getFilename();
            if (!$fileinfo->isDot() && substr($filename, 0, 10) == "page_list_") {
                $page_list_templates[$filename] = str_replace("page_list_", "", str_replace(".html", "", $filename));
            }
        }
        
        foreach ($page_list_templates as $template) {
            $selected = '';
            if ($this->node_data['component']['template'] == $template) $selected = 'selected="selected"';
            $this->tpl->assign("PAGE_LIST_TEMPLATE", $template);
            $this->tpl->assign("SELECTED", $selected);
            $this->tpl->parse("content.page_list_template_item");
        }
    }

}
