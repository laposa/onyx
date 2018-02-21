<?php
/**
 *
 * Copyright (c) 2010-2018 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *  
 */
        
$layout_style['content'] = array();
        
$layout_style['layout']['styles'] = array(
    'fibonacci-1-5'=>array('title' => 'Fibonacci 1/5 ratio (16.6%/83.3%)', 'description'=>'useful for a side bar'),
    'fibonacci-1-3'=>array('title' => 'Fibonacci 1/3 ratio (25%/75%)', 'description'=>'similar to former twenty-eighty'),
    'fibonacci-2-5'=>array('title' => 'Fibonacci 2/5 ratio (28.6%/71.4%)', 'description'=>'similar to former twenty-eighty'),
    'fibonacci-1-2'=>array('title' => 'Fibonacci 1/2 ratio (33%/66%)', 'description'=>'former thirty-seventy'),
    'fibonacci-3-5'=>array('title' => 'Fibonacci 3/5 ratio (38.2%/61.8%), rounded to Golden Ratio', 'description'=>'Golden ratio is convergence of fibonacci number in infinity. fibonacci number for 3:5 would be 1:1.666, Golden Ration is 1:1.618'),
    /*'fibonacci-2-3'=>array('title' => 'fibonacci 2/3 ratio (40%/60%)', 'description'=>'forty-sixty'),*/
    'fibonacci-1-1'=>array('title' => 'Fibonacci 1/1 ratio (50%/50%)', 'description'=>'former fifty-fifty'),
    /*'fibonacci-3-2'=>array('title' => 'fibonacci 3/2 ratio (60%/40%)', 'description'=>'sixty-forty'),*/
    'fibonacci-5-3'=>array('title' => 'Fibonacci 5/3 ratio, (61.8%/38.2%), rounded to Golden Ratio', 'description'=>'1.666:1, convergence to golden ration, 61.8/38.2 (Golden ratio 1.618:1), convergence of fibonacci number'),
    'fibonacci-2-1'=>array('title' => 'Fibonacci 2/1 ratio (66%/33%)', 'description'=>'former seventy-thirty, sixtySix-thirtyThree'),
    'fibonacci-5-2'=>array('title' => 'Fibonacci 5/2 ratio (71.4%/28.6%)', 'description'=>'similar to former twenty-eighty'),
    'fibonacci-3-1'=>array('title' => 'Fibonacci 3/1 ratio (75%/25%)', 'description'=>'similar to former twenty-eighty'),
    'fibonacci-5-1'=>array('title' => 'Fibonacci 5/1 ratio (83.3%/16.6%)', 'description'=>'useful for a side bar'),
);

$layout_style['layout']['default'] = 'fibonacci-1-1';

$layout_style['page'] = $layout_style['layout'];
$layout_style['page']['default'] = 'fibonacci-2-1'; //TODO: get default value from common_node:conf

$layout_style['container'] = $layout_style['layout'];

$layout_style['site'] = $layout_style['layout'];
$layout_style['site']['default'] = 'fibonacci-1-5'; //TODO: get default value from common_node:conf
