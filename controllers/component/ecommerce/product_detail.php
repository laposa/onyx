<?php
/** 
 * Copyright (c) 2005-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Component_Ecommerce_Product_Detail extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        
        /**
         * get product detail
         */

        require_once('models/ecommerce/ecommerce_product.php');
        $Product = new ecommerce_product();
        
        $product = $Product->productDetail($this->GET['product_id']);
        
        /**
         * if product_node_id is empty, than find default
         */

        if (is_numeric($this->GET['product_node_id'])) $product_node_id =  $this->GET['product_node_id'];
        else {
            $product_node_detail = $Product->findProductInNode($this->GET['product_id']);
            $product_node_id = $product_node_detail['id'];
        }
        
        
        /**
         * process only published
         */

        if ($product['publish'] == 1) {
            
            $this->tpl->assign('PRODUCT', $product);
            
            /**
             * process images
             */ 
            
            $this->processImages($product);
            

            /**
             * varieties
             */

            $Variety_list = new Onyx_Request("component/ecommerce/variety_list~product_id={$this->GET['product_id']}~");
            $this->tpl->assign('VARIETY_LIST', $Variety_list->getContent());
            
            /**
             * highlight stars (TODO: rating)
             */

            if ($product['priority'] > 99 ) {
                $c = $product['priority'] / 100;
                for ($i = 0; $i < $c; $i++) {
                    $this->tpl->parse("content.highlight.star");
                }
                $this->tpl->assign('HIGHLIGHT_TEXT', "&nbsp;&nbsp;&nbsp; $i stars rating");
                $this->tpl->parse("content.highlight");
            }
            
        }

        return true;
    }

    /**
     * process images
     */

    function processImages($product) {
    
        /**
         * product image conf
         */

        require_once('models/ecommerce/ecommerce_product_image.php');
        $ecommerce_product_image_conf = ecommerce_product_image::initConfiguration();

        /**
         * image width
         */

        if (is_numeric($this->GET['image_width'])) $image_width = $this->GET['image_width'];
        else $image_width = $GLOBALS['onyx_conf']['global']['product_detail_image_width'];
        
        $this->tpl->assign("IMAGE_WIDTH", $image_width);


        //for full product detail (product_radio), use image_gallery
        if ($template_block =='product_radio') {
            switch ($GLOBALS['onyx_conf']['global']['product_image_gallery']) {
                case 'simple_list':
                    $image_controller = 'component/image';
                    break;
                case 'gallery':
                    $image_controller = 'component/image_gallery';
                    break;
                case 'gallery_smooth':
                default:
                    $image_controller = 'component/image_gallery_smooth';
                    break;
            }
            $image_limit = '0,1';
        } else {
            $image_controller = 'component/image';
            $image_limit = '0,1';
        }
        
        $cycle = array();
        $cycle['fx'] = $ecommerce_product_image_conf['cycle_fx'];
        $cycle['easing'] = $ecommerce_product_image_conf['cycle_easing'];
        $cycle['timeout'] = $ecommerce_product_image_conf['cycle_timeout'];
        $cycle['speed'] = $ecommerce_product_image_conf['cycle_speed'];
        
        $Image = new Onyx_Request("$image_controller&relation=product&role=main&width=$image_width&node_id={$product['id']}&limit=$image_limit&cycle_fx={$cycle['fx']}&cycle_easing={$cycle['easing']}&cycle_timeout={$cycle['timeout']}&cycle_speed={$cycle['speed']}");
        $this->tpl->assign('PRODUCT_IMAGE', $Image->getContent());
    
        /**
         * variety image
         */

        //$Image = new Onyx_Request("image&relation=product_variety&role=main&node_id={$product['variety'][0]['id']}&limit=0,1");
        //$this->tpl->assign('IMAGE_VARIETY', $Image->getContent());

    }
}
