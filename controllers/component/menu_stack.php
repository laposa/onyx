<?php
/** 
 * Copyright (c) 2015-2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/component/menu.php');

class Onyx_Controller_Component_Menu_Stack extends Onyx_Controller_Component_Menu {
    
    /**
     * parseItem
     */
     
    public function parseItem($item)
    {
        if (array_key_exists('image_width', $this->GET)) $image_width = $this->GET['image_width'];
        else $image_width = null;
        if (array_key_exists('image_height', $this->GET)) $image_height = $this->GET['image_height'];
        else $image_height = null;
        if (array_key_exists('image_fill', $this->GET)) $image_fill = $this->GET['image_fill'];
        else $image_fill = null;
        
        $_Onyx_Request = new Onyx_Request("component/teaser_stack~target_node_id={$item['id']}:image_width=$image_width:image_height=$image_height:image_fill=$image_fill~");
        $item['teaser_content'] = $_Onyx_Request->getContent();

        return parent::parseItem($item);
    }

}
