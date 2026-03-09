<?php
require_once('controllers/bo/component/x.php');
require_once('models/ecommerce/ecommerce_recipe_ingredients.php');
require_once('models/ecommerce/ecommerce_product.php');
require_once('models/ecommerce/ecommerce_recipe.php');

class Onyx_Controller_Bo_Component_X_Recipe_Ingredients extends Onyx_Controller_Bo_Component_X {

    public function mainAction() {

        $Ingredients = new ecommerce_recipe_ingredients();
        $Product = new ecommerce_product();
        $Recipe = new ecommerce_recipe();

        $recipe_id = $this->GET['recipe_id'] ?? ($_POST['recipe']['id'] ?? null);
        $recipe_data = $Recipe->getDetail($recipe_id);

        if (!$recipe_data) {
            return true;
        }
        
        if (isset($_POST['save'])) {
            $current = $Ingredients->listing("recipe_id = $recipe_id");
            $submitted = $_POST['ingredients'] ?? [];
            $keep = [];

            if (is_array($submitted)) {
                foreach (array_keys($submitted) as $submitted_id) {
                    if (is_numeric($submitted_id)) $keep[] = (int) $submitted_id;
                }
            }

            foreach ($current as $c) {
                if (!in_array((int) $c['id'], $keep)) $Ingredients->delete($c['id']);
            }

            if (is_array($submitted)) {
                foreach ($submitted as $ingredient_id => $item) {
                    $ingredient = [];
                    $ingredient['recipe_id'] = (int) $recipe_id;
                    $ingredient['product_variety_id'] = $item['product_variety_id'];
                    $ingredient['quantity'] = $item['quantity'];
                    $ingredient['units'] = $item['units'];
                    $ingredient['notes'] = $item['notes'];
                    $ingredient['group_title'] = $item['group_title'];

                    if (is_numeric($ingredient_id)) {
                        $ingredient['id'] = $ingredient_id;
                        $Ingredients->update($ingredient);
                    } else {
                        $Ingredients->insert($ingredient);
                    }
                }
            }
        }

        $this->tpl->assign('RECIPE', array('id' => $recipe_id));

        $ingredients = $Ingredients->getIngredientsForRecipe($recipe_id);

        if (is_array($ingredients) && count($ingredients) > 0) {
            foreach ($ingredients as $ingredient) {
                $this->tpl->assign('ITEM', $ingredient);
                $this->tpl->parse('content.preview.item');
            }
        } else {
            $this->tpl->parse('content.preview.empty');
        }

        $units = $Ingredients->getUnits();
        $products = $Product->getProductListForDropdown();

        $this->parseUnits($units, false, 'content.edit.row_template.unit');
        $this->parseIngredients($products, false, 'content.edit.row_template.product');
        $this->tpl->parse('content.edit.row_template');

        $current = $Ingredients->listing("recipe_id = $recipe_id");
        if (is_array($current)) {
            foreach ($current as $ingredient) {
                $this->tpl->assign('ITEM', $ingredient);
                $this->parseUnits($units, $ingredient['units'], 'content.edit.item.unit');
                $this->parseIngredients($products, $ingredient['product_variety_id'], 'content.edit.item.product');
                $this->tpl->parse('content.edit.item');
            }
        }

        parent::parseTemplate();

        return true;
    }

    protected function parseUnits(&$units, $active, $block) {
        if (!is_array($units)) return;

        foreach ($units as $unit) {
            $unit_option = $unit;
            $unit_option['selected'] = ($active == $unit['id']) ? 'selected="selected"' : '';
            $this->tpl->assign('UNIT', $unit_option);
            $this->tpl->parse($block);
        }
    }

    protected function parseIngredients(&$products, $active, $block) {
        if (!is_array($products)) return;

        foreach ($products as $product) {
            $product_option = array(
                'id' => $product['id'],
                'name' => $product['product_name'] . ' - ' . $product['variety_name'],
                'sku' => $product['sku'],
                'class' => ($product['variety_publish'] == 0 || $product['product_publish'] == 0) ? 'disabled' : '',
                'selected' => ($active == $product['id']) ? 'selected="selected"' : ''
            );
            $this->tpl->assign('PRODUCT_OPTION', $product_option);
            $this->tpl->parse($block);
        }
    }
}
