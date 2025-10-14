<?php
/** 
 * Copyright (c) 2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */
require_once('controllers/bo/component.php');

class Onyx_Controller_Bo_Component_Product_Other_Data extends Onyx_Controller_Bo_Component {

    /**
     * main action
     */
     
    public function mainAction() {

        parent::assignProductData();
    
        /**
         * other data (attributes) list
         */

        $template = (isset($_GET['edit']) && $_GET['edit'] == 'true') ? 'edit' : 'preview';
        
        if (is_array($this->product_data['other_data'])) {
            foreach ($this->product_data['other_data'] as $key=>$value) {
                $note['key'] = $key;
                $note['value'] = $value;
                if ($note['key'] != '') {
                    $this->tpl->assign('OTHER_DATA', $note);
                    $this->tpl->parse("content.{$template}.other_data.item");
                }
            }
            if (count($this->product_data['other_data']) > 0) $this->tpl->parse("content.{$template}.other_data");
        }

        parent::parseTemplate();

        return true;
    }
}
