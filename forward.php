<?php
/** 
 * Copyright (c) 2006-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Forward extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        if ($this->GET['to']) {
            
            header("HTTP/1.1 301 Moved Permanently");
            onyxGoTo($this->GET['to']);
            
        } else if (is_numeric($this->GET['product_id'])) {
        
            header("HTTP/1.1 301 Moved Permanently");
            require_once('models/common/common_node.php');
            $Node = new common_node();

            $product_homepage = $Node->getProductNodeHomepage($this->GET['product_id']);
            
            if (is_array($product_homepage)) {
            
                onyxGoTo("/page/{$product_homepage['id']}");
            
            } else {
            
                msg("Product no longer available");
                onyxGoTo("/");
            
            }
            
        } else if (is_numeric($this->GET['recipe_id'])) {
        
            header("HTTP/1.1 301 Moved Permanently");
            require_once('models/common/common_node.php');
            $Node = new common_node();

            $recipe_homepage = $Node->getRecipeNodeHomepage($this->GET['recipe_id']);
            
            if (is_array($recipe_homepage)) {
            
                onyxGoTo("/page/{$recipe_homepage['id']}");
            
            } else {
            
                msg("Recipe no longer available");
                onyxGoTo("/");
            
            }
            
        } else if (is_numeric($this->GET['store_id'])) {
        
            header("HTTP/1.1 301 Moved Permanently");
            require_once('models/ecommerce/ecommerce_store.php');
            $Store = new ecommerce_store();

            $store_homepage = $Store->getStoreHomepage($this->GET['store_id']);
            
            if (is_array($store_homepage)) {
            
                onyxGoTo("/page/{$store_homepage['id']}");
            
            } else {
            
                msg("Store no longer available");
                onyxGoTo("/");
            
            }
        }

        return true;
    }
}
