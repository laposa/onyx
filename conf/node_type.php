<?php
/**
 *
 * Copyright (c) 2006-2024 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *  
 */

$templates_info = [];

$templates_info['variable'] = [
    'default'=>['title' => 'Default', 'description'=>'', 'visibility'=>false],
    'text'=>['title' => 'Text Variable', 'description'=>'', 'visibility'=>true],
    'image'=>['title' => 'Image Variable', 'description'=>'', 'visibility'=>true],
    'default_template' => 'default'
    ];
    
$templates_info['content'] = [
    'default'=>['title' => 'Default', 'description'=>'', 'visibility'=>false],
    'rte'=>['title' => 'Rich Text', 'description'=>'', 'visibility'=>true],
    'image_gallery'=>['title' => 'Image Gallery', 'description'=>'Multiple images listed in a gallery', 'visibility'=>true],
    'image'=>['title' => 'Image', 'description'=>'Single image', 'visibility'=>true],
    'video'=>['title' => 'Video', 'description'=>'', 'visibility'=>true],
    'quote'=>['title' => 'Quote (Testimonial)', 'description'=>'Testimonial', 'visibility'=>true],
    'contact_form'=>['title' => 'Contact Form', 'description'=>'', 'visibility'=>true], 
    'comment'=>['title' => 'Forum - User comments', 'description'=>'', 'visibility'=>false],
    'news_list'=>['title' => 'News List', 'description'=>'', 'visibility'=>true],
    'menu'=>['title' => 'Menu Navigation', 'description'=>'Menu of Pages', 'visibility'=>true], 
    'content_list'=>['title' => 'Content List', 'description'=>'Useful for FAQ list', 'visibility'=>true],
    'divider'=>['title' => 'Horizontal Divider', 'description'=>'', 'visibility'=>false],
    'html'=>['title' => 'Pure HTML', 'description'=>'', 'visibility'=>true],
    'feed'=>['title' => 'Remote Feed Resource', 'description'=>'', 'visibility'=>false],
    'component'=>['title' => 'Generic Component', 'description'=>'', 'visibility'=>true], 
    'file'=>['title' => 'File List (Downloads)', 'description'=>'', 'visibility'=>true],
    'survey'=>['title' => 'Survey (Questionnaire Form)', 'description'=>'', 'visibility'=>true],
    'external_source'=>['title' => 'Remote HTML Resource', 'description'=>'', 'visibility'=>false],
    'shared'=>['title' => 'Shared (Linked) Content', 'description'=>'', 'visibility'=>false],
    'imagemap'=>['title' => 'Image map', 'description'=>'', 'visibility'=>false], 
    'teaser'=>['title' => 'Teaser', 'description'=>'Create a teaser for page and allow to customise image, description and link text', 'visibility'=>true],
    'page_list'=>['title' => 'Page List', 'description'=>'', 'visibility'=>true],
    'filter'=>['title' => 'Categories Filter', 'description'=>'', 'visibility'=>false],
    'notice'=>['title' => 'Notice', 'description'=>'i.e. for stores', 'visibility'=>false],
    'textile'=>['title' => 'Textile', 'description'=>'', 'visibility'=>false],
    'adaptive'=>['title' => 'Adaptive', 'description'=>'Show only if meets certain conditions', 'visibility'=>false],
    
    'default_template' => 'default'
    ];
        
$templates_info['layout'] = [
    'default'=>['title' => 'Default', 'description'=>'', 'visibility'=>false],
    '1column'=>['title' => '1-column (box)', 'description'=>'', 'visibility'=>true],
    '2columns'=>['title' => '2-columns', 'description'=>'', 'visibility'=>true],
    '3columns'=>['title' => '3-columns', 'description'=>'', 'visibility'=>true],
    '4columns'=>['title' => '4-columns', 'description'=>'', 'visibility'=>true],
    '5columns'=>['title' => '5-columns', 'description'=>'', 'visibility'=>true],
    '6columns'=>['title' => '6-columns', 'description'=>'', 'visibility'=>true],
    'tabs'=>['title' => 'Tabs', 'description'=>'', 'visibility'=>true],
    'slider'=>['title' => 'Slider', 'description'=>'', 'visibility'=>true],
    'default_template' => 'default'
    ];
        
$templates_info['page'] = [
    'default'=>['title' => 'Default', 'description'=>'', 'visibility'=>true],
    'blank'=>['title' => 'Blank', 'description'=>'', 'visibility'=>true],
    'news'=>['title' => 'News article', 'description'=>'', 'visibility'=>true],
    'competition'=>['title' => 'Competition (Survey)', 'description'=>'', 'visibility'=>true],
    'poll'=>['title' => 'Poll (Survey)', 'description'=>'', 'visibility'=>true],
    'symbolic'=>['title' => 'Alias (Redirect)', 'description'=>'a.k.a. symbolic link page or redirect with a record in navigation, i.e. can be shown as part of the navigation', 'visibility'=>true],
    'default_template' => 'default'
    ];
        
$templates_info['container'] = [
    'default'=>['title' => 'System folder', 'description'=>'', 'visibility'=>true],
    'default_template' => 'default'
    ];

$templates_info['site'] = [
    'default'=>['title' => 'Site', 'description'=>'', 'visibility'=>true],
    'print'=>['title' => 'Printe template', 'description'=>'', 'visibility'=>false],
    'facebook'=>['title' => 'Facebook', 'description'=>'', 'visibility'=>false],
    'default_template' => 'default'
    ];


/**
 * add ecommerce node types when ecommerce is enabled
 * please note the templates and controllers are only in project_skeleton/ecommerce/
 */

if (ONYX_ECOMMERCE) {
    
    $templates_info['content']['product_highlights'] = ['title' => 'Product Highlights', 'description'=>'', 'visibility'=>true];
    $templates_info['content']['special_offer_list'] = ['title' => 'Special Offers List', 'description'=>'', 'visibility'=>true];
    $templates_info['content']['recipe_list'] = ['title' => 'Recipe List', 'description'=>'', 'visibility'=>true];

    $templates_info['page']['recipe'] = ['title' => 'Recipe', 'description'=>'', 'visibility'=>false];
    $templates_info['page']['store'] = ['title' => 'Store', 'description'=>'', 'visibility'=>false];
    $templates_info['page']['product'] = ['title' => 'Product', 'description'=>'', 'visibility'=>true];
    $templates_info['page']['product_browse'] = ['title' => 'Products Browse', 'description'=>'', 'visibility'=>true];
    
}
