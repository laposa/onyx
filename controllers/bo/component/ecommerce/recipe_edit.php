<?php
/** 
 * Copyright (c) 2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Bo_Component_Ecommerce_Recipe_Edit extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        // initialize
        require_once('models/ecommerce/ecommerce_recipe.php');
        $Recipe = new ecommerce_recipe();
        
        // save      
        if ($_POST['save']) {
        
            // update recipe
            if($recipe_id = $Recipe->updateRecipe($_POST['recipe'])) {
            
                msg("Recipe ID=$recipe_id updated");
                
                // forward to recipe list main page and exit
                onyxGoTo("/backoffice/recipes");
                return true;
            }
        }
        
        // recipe detail
        $recipe = $Recipe->detail($this->GET['id']);
        $recipe['publish'] = ($recipe['publish'] == 1) ? 'checked="checked" ' : '';
        $this->tpl->assign('RECIPE', $recipe);

        return true;
    }
}   
            
