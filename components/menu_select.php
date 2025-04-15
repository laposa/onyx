<?php
/** 
 * Copyright (c) 2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/component/menu.php');

class Onyx_Controller_Component_Menu_Select extends Onyx_Controller_Component_Menu {

    public function parseItem($item)
    {
        if (in_array($item['id'], $_SESSION['active_pages'])) {
            $item['selected'] = 'selected="selected"';
        }

        return parent::parseItem($item);
    }

}
