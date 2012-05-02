<?php
/**
 *
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 	
 */

$templates_info['content'] = array(
		'textile'=>array('title' => 'Textile', 'description'=>''),
		'RTE'=>array('title' => 'Rich Text', 'description'=>''), 
		'product_highlights'=>array('title' => 'Product Highlights', 'description'=>''), 
		'picture'=>array('title' => 'Photo Gallery', 'description'=>''),
		'quote'=>array('title' => 'Quote', 'description'=>''),
		'contact_form'=>array('title' => 'Contact Form', 'description'=>''), 
		'comment'=>array('title' => 'Forum - User comments', 'description'=>''),
		'news_list'=>array('title' => 'News List', 'description'=>''),
		'menu'=>array('title' => 'Menu of Pages', 'description'=>''), 
		'content_list'=>array('title' => 'Content List', 'description'=>'Useful for FAQ list'),
		'divider'=>array('title' => 'Horizontal Divider', 'description'=>''),
		'HTML'=>array('title' => 'Pure HTML', 'description'=>''),
		'feed'=>array('title' => 'Remote Feed Resource', 'description'=>''),
		'component'=>array('title' => 'Generic Component', 'description'=>''), 
		'file'=>array('title' => 'File List (Downloads)', 'description'=>''),
		//'lastFM'=>array('title' => 'last FM user profile', 'description'=>''),
		/*'news'=>array('title' => 'News item', 'description'=>''),*/
		 //'external_source'=>array('title' => 'Remote HTML Resource', 'description'=>''),
		//'print_article_list'=>array('title' => 'Print Article List (JCM)', 'description'=>''),
		/*'shared'=>array('title' => 'Shared (Linked) Content', 'description'=>''),*/
		//'imagemap'=>array('title' => 'Image map', 'description'=>''), 
		//'teaser'=>array('title' => 'Content teaser', 'description'=>''),
		//'filter'=>array('title' => 'Categories Filter', 'description'=>''),
		'default_template' => 'RTE'
		);
		
$templates_info['layout'] = array(
		'1column'=>array('title' => '1-column (box)', 'description'=>''),
		'2columns'=>array('title' => '2-columns', 'description'=>''),
		'3columns'=>array('title' => '3-columns', 'description'=>''),
		'4columns'=>array('title' => '4-columns', 'description'=>''),
		'5columns'=>array('title' => '5-columns', 'description'=>''),
		'6columns'=>array('title' => '6-columns', 'description'=>''),
		'tabs'=>array('title' => 'Tabs', 'description'=>''),
		'default_template' => '2columns'
		);
		
$templates_info['page'] = array(
		'default'=>array('title' => 'Default', 'description'=>''),
		/*'blank'=>array('title' => 'Blank page', 'description'=>''), */
		/*'commerce'=>array('title' => 'Password protected e-commerce', 'description'=>'Used for My Account pages'), */
		'product_browse'=>array('title' => 'Products Browse', 'description'=>''),
		'symbolic'=>array('title' => 'Symbolic Link Page', 'description'=>''), 
		'default_template' => 'default'
		);
$templates_info['container'] = array(
		'default'=>array('title' => 'System folder', 'description'=>''),
		'default_template' => 'default'
		);

$templates_info['site'] = array(
		'default'=>array('title' => 'Site', 'description'=>''),
		'default_template' => 'default'
		);