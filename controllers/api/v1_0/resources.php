<?php
/** 
 * Copyright (c) 2012-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api.php');
require_once('models/common/common_revision.php');

class Onxshop_Controller_Api_v1_0_Resources extends Onxshop_Controller_Api {

    /**
     * get data
     */
    
    public function getData() {

        $version = $this->GET['version'];
        $api_key = $this->GET['api_key'];
        $format = $this->GET['format'];
        
        if ($version && $api_key && $format) {
            
            $version_string = preg_replace('/\_/', '.', $version);
            $version_string = ltrim($version_string, 'v');
            
            if ($_SERVER['SSL_PROTOCOL'] || $_SERVER['HTTPS']) $protocol = 'https';
            else $protocol = 'http';
            
            $base_api_url = "$protocol://{$_SERVER['HTTP_HOST']}/api/v{$version_string}/";
            $standard_params = "?format={$format}&api_key={$api_key}";
            
            $data = array();
            $data['recipe_list'] = "{$base_api_url}recipe_list{$standard_params}";
            $data['recipe_category_list'] = "{$base_api_url}recipe_category_list{$standard_params}";
            $data['special_offer_list'] = "{$base_api_url}special_offer_list{$standard_params}";
            $data['store_location_list'] = "{$base_api_url}store_location_list{$standard_params}";
            $data['recipe_rating'] = "{$base_api_url}recipe_rating{$standard_params}";
            $data['css'] = "$protocol://{$_SERVER['HTTP_HOST']}/css/recipe_app.css";
            $data['iphone_download_url'] = "http://itunes.apple.com/";
            $data['android_download_url'] = "https://play.google.com/";
            $data['landing_page'] = "$protocol://{$_SERVER['HTTP_HOST']}/";
            $data['theme_version'] = $this->getThemeVersion();
            $data['data_version'] = $this->getDataVersion();
            $data['background_images'] = "$protocol://{$_SERVER['HTTP_HOST']}/images/recipe_app/backgrounds/";
            $data['background_main'] = "$protocol://{$_SERVER['HTTP_HOST']}/images/recipe_app/backgrounds/main.png";
            $data['background_invisible_header'] = "$protocol://{$_SERVER['HTTP_HOST']}/images/recipe_app/backgrounds/invisible_header.png";
            $data['background_empty_shopping_list'] = "$protocol://{$_SERVER['HTTP_HOST']}/images/recipe_app/backgrounds/empty_shopping_list.png";
            $data['mail_template_images'] = "$protocol://{$_SERVER['HTTP_HOST']}/images/recipe_app/mail_template/";
            
        } else {
        
            $data['status'] = 400;
            $data['message'] = "Invalid request";
        
        }
        
        return $data;
        
    }
    
    /**
     * getDataVersion
     */
     
    public function getDataVersion() {
         
        if (is_numeric($GLOBALS['onxshop_conf']['common_configuration']['api_data_version'])) {
            
            /**
             * read from config
             */

            $data_version = $GLOBALS['onxshop_conf']['common_configuration']['api_data_version'];
        
        } else {
            
            /**
             * return latest revision ID
             */
             
            $Revision = new common_revision();
            $data_version = $Revision->count(); // this should be max(id), but count will do
                
        }
        
        return (int)$data_version;
        
    }
    
    /**
     * getThemeVersion
     */
     
    public function getThemeVersion() {
        
        if (is_numeric($GLOBALS['onxshop_conf']['common_configuration']['api_theme_version'])) {
            
            $theme_version = $GLOBALS['onxshop_conf']['common_configuration']['api_theme_version'];
            
        } else {
            
            $theme_version = 1;
            
        }
        
        return (int)$theme_version;
        
    }
    
}
