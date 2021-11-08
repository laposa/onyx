<?php
/**
 *
 * Copyright (c) 2006-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *  
 */

$templates_info['variable'] = array(
    'default'=>array('title' => 'Default', 'description'=>'', 'visibility'=>false),
    'text'=>array('title' => 'Text Variable', 'description'=>'', 'visibility'=>true),
    'image'=>array('title' => 'Image Variable', 'description'=>'', 'visibility'=>true),
    'default_template' => 'default'
    );
    
$templates_info['content'] = array(
    'default'=>array('title' => 'Default', 'description'=>'', 'visibility'=>false),
    'rte'=>array('title' => 'Rich Text', 'description'=>'', 'visibility'=>true),
    'image_gallery'=>array('title' => 'Image Gallery', 'description'=>'Multiple images listed in a gallery', 'visibility'=>true),
    'image'=>array('title' => 'Image', 'description'=>'Single image', 'visibility'=>true),
    'video'=>array('title' => 'Video', 'description'=>'', 'visibility'=>true),
    'quote'=>array('title' => 'Quote (Testimonial)', 'description'=>'Testimonial', 'visibility'=>true),
    'contact_form'=>array('title' => 'Contact Form', 'description'=>'', 'visibility'=>true), 
    'comment'=>array('title' => 'Forum - User comments', 'description'=>'', 'visibility'=>false),
    'news_list'=>array('title' => 'News List', 'description'=>'', 'visibility'=>true),
    'menu'=>array('title' => 'Menu Navigation', 'description'=>'Menu of Pages', 'visibility'=>true), 
    'content_list'=>array('title' => 'Content List', 'description'=>'Useful for FAQ list', 'visibility'=>true),
    'divider'=>array('title' => 'Horizontal Divider', 'description'=>'', 'visibility'=>false),
    'html'=>array('title' => 'Pure HTML', 'description'=>'', 'visibility'=>true),
    'feed'=>array('title' => 'Remote Feed Resource', 'description'=>'', 'visibility'=>false),
    'component'=>array('title' => 'Generic Component', 'description'=>'', 'visibility'=>true), 
    'file'=>array('title' => 'File List (Downloads)', 'description'=>'', 'visibility'=>true),
    'survey'=>array('title' => 'Survey (Questionnaire Form)', 'description'=>'', 'visibility'=>true),
    'external_source'=>array('title' => 'Remote HTML Resource', 'description'=>'', 'visibility'=>false),
    'shared'=>array('title' => 'Shared (Linked) Content', 'description'=>'', 'visibility'=>false),
    'imagemap'=>array('title' => 'Image map', 'description'=>'', 'visibility'=>false), 
    'teaser'=>array('title' => 'Teaser', 'description'=>'Create a teaser for page and allow to customise image, description and link text', 'visibility'=>true),
    'page_list'=>array('title' => 'Page List', 'description'=>'', 'visibility'=>true),
    'filter'=>array('title' => 'Categories Filter', 'description'=>'', 'visibility'=>false),
    'notice'=>array('title' => 'Notice', 'description'=>'i.e. for stores', 'visibility'=>false),
    'adaptive'=>array('title' => 'Adaptive', 'description'=>'Show only if meets certain conditions', 'visibility'=>false),
    'default_template' => 'default'
    );
        
$templates_info['layout'] = array(
    'default'=>array('title' => 'Default', 'description'=>'', 'visibility'=>false),
    '1column'=>array('title' => '1-column (box)', 'description'=>'', 'visibility'=>true),
    '2columns'=>array('title' => '2-columns', 'description'=>'', 'visibility'=>true),
    '3columns'=>array('title' => '3-columns', 'description'=>'', 'visibility'=>true),
    '4columns'=>array('title' => '4-columns', 'description'=>'', 'visibility'=>true),
    '5columns'=>array('title' => '5-columns', 'description'=>'', 'visibility'=>true),
    '6columns'=>array('title' => '6-columns', 'description'=>'', 'visibility'=>true),
    'tabs'=>array('title' => 'Tabs', 'description'=>'', 'visibility'=>true),
    'slider'=>array('title' => 'Slider', 'description'=>'', 'visibility'=>true),
    'default_template' => 'default'
    );
        
$templates_info['page'] = array(
    'default'=>array('title' => 'Default', 'description'=>'', 'visibility'=>true),
    'blank'=>array('title' => 'Blank', 'description'=>'', 'visibility'=>true),
    'news'=>array('title' => 'News article', 'description'=>'', 'visibility'=>true),
    'competition'=>array('title' => 'Competition (Survey)', 'description'=>'', 'visibility'=>true),
    'poll'=>array('title' => 'Poll (Survey)', 'description'=>'', 'visibility'=>true),
    'symbolic'=>array('title' => 'Alias (Redirect)', 'description'=>'a.k.a. symbolic link page or redirect with a record in navigation, i.e. can be shown as part of the navigation', 'visibility'=>true),
    'default_template' => 'default'
    );
        
$templates_info['container'] = array(
    'default'=>array('title' => 'System folder', 'description'=>'', 'visibility'=>true),
    'default_template' => 'default'
    );

$templates_info['site'] = array(
    'default'=>array('title' => 'Site', 'description'=>'', 'visibility'=>true),
    'print'=>array('title' => 'Printe template', 'description'=>'', 'visibility'=>false),
    'facebook'=>array('title' => 'Facebook', 'description'=>'', 'visibility'=>false),
    'default_template' => 'default'
    );


/**
 * add ecommerce node types when ecommerce is enabled
 * please note the templates and controllers are only in project_skeleton/ecommerce/
 */

if (ONYX_ECOMMERCE) {
    
    $templates_info['content']['product_highlights'] = array('title' => 'Product Highlights', 'description'=>'', 'visibility'=>true);
    $templates_info['content']['special_offer_list'] = array('title' => 'Special Offers List', 'description'=>'', 'visibility'=>true);
    $templates_info['content']['recipe_list'] = array('title' => 'Recipe List', 'description'=>'', 'visibility'=>true);

    $templates_info['page']['recipe'] = array('title' => 'Recipe', 'description'=>'', 'visibility'=>false);
    $templates_info['page']['store'] = array('title' => 'Store', 'description'=>'', 'visibility'=>false);
    $templates_info['page']['product'] = array('title' => 'Product', 'description'=>'', 'visibility'=>false);
    $templates_info['page']['product_browse'] = array('title' => 'Products Browse', 'description'=>'', 'visibility'=>true);
    
}
