<?php
/** 
 * Copyright (c) 2008-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Component extends Onyx_Controller {

		public $Node;
		public $node_data;
     
    public function assignNodeData() {

        $this->Node = new common_node();
        $this->node_data = $this->Node->nodeDetail($this->GET['node_id']);

        return true;
    }

		public function parseTemplate() {
        
            $this->tpl->assign('NODE', $this->node_data);

            if (isset($_GET['edit']) && $_GET['edit'] == 'true') {
                $this->tpl->parse("content.edit");
            } else {
                $this->tpl->parse("content.preview");
            }
		}
}   

