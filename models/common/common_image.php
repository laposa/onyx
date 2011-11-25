<?php
require_once('models/common/common_file.php');

/**
 * class common_image
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class common_image  extends common_file {

	var $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'src'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'role'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'node_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'title'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'description'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'priority'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'author'=>array('label' => '', 'validation'=>'int', 'required'=>true)
	);
	 
	 
	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {
	
		$conf = common_image::initImageDefaultConfiguration();
		return $conf;
		
	}
	
	/**
	 * init default conf
	 */
	 
	static function initImageDefaultConfiguration() {
	
		if (array_key_exists('common_image', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['common_image'];
		else $conf = array();
		
		
		/**
		 * set default values if empty
		 */
		
		if (!is_numeric($conf['width_max'])) $conf['width_max'] = 1000;
		if (!is_numeric($conf['height_max'])) $conf['height_max'] = 600;
		
		if (!is_numeric($conf['thumbnail_width_min'])) $conf['thumbnail_width_min'] = 25;
		if (!is_numeric($conf['thumbnail_width_max'])) $conf['thumbnail_width_max'] = 1000;
		if (!is_numeric($conf['thumbnail_step'])) $conf['thumbnail_step'] = 5;
		
		if (!is_numeric($conf['jpeg_quality'])) $conf['jpeg_quality'] = 80;
				
		return $conf;
		
	}
	
	/**
	 * create watermark
	 */
	 
	function watermark() {
		//composite -watermark 30% -gravity southeast 1805.jpg test.jpg test1.jpg
	}

	/**
	 * resize
	 */
	 
	static function resize($file, $required_width, $required_height = false){
	
		//first check file exists and is readable
		if (!is_readable(ONXSHOP_PROJECT_DIR . $file)) return false;
		
		//prepare variables
		if (is_numeric($required_height)) {
			$directory = ONXSHOP_PROJECT_DIR ."var/thumbnails/{$required_width}x{$required_height}";
			$thumb_file = "var/thumbnails/{$required_width}x{$required_height}/" . md5($file);
		} else {
			$directory = ONXSHOP_PROJECT_DIR ."var/thumbnails/$required_width";
			$thumb_file = "var/thumbnails/$required_width/" . md5($file);
		}
		
		$file_rp = ONXSHOP_PROJECT_DIR . $file;
		$thumb_file_rp = ONXSHOP_PROJECT_DIR . $thumb_file;
		
		//check if the destination directory exits
		if (!is_readable($directory)) {
			if (!mkdir($directory)) {
				msg("common_image.resize(): Cannot create folder $directory", 'error');
			}
		}
		
		//determinate real image size
		$size = common_image::getImageSize($file_rp);

		$width = $required_width;
		$height = round($required_width/$size['proportion']);
		msg("Thumbnail will have size $width x $height", 'ok', 3);
		
		
		/**
		 * if height is specified align in center (add border)
		 */
		
		if (is_numeric($required_height)) {
			
			$add_border_im_param = common_image::addBorderImParam($width, $height, $required_width, $required_height, $size['proportion']);
			
			/* if the image is too tall, reduce the size */
			
			if ($height > $required_height) {
				
				$width = round($required_height * $size['proportion']);
				$height = $required_height;
			
			}
			
		} else {
			
			$add_border_im_param = '';
		
		}
		
		
		/**
		 * return cached file or create with ImageMagick
		 */
		 
		if (is_readable($thumb_file_rp)) {
		
			return $thumb_file;
		
		} else {
			$image_configuration = common_image::initConfiguration();
			$jpeg_quality = $image_configuration['jpeg_quality'];
			
			$shell_command = "resize " . escapeshellarg($file_rp) . " " . escapeshellarg($width) . " " . escapeshellarg($height) . " " . escapeshellarg($thumb_file_rp) . " " . $jpeg_quality . " " . escapeshellarg($add_border_im_param);
			
			$result = local_exec($shell_command);
			
			if (is_readable($thumb_file_rp)) return $thumb_file;
			else return false;
		}
			
	}
	
	/**
	 * this is an interim solution before upgrading to Debian Squeeze
	 * http://www.imagemagick.org/Usage/thumbnails/#cut
	 * http://www.imagemagick.org/Usage/resize/#space_fill
	 */
	 
	static public function addBorderImParam($width, $height, $required_width, $required_height, $proportion) {
	
		if ($height < $required_height) {
		
			/* if the image is not tall enought, add border on top and bottom*/
			$height_diff = $required_height - $height;
			$half_of_height_diff = round($height_diff / 2);
			
			$add_border_im_param = "-bordercolor white  -border 0x{$half_of_height_diff}";
				
		} else if ($height > $required_height) {
			
			/* if the image is too tall, reduce the size, add border on left and right */
			$width = round($required_height * $proportion);
			$height = $required_height;
			
			$width_diff = $required_width - $width;
			$half_of_width_diff = round($width_diff / 2);
			$add_border_im_param = "-bordercolor white  -border {$half_of_width_diff}x0";
				
		}
		
		
		return $add_border_im_param;
		
	}
}
