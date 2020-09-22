<?php
/** 
 * Copyright (c) 2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Component_Ecommerce_Recipe_In_Node extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_node.php');
        require_once('models/ecommerce/ecommerce_recipe.php');
        
        $Node = new common_node();
        $Recipe = new ecommerce_recipe();
        
        $recipe_id =  $this->GET['id'];
        
        /**
         * find recipe homepage
         */
         
        $recipe_homepage = $Recipe->getRecipeHomepage($recipe_id);
        
        /**
         * allow to insert new
         */
         
        if (!is_array($recipe_homepage) && !is_numeric($this->GET['add_to_parent'])) {
            $this->tpl->parse('content.not_exists');
        }
        
        /**
         * move page if requested
         */
         
        if (is_numeric($this->GET['add_to_parent'])) {
            if (is_array($recipe_homepage )) {
                //moving
                $recipe_homepage = $this->moveRecipeNode($recipe_id, $this->GET['add_to_parent']);
            } else {
                //insert new
                $recipe_homepage = $this->insertNewRecipeToNode($recipe_id, $this->GET['add_to_parent']);
            }
            
            
        }
        
        
        /**
         * display recipe homepage detail
         */
         
        if (is_array($recipe_homepage)) {
            
            //parent detail
            $parent_detail = $Node->detail($recipe_homepage['parent']);
            $this->tpl->assign("PARENT_DETAIL", $parent_detail);
            
            //breadcrumb
            $_Onyx_Request = new Onyx_Request("component/breadcrumb~id={$recipe_homepage['id']}:create_last_link=1~");
            $this->tpl->assign('BREADCRUMB', $_Onyx_Request->getContent());
            
            //children node list
            $_Onyx_Request = new Onyx_Request("bo/component/node_list~id={$recipe_homepage['id']}:node_group=content~");
            $this->tpl->assign('NODE_LIST', $_Onyx_Request->getContent());
            
            //parse
            $this->tpl->parse('content.recipe_node');
        }
        
        return true;
    }
    
    /**
     * insert recipe to node
     */
    
    function insertNewRecipeToNode($recipe_id, $parent_id) {
    
        if (!is_numeric($recipe_id)) return false;
        if (!is_numeric($parent_id)) return false;
        
        $Node = new common_node();
        $Recipe = new ecommerce_recipe();
        
        /**
         * get recipe detail
         */
         
        $recipe_detail = $Recipe->detail($recipe_id);
         
        /**
         * prepare node data
         */
         
        $recipe_node['title'] = $recipe_detail['title'];
        $recipe_node['parent'] = $parent_id;
        $recipe_node['parent_container'] = 0;
        $recipe_node['node_group'] = 'page';
        $recipe_node['node_controller'] = 'recipe';
        $recipe_node['content'] = $recipe_id;
        //$recipe_node['layout_style'] = $Node->conf['page_recipe_layout_style'];
        //this need to be updated on each recipe update
        $recipe_node['publish'] = $recipe_detail['publish'];
        
        /**
         * insert node
         */
         
        if ($recipe_homepage = $Node->nodeInsert($recipe_node)) {
            msg("Recipe has been added into the node", 'ok');
            return $recipe_homepage;
        } else {
            msg("Can't add recipe to node.");
            return false;
        }
    }
    
    /**
     * move recipe node
     */
     
    function moveRecipeNode($recipe_id, $parent_id) {
    
        if (!is_numeric($recipe_id)) return false;
        if (!is_numeric($parent_id)) return false;
        
        $Node = new common_node();
        $Recipe = new ecommerce_recipe();
        
        /**
         * get current detail
         */
         
        $recipe_homepage = $Recipe->getRecipeHomepage($recipe_id);
         
        /**
         * modify node data
         */
        
        $recipe_homepage['parent'] = $parent_id;
        
        if ($Node->nodeUpdate($recipe_homepage)) {
            msg("Recipe node has been updated", 'ok');
            return $recipe_homepage;
        } else {
            msg("Can't update recipe node.");
            return false;
        }
        
    }
}
