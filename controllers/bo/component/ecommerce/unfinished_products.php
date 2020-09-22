<?php
/**
 * Unfinished products
 *
 * Copyright (c) 2008-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Bo_Component_Ecommerce_Unfinished_Products extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        //listing
        require_once('models/ecommerce/ecommerce_product.php');
        
        $Product = new ecommerce_product(); 
        
        //delete product
        //TODO make it safer
        if (is_numeric($this->GET['delete_product_id'])) {
            if ($Product->productDelete($this->GET['delete_product_id'])) msg("Product ID {$this->GET['delete_product_id']} has been deleted");
            else msg("Cannot delete product ID {$this->GET['delete_product_id']}", 'error');
        }
        
        //get unfinished products
        if ($unfinished = $Product->getUnfinishedProduct()) {
            foreach ($unfinished as $u) {
                $this->tpl->assign('ITEM', $u);
                $this->tpl->parse('content.unfinished.item');
            }
            $this->tpl->parse('content.unfinished');
        }

        return true;
    }
}
