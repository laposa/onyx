<?php
/**
 * Copyright (c) 2006-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onyx_Controller_Node_Content_Menu extends Onyx_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {
        
        /**
         * initialise node and get detail
         */
         
        require_once('models/common/common_node.php');
        
        $Node = new common_node();
        
        $node_data = $Node->nodeDetail($this->GET['id']);
        
        /**
         * get node component options
         */
         
        switch ($node_data['component']['template']) {
            case 'menu_SELECT':
                $template = 'component/menu_select';
                break;
            case 'menu_GRID':
                $template = 'component/menu_grid';
                break;
            case 'menu_STACK':
                $template = 'component/menu_stack';
                break;
            default:
                $template = 'component/menu';
        }
        
        if (is_numeric($node_data['component']['level'])) $level = $node_data['component']['level'];
        else $level = 0;
        if (is_numeric($node_data['component']['display_all'])) $display_all = $node_data['component']['display_all'];
        else $display_all = 0;
        if (is_numeric($node_data['component']['open'])) $open = $node_data['component']['open'];
        else $open = '';
        
        /**
         * image size
         */
        
        if ($template == 'component/menu_grid' || $template == 'component/menu_stack') {
            
            $image_o = $this->getImageSizeOptions($node_data);
                    
        }
        
        /**
         * pass to menu component
         */
         
        $Onyx_Request = new Onyx_Request("$template~id={$node_data['component']['node_id']}:display_strapline={$node_data['component']['display_strapline']}:level={$level}:expand_all={$display_all}:open={$open}:image_width={$image_o['width']}:image_height={$image_o['height']}:image_fill={$image_o['fill']}~");
        $this->tpl->assign("MENU", $Onyx_Request->getContent());
        $this->tpl->assign("NODE", $node_data);
        
        if ($node_data['display_title'])  $this->tpl->parse('content.title');

        return true;
    }
}
