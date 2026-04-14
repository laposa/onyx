<?php
require_once('controllers/bo/component/x.php');
require_once('models/common/common_node.php');
require_once('models/ecommerce/ecommerce_recipe.php');

class Onyx_Controller_Bo_Component_X_Recipe_Add extends Onyx_Controller_Bo_Component_X {

    public function mainAction() {

        $node = new common_node();
        $recipe = new ecommerce_recipe();

        $node_data = $node->detail($this->GET['node_id']);

        if ($_POST['save'] ?? false) {
            $recipe_data = $_POST['recipe'];

            if ($id = $recipe->insertRecipe($recipe_data)) {
                msg("Recipe has been added.");

                $node_data['content'] = $id;
                $node->nodeUpdate($node_data);
            } else {
                msg("Recipe has not been added.", 'error');
            }
        }

        return true;
    }
}
