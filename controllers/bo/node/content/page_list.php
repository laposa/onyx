<?php
/**
 * Copyright (c) 2013-2014 Onxshop Ltd (https://onxshop.com)
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

        $this->renderTeaserTemplatesDropdown();

    }

    /**
     * List existing teaser_*.html files in the template dropdown
     */
    function renderTeaserTemplatesDropdown() {
        $teaser_templates = [];

        $dir = new DirectoryIterator(ONXSHOP_PROJECT_DIR . "/templates/component");
        foreach ($dir as $fileinfo) {
            $filename = $fileinfo->getFilename();
            if (!$fileinfo->isDot() && substr($filename, 0, 7) == "teaser_") {
                $teaser_templates[$filename] = str_replace("teaser_", "", str_replace(".html", "", $filename));
            }
        }

        $dir = new DirectoryIterator(ONXSHOP_DIR . "/templates/component");
        foreach ($dir as $fileinfo) {
            $filename = $fileinfo->getFilename();
            if (!$fileinfo->isDot() && substr($filename, 0, 7) == "teaser_") {
                $teaser_templates[$filename] = str_replace("teaser_", "", str_replace(".html", "", $filename));
            }
        }
        foreach ($teaser_templates as $template) {
            $selected = '';
            if ($this->node_data['component']['template'] == $template) $selected = 'selected="selected"';
            $this->tpl->assign("TEASER_TEMPLATE", $template);
            $this->tpl->assign("SELECTED", $selected);
            $this->tpl->parse("content.teaser_template_item");
        }
    }

}
