<?php
/** 
 * Copyright (c) 2010-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Bo_Component_File extends Onyx_Controller {

    public $File;

    /**
     * main action
     */
     
    public function mainAction() {
    
        $relation = $this->GET['relation'];
        
        $this->File = $this->initializeFile($relation);

        $this->tpl->assign('IMAGE_CONF', $this->File->conf);


        return true;
    }
    
    /**
     * initialize file
     */
     
    public function initializeFile($relation) {
    
        switch ($relation) {
            case 'product':
                require_once('models/ecommerce/ecommerce_product_image.php');
                $File = new ecommerce_product_image();
            break;
            case 'product_variety':
                require_once('models/ecommerce/ecommerce_product_variety_image.php');
                $File = new ecommerce_product_variety_image();
            break;
            case 'recipe':
                require_once('models/ecommerce/ecommerce_recipe_image.php');
                $File = new ecommerce_recipe_image();
            break;
            case 'store':
                require_once('models/ecommerce/ecommerce_store_image.php');
                $File = new ecommerce_store_image();
            break;
            case 'survey':
                require_once('models/education/education_survey_image.php');
                $File = new education_survey_image();
            break;
            case 'taxonomy':
                require_once('models/common/common_taxonomy_label_image.php');
                $File = new common_taxonomy_label_image();
            break;
            case 'node':
                require_once('models/common/common_image.php');
                $File = new common_image();
            break;
            case 'print_article':
                require_once('models/common/common_print_article.php');
                $File = new common_print_article();
            break;
            case 'file':
            default:
                require_once('models/common/common_file.php');
                $File = new common_file();
            break;
        }
        
        return $File;
    }
}
