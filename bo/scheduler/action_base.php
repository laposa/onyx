<?php
/** 
 * Copyright (c) 2006-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

abstract class Onyx_Controller_Scheduler_Action_Base extends Onyx_Controller {

    /**
     * Main action
     */
     
    public function mainAction() {

        // implement in subclass
        // call setActionStatus() to flag the job as completed or failed

        return true;
    }

    /**
     * Set action status and action result message
     * 
     * @param bool   $status   true/false = completed/failed
     * @param string $message  action result message
     */
    public function setActionStatus($status, $message = null)
    {
        $this->tpl->assign("STATUS", $status ? "true" : "false");
        if ($message) msg($message);
    }


    /**
     * Get model for given content type
     */

    public function getContentModel($content_type) {

        switch ($content_type) {

            case 'education_survey':
                require_once('models/education/education_survey.php');
                return new education_survey();

            case 'ecommerce_product':
                require_once('models/ecommerce/ecommerce_product.php');
                return new ecommerce_product();

            case 'ecommerce_promotion':
                require_once('models/ecommerce/ecommerce_promotion.php');
                return new ecommerce_promotion();

            case 'ecommerce_delivery_carrier_zone_price':
                require_once('models/ecommerce/ecommerce_delivery_carrier_zone_price.php');
                return new ecommerce_delivery_carrier_zone_price();

            case 'common_node':
                require_once('models/common/common_node.php');
                return new common_node();

        }

        return false;
    }

    /**
     * Set publish status (1 = publish, 0 = unpublish) for given content
     */

    public function setPublishStatus($content_type, $content_id, $status)
    {
        if (!is_numeric($content_id)) {
            $this->setActionStatus(false, "Content ID is not numeric");
            return;
        }

        $ContentModel = $this->getContentModel($content_type);

        if (!$ContentModel) {
            $this->setActionStatus(false, "Invalid content type");
            return;
        }
    
        $ContentModel->update(array(
            'id' => $content_id, 
            'publish' => $status == 1 ? 1 : 0
        ));

        // for a product, related product_page needs to be updated as well
        if ($content_type == 'ecommerce_product') {
            $product_page = $ContentModel->getProductHomepage($content_id);
            if (is_array($product_page) && count($product_page) > 0) {
                $this->updateProductPage($product_page, $status);
            }
        }

        $this->setActionStatus(true, $status ? "Content published" : "Content unpublished");
        $this->flushCache();

    }

    /**
     * updateProductPage
     */

    public function updateProductPage($product_homepage, $status)
    {
        $product_homepage['publish'] = ($status == 1 ? 1 : 0);

        $Node = $this->getContentModel('common_node');
        $Node->nodeUpdate($product_homepage);
    }

    /**
     * flushCache
     */
     
    public function flushCache()
    {
        onyx_flush_cache();
    }

}
