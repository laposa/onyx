<?php
/**
 * Copyright (c) 2014-2022 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Bo_Component_Revision_Detail extends Onyx_Controller {

    /**
     * main action
     */

    private $node_data;
     
    public function mainAction() {
    
        require_once('models/common/common_revision.php');
        $node = new common_node();
        $revision = new common_revision();

        $this->node_data = $node->nodeDetail($this->GET['node_id'] ?? $_POST['node']['id']);
        $this->node_data['custom_fields'] = serialize($this->node_data['custom_fields']);

        if (in_array($this->GET['object'], common_revision::getAllowedRevisionObjects())) $object = $this->GET['object'];
        $selected_id = $this->GET['revision_id'];

        $this->parseDetail($selected_id);

        return true;
    }

    /**
     * parse
     */
    
    public function parseDetail($id) {
        
        if (is_numeric($id)) {
            require_once('models/client/client_customer.php');
            require_once('models/common/common_revision.php');
            $customer = new client_customer();
            $revision = new common_revision();

            $revision = $revision->getRevisionById($id);
            $revision_changes['content'] = unserialize($revision['content']);

            ksort($revision_changes['content']);

            // TODO: add component name to revisions so we can easily look for specific changes?

            unset($revision_changes['content']['modified']);
            $this->node_data['custom_fields'] = serialize($this->node_data['custom_fields']);

            // check for changes made 
            if($this->node_data != false) {
                foreach($revision_changes['content'] as $attribute => $value) {
                    if($this->node_data[$attribute] == $value) {
                        unset($revision_changes['content'][$attribute]);
                    }
                }
            }

            $revision_changes['author'] = $customer->getDetail($revision_changes['content']['customer_id']);

            // parse changes to template
            foreach($revision_changes['content'] as $attribute => $value) {
                $this->tpl->assign('LINE', $attribute.": ".$value);
                $this->tpl->parse('content.line');
            }

            $this->tpl->assign('REVISION', $item);
        } else {
            $this->tpl->parse('content.empty');
        }
    }
    
}

