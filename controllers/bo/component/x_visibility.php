<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component/x.php');
require_once('models/common/common_node.php');
require_once('models/common/common_scheduler.php');

class Onyx_Controller_Bo_Component_X_Visibility extends Onyx_Controller_Bo_Component_X {

    /**
     * main action
     */
     
    public function mainAction() {

        // get details
        $node = new common_node();
        $node_data = $node->nodeDetail($this->GET['node_id'] ?? $_POST['node']['id']);

        //publish
        if ($node_data['publish'] == 1) {
            $node_data['publish_check'] = 'checked="checked"';
        } else {
            $node_data['publish_check'] = '';
        }
        
        //display in menu
        $node_data["display_in_menu_select_" . $node_data['display_in_menu']] = "selected='selected'";

        //save
        if (isset($_POST['save'])) {
            $save_data = $_POST['node'];
            $scheduler = new common_scheduler();
            $jobs = $_POST['scheduler'];

            // TODO: Scheduler needs rework, saving works but deleting does not
            if (is_array($jobs)) {
                foreach ($jobs['controller'] as $i => $controller) {
                    $date = $jobs['date'][$i];
                    $time = $jobs['time'][$i];
                    $date = implode("-", array_reverse(explode("/", $date)));
                    $scheduled_time = strtotime($date . " " . $time);

                    $data = array(
                        'node_id' => $save_data['id'],
                        'node_type' => 'common_node',
                        'controller' => $controller,
                        'scheduled_time' => $scheduled_time,
                    );

                    $id = $scheduler->scheduleNewJob($data);

                    if ($id > 0) msg("Scheduled task saved as id=$id");
                }
            }

            if (isset($save_data['publish']) && ($save_data['publish'] == 'on' || $save_data['publish'] == 1)) $save_data['publish'] = 1;
            else $save_data['publish'] = 0;

            if($node->nodeUpdate($save_data)) {
                msg("{$node_data['node_group']} (id={$node_data['id']}) has been updated");
            } else {
                msg("Cannot update node {$node_data['node_group']} (id={$node_data['id']})", 'error');
            }
        }

        $this->tpl->assign('PUBLISHED', $node_data['publish'] == 1 ? 'Yes' : 'No');
        $this->tpl->assign('NODE', $node_data);

        parent::parseTemplate();

        return true;
    }
}   

