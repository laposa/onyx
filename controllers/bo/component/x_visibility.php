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

        //publish checkbox on front-end
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

            $save_data['publish'] = $this->handlePublish($save_data, $node_data);
            $node->nodeUpdate($save_data);
        }

        $this->tpl->assign('PUBLISHED', $node_data['publish'] == 1 ? 'Yes' : 'No');
        $this->tpl->assign('NODE', $node_data);

        parent::parseTemplate();

        return true;
    }

        function handlePublish($save_data, $node_data) {
            $save_data['publish'] = isset($save_data['publish']) && ($save_data['publish'] == 'on') ? 1 : 0;

            //change of publish happens
            if($save_data['publish'] != $node_data['publish']) {
                switch ($node_data['node_controller']) {
                    case 'product':
                        require_once('models/ecommerce/ecommerce_product.php');
                        $product = new ecommerce_product();
                        $product_data = $product->productDetail($node_data['content']);
                        if ($product_data) {
                            $product_data['publish'] = $save_data['publish'];
                            $product->updateProduct($product_data);
                        }
                        break;
                    case 'store':
                        require_once('models/ecommerce/ecommerce_store.php');
                        $store = new ecommerce_store();
                        $store_data = $store->detail($node_data['content']);
                        if ($store_data) {
                            $store_data['publish'] = $save_data['publish'];
                            $store->storeUpdate($store_data);
                        }
                        break;
                    case 'recipe':
                        require_once('models/ecommerce/ecommerce_recipe.php');
                        $recipe = new ecommerce_recipe();
                        $recipe_data = $recipe->getDetail($node_data['content']);
                        if ($recipe_data) {
                            $recipe_data['publish'] = $save_data['publish'];
                            $recipe->updateRecipe($recipe_data);
                        }
                        break;
                    default:
                    break;
                }
                return $save_data['publish'];
            }
        }
}   

