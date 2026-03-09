<?php
require_once('controllers/bo/component/x.php');
require_once('models/ecommerce/ecommerce_recipe.php');

class Onyx_Controller_Bo_Component_X_Recipe_Video extends Onyx_Controller_Bo_Component_X {

    public function mainAction() {
        $recipe_id = $this->GET['recipe_id'] ?? ($_POST['recipe']['id'] ?? null);

        $recipe = new ecommerce_recipe();
        $recipe_data = $recipe->getDetail($recipe_id);

        if (!$recipe_data) {
            return true;
        }

        if (isset($_POST['save'])) {
            if ($recipe->updateRecipe($_POST['recipe'])) {
                msg("{$recipe_data['node_group']} {$recipe_data['title']} (id={$recipe_data['id']}) has been updated");
            } else {
                msg("Cannot update {$recipe_data['node_group']} {$recipe_data['title']} (id={$recipe_data['id']})", 'error');
            }

            $recipe_data = $recipe->getDetail($recipe_id);
        }

        $this->tpl->assign('RECIPE', $recipe_data);
        parent::parseTemplate();

        return true;
    }
}
