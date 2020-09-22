<?php
/** 
 * Copyright (c) 2007-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */


class Onyx_Controller_Component_Ecommerce_Product_Options extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
        
        require_once('models/ecommerce/ecommerce_product_taxonomy.php');
        $ProductTaxonomy = new ecommerce_product_taxonomy();
        
        $node_id =  $this->GET['id'];
        
        
        require_once('models/common/common_taxonomy.php');
        $Taxonomy = new common_taxonomy();
        
        
        
        //listing
        if (is_numeric($node_id)) {
        
            $current = $ProductTaxonomy->getRelationsToNode($node_id);
        
            
            if (is_array($current)) { 
        
                foreach ($current as $c_id) {
        
                    $taxonomy_data = $Taxonomy->taxonomyItemDetail($c_id);
                    
                    $option = $taxonomy_data['label'];
                    
                    $this->tpl->assign("OPTION", $taxonomy_data['label']);
                    
                    //check if it is a Product Options
                    if ($taxonomy_data['parent'] == $ProductTaxonomy->conf['options_id']) {
                    
                        $taxonomy_list = $Taxonomy->getChildren($taxonomy_data['id']);
                        
                        foreach ($taxonomy_list as $item) {
                        
                            if ($item['label']['publish'] == 1) {
                                $this->tpl->assign("ITEM", $item);
                                $this->tpl->parse("content.option.item");
                            }
                        }
                        
                        $this->tpl->parse("content.option");
                    }
                    
                }
            }
        }

        return true;
    }
}
