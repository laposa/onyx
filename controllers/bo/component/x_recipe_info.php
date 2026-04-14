<?php
require_once('controllers/bo/component/x.php');
require_once('models/ecommerce/ecommerce_recipe.php');
require_once('models/common/common_node.php');

class Onyx_Controller_Bo_Component_X_Recipe_Info extends Onyx_Controller_Bo_Component_X {

    public function mainAction() {
        // node details
        $node = new common_node();
        $node_data = $node->detail($this->GET['node_id']);

        // get recipe details from node content in order to avoid issues with refreshing & check whether recipe exists
        $recipe = new ecommerce_recipe();
        $recipe_data = $recipe->getDetail(is_numeric($node_data['content']) ? $node_data['content'] : null);
        
        // save
        if (isset($_POST['save'])) {

            $recipe->updateRecipe($_POST['recipe']);
            return true;
            
        }
        
        $this->tpl->assign('NODE', $node_data);

        if ($recipe_data) {
            $this->tpl->assign('RECIPE', $recipe_data);
            parent::parseTemplate();
        } else $this->tpl->parse("content.missing_recipe");

        return true;
    }
}
