<?php
/**
 * Copyright (c) 2006-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/node/default.php');

class Onyx_Controller_Bo_Node_Site_Default extends Onyx_Controller_Bo_Node_Default {

    /**
     * pre action
     */

    function pre() {
        if ($_POST['node']['display_secondary_navigation'] == 'on' || $_POST['node']['display_secondary_navigation'] == 1) $_POST['node']['display_secondary_navigation'] = 1;
        else $_POST['node']['display_secondary_navigation'] = 0;
    }

    /**
     * post action
     */
     
    function post() {

        if (!is_numeric($this->node_data['display_secondary_navigation'])) $this->node_data['display_secondary_navigation'] = $GLOBALS['onyx_conf']['global']['display_secondary_navigation'];
        $this->node_data['display_secondary_navigation']        = ($this->node_data['display_secondary_navigation']) ? 'checked="checked"'      : '';

        //style
        $styles = array(
            'twenty-eighty',
            'thirty-seventy',
            'fifty-fifty',
            'seventy-thirty',
            'eighty-twenty'
        );
    
        foreach ($styles as $style) {
            $this->tpl->assign("STYLE", $style);
            if ($this->node_data['layout_style'] == $style) $this->tpl->assign("SELECTED", "selected='selected'");
            else $this->tpl->assign("SELECTED", "");
            $this->tpl->parse("content.style.item");
        }
        $this->tpl->parse("content.style");
    }
}
