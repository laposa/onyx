<?php
/**
 *
 * Copyright (c) 2006-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 	
 */

$templates_info['content'] = array(
	'default'=>array('title' => 'Default', 'description'=>'', 'visibility'=>false),
	'textile'=>array('title' => 'Textile', 'description'=>'', 'visibility'=>true),
	'RTE'=>array('title' => 'Rich Text', 'description'=>'', 'visibility'=>true), 
	'product_highlights'=>array('title' => 'Product Highlights', 'description'=>'', 'visibility'=>true),
	'special_offer_list'=>array('title' => 'Special Offers List', 'description'=>'', 'visibility'=>true),
	'picture'=>array('title' => 'Photo Gallery', 'description'=>'', 'visibility'=>true),
	'video'=>array('title' => 'Video', 'description'=>'', 'visibility'=>true),
	'quote'=>array('title' => 'Quote (Testimonial)', 'description'=>'Testimonial', 'visibility'=>true),
	'contact_form'=>array('title' => 'Contact Form', 'description'=>'', 'visibility'=>true), 
	'comment'=>array('title' => 'Forum - User comments', 'description'=>'', 'visibility'=>true),
	'news_list'=>array('title' => 'News List', 'description'=>'', 'visibility'=>true),
	'menu'=>array('title' => 'Menu of Pages', 'description'=>'', 'visibility'=>true), 
	'content_list'=>array('title' => 'Content List', 'description'=>'Useful for FAQ list', 'visibility'=>true),
	'divider'=>array('title' => 'Horizontal Divider', 'description'=>'', 'visibility'=>true),
	'HTML'=>array('title' => 'Pure HTML', 'description'=>'', 'visibility'=>true),
	'feed'=>array('title' => 'Remote Feed Resource', 'description'=>'', 'visibility'=>true),
	'component'=>array('title' => 'Generic Component', 'description'=>'', 'visibility'=>true), 
	'file'=>array('title' => 'File List (Downloads)', 'description'=>'', 'visibility'=>true),
	'recipe_list'=>array('title' => 'Recipe List', 'description'=>'', 'visibility'=>true),
	'survey'=>array('title' => 'Survey (Questionnaire Form)', 'description'=>'', 'visibility'=>true),
	'external_source'=>array('title' => 'Remote HTML Resource', 'description'=>'', 'visibility'=>false),
	'print_article_list'=>array('title' => 'Print Article List', 'description'=>'', 'visibility'=>false),
	'shared'=>array('title' => 'Shared (Linked) Content', 'description'=>'', 'visibility'=>false),
	'imagemap'=>array('title' => 'Image map', 'description'=>'', 'visibility'=>false), 
	'teaser'=>array('title' => 'Content teaser', 'description'=>'', 'visibility'=>false),
	'filter'=>array('title' => 'Categories Filter', 'description'=>'', 'visibility'=>false),
	'default_template' => 'RTE'
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
	'default_template' => '2columns'
	);
		
$templates_info['page'] = array(
	'default'=>array('title' => 'Default', 'description'=>'', 'visibility'=>true),
	'blank'=>array('title' => 'Blank', 'description'=>'', 'visibility'=>false),
	'news'=>array('title' => 'News article', 'description'=>'', 'visibility'=>false),
	'recipe'=>array('title' => 'Recipe', 'description'=>'', 'visibility'=>false),
	'store'=>array('title' => 'Store', 'description'=>'', 'visibility'=>false),
	'product'=>array('title' => 'Product', 'description'=>'', 'visibility'=>false),
	'product_browse'=>array('title' => 'Products Browse', 'description'=>'', 'visibility'=>true),
	'competition'=>array('title' => 'Competition (Survey)', 'description'=>'', 'visibility'=>true),
	'poll'=>array('title' => 'Poll (Survey)', 'description'=>'', 'visibility'=>true),
	'symbolic'=>array('title' => 'Alias', 'description'=>'a.k.a. symbolic link page', 'visibility'=>true), 
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
