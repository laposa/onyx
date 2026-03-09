<?php
require_once('controllers/bo/component/x.php');

class Onyx_Controller_Bo_Component_X_Recipe_Images extends Onyx_Controller_Bo_Component_X {

    public function mainAction() {
        if (is_numeric($this->GET['recipe_id'] ?? null)) $recipe_id = $this->GET['recipe_id'];
        else $recipe_id = false;

        if (is_numeric($recipe_id)) $this->tpl->parse('content.preview');

        return true;
    }
}
