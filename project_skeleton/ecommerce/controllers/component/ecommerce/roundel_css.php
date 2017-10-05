<?php
/** 
 * Copyright (c) 2013-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('models/ecommerce/ecommerce_offer.php');

class Onxshop_Controller_Component_Ecommerce_Roundel_CSS extends Onxshop_Controller {

    /**
     * main action
     */
    
    public function mainAction() {

        $Offer = new ecommerce_offer();
        $includeForthcoming = $_SESSION['fe_edit_mode'] == 'edit';
        $offers = $Offer->getActiveOffers($includeForthcoming);

        foreach ($offers as $offer) {

            $offer['price_formatted'] = $this->formatPrice($offer['price'], $offer['currency_code']);
            $offer['title'] =  $this->getRoundelText($offer);
            $offer['image'] = $this->getRoundelImageSource($offer);

            $this->tpl->assign("ITEM", $offer);
            $this->tpl->parse("content.item");
        }

        return true;
        
    }
    
    /**
     * formatPrice
     */
     
    static function formatPrice($value, $currency_code)
    {
        $prefix = '';
        $postfix = '';
        if ($currency_code == "EUR") $prefix = "€";
        if ($currency_code == "GBP") $prefix = "£";
        if ($value < 1) {
            $value = $value * 100;
            $prefix = '';
            if ($currency_code == "EUR") $postfix = 'c';
            else $postfix = 'p';
        }
        if (fmod($value, 1) > 0) $price = number_format($value, 2);
        else $price = (int) $value;
        return $prefix . $price . $postfix;
    }
    
    /**
     * getRoundelText
     */

    static function getRoundelText($offer)
    {
        // replace placeholders
        $result = str_replace('__price__', $offer['price_formatted'], $offer['roundel_category']);
        $result = str_replace('__saving__', $offer['saving'], $result);
        $result = str_replace('__quantity__', $offer['quantity'], $result);

        // remove font size
        $result = trim(preg_replace("/\{\d+\}/", '', $result));

        // remove new lines
        $result = trim(preg_replace("/[\n\r]/", ' ', $result));

        return $result;
    }
    
    /**
     * getRoundelImageSource
     */

    static function getRoundelImageSource($offer)
    {
        // replace placeholders
        $text = str_replace('__price__', $offer['price_formatted'], $offer['roundel_category']);
        $text = str_replace('__saving__', $offer['saving'], $text);
        $text = str_replace('__quantity__', $offer['quantity'], $text);

        /**
         * options configured at offer level
         */
         
        $i = 1;
        $data = array();
        $lines = explode("\n", $text);

        foreach ($lines as $line) {
            preg_match("/\{(\d+)\}/", $line, $matches);
            $data["text{$i}"] = trim(preg_replace("/\{\d+\}/", '', $line));
            $data["size{$i}"] = (int) $matches[1];
            $i++;
        }
        
        /**
         * option configured at category level
         */

        $colours = explode("\n", $offer['campaign_category']);
        if (!empty($colours[0])) $data['bgcolor'] = $colours[0];
        if (!empty($colours[1])) $data['textcolor'] = $colours[1];

        /**
         * all parameters are passed to round_image
         */
         
        return '/request/component/ecommerce/roundel_image?' . http_build_query($data);
    }
}
