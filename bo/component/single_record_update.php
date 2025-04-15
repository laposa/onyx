<?php
/**
 * Copyright (c) 2009-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Component_Single_Record_Update extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        /**
         * TODO:
         * implement general updateSingleAttribute()
         */
        //print_r($_POST);
        $model = $this->GET['model'];
        $attribute = $this->GET['attribute'];
        $update_value = trim($_POST['update_value']);
        $original_value = trim($_POST['original_html']);
        
        // currently implemented for product_variety.name and price.value
        switch ($model) {
        
            case 'common_node':
        
                require_once('models/common/common_node.php');
                
                $element_id_parts = explode('-', $_POST['element_id']);
                $variety_id = $element_id_parts[3];
                
                $ModelObj = new common_node();
                
                if (!$ModelObj->updateSingleAttribute($attribute, $update_value, $variety_id)) msg('Failed', 'error');
            
            break;
            
            case 'ecommerce_product_variety':
        
                require_once('models/ecommerce/ecommerce_product_variety.php');
                
                $element_id_parts = explode('-', $_POST['element_id']);
                $variety_id = $element_id_parts[3];
                
                $ModelObj = new ecommerce_product_variety();
                
                if (!$ModelObj->updateSingleAttribute($attribute, $update_value, $variety_id)) msg('Failed', 'error');
            
            break;
            
            case 'ecommerce_price':
        
                require_once('models/ecommerce/ecommerce_price.php');
                $ModelObj = new ecommerce_price();
                
                $element_id_parts = explode('-', $_POST['element_id']);
                $variety_id = $element_id_parts[3];
                
                $last_price = $ModelObj->getLastPriceForVariety($variety_id);
                //remove anything else than number and decimal point
                $update_value = preg_replace("/[^0-9\.]*/", '', $update_value);
                //update only when the new price is different than old price
                if (round($last_price['value'], 2) != round($update_value, 2)) if (!$ModelObj->updateSingleAttribute('value', $update_value, $last_price['id'])) msg('Failed', 'error');
            break;

            case 'international_translation':
        
                require_once('models/international/international_translation.php');
                
                $element_id_parts = explode('-', $_POST['element_id']);
                $updated_id = $element_id_parts[3];
                $ModelObj = new international_translation();
                if (!$ModelObj->updateSingleAttribute($attribute, $update_value, $updated_id)) msg('Failed', 'error');
            
            break;

            default:
                return false;
            break;
        }
        
        
        
        if ($update_value != $original_value) {
            $value = $update_value ;
        } else {
            $value = $original_value;
        }
        
        $this->tpl->assign('VALUE', $value);

        return true;
    }
}
