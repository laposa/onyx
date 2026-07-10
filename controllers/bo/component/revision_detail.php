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
            $revision_changes = unserialize($revision['content']);

            ksort($revision_changes);

            // TODO: add component name to revisions so we can easily look for specific changes?

            unset($revision_changes['modified']);
            $this->node_data['custom_fields'] = unserialize($this->node_data['custom_fields']);
            $revision_changes['custom_fields'] = json_decode($revision_changes['custom_fields']);
            $revision['author'] = $customer->getDetail($revision['customer_id']);

            // check for changes made 
            if($this->node_data != false) {
                foreach($revision_changes as $attribute => $value) {
                    if($this->node_data[$attribute] == $value) {
                        unset($revision_changes[$attribute]);
                    }
                }
            }

            // parse changes to template
            foreach($revision_changes as $attribute => $value) {

                if($attribute == 'custom_fields') {
                    $value = json_encode($value);
                }

                $change['attribute'] = ucwords(str_replace('_', ' ', $attribute));
                $change['current'] = $attribute == 'custom_fields' ? json_encode($this->node_data[$attribute] ?? '') : $this->node_data[$attribute];
                $change['revision'] = $value;

                $this->tpl->assign('CHANGE', $change);
                $this->tpl->parse('content.line');
            }

            $this->tpl->assign('REVISION', $revision);
        } else {
            $this->tpl->parse('content.empty');
        }
    }
    
}

