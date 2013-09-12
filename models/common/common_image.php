<?php
require_once('models/common/common_file.php');

/**
 * class common_image
 *
 * Copyright (c) 2009-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class common_image  extends common_file {

	var $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'src'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'role'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'node_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'title'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'description'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'priority'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'author'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'content'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false),
		'link_to_node_id'=>array('label' => '', 'validation'=>'int', 'required'=>false)
	);
	
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE common_image (
    id serial NOT NULL PRIMARY KEY,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL REFERENCES common_node ON UPDATE CASCADE ON DELETE CASCADE,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone,
    author integer,
    content text,
    other_data text,
    link_to_node_id integer
);
		";
		
		return $sql;
	}
	 
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
	 
	static function resize($file, $required_width, $required_height = false, $method = 'extent', $gravity = 'center', $fill = false){
	
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
		 * resize method option
		 */
		
		if (!in_array(strtolower($method), array('crop', 'extent'))) $method = 'extent';
		
		/**
		 * gravity option
		 * NorthWest, North, NorthEast, West, Center, East, SouthWest, South, SouthEast
		 */
		
		if (!in_array(strtolower($gravity), array('northwest', 'north', 'northeast', 'west', 'center', 'east', 'southwest', 'south', 'southeast'))) $gravity = 'center';
		
		/**
		 * fill option
		 */
		 
		if ($fill == true) {
			$fill = '^';
		} else {
			$fill = '';
		}
		
		/**
		 * if height is specified align in center and allow to fill the space
		 * see	http://www.imagemagick.org/Usage/thumbnails/#cut
		 		http://www.imagemagick.org/Usage/resize/#shrink
		 */
		if (is_numeric($required_height)) {
			if (preg_match("/(\.gif$)|(\.png$)/i", $file_rp)) $background = 'none';
			else $background = 'white';
			$other_im_params = "-background {$background} -alpha background -gravity {$gravity} -{$method} {$width}x{$required_height}";
			$height = $required_height;
			
		} else {
			
			$other_im_params = '';
		
		}
		
		
		/**
		 * return cached file or create with ImageMagick
		 */
		 
		if (is_readable($thumb_file_rp)) {
		
			return $thumb_file;
		
		} else {
		
			$image_configuration = common_image::initConfiguration();
			$jpeg_quality = $image_configuration['jpeg_quality'];
			
			$file_rp_escaped = escapeshellarg($file_rp);
			$width = (int)$width;
			$height = (int)$height;
			$jpeg_quality = (int)$jpeg_quality;
			$thumb_file_rp_escaped = escapeshellarg($thumb_file_rp);
			
			//usage: resize $filename $width $height $target_filename $quality $other_options
			///usr/bin/convert "$1" -colorspace RGB -depth 8 -quality $QUALITY -thumbnail $2x$3^ $6 "$4"
			
			$shell_command = "convert {$file_rp_escaped} -colorspace RGB -depth 8 -quality {$jpeg_quality} -thumbnail {$width}x{$height}{$fill} {$other_im_params} {$thumb_file_rp_escaped}";
			
			$result = local_exec($shell_command);
			
			if (is_readable($thumb_file_rp)) return $thumb_file;
			else return false;
		}
			
	}

	/**
	 * getTeaserImageForNodeId
	 */
	 
	public function getTeaserImageForNodeId($node_id) {
		
		/**
		 * try to get explicit "teaser" image role
		 */
		 
		$file_list = $this->listFiles($node_id , $priority = "priority DESC, id ASC", 'teaser');
		
		/**
		 * if the list is empty, get any image
		 */
		
		if (count($file_list) == 0) {
		
			$file_list = $this->listFiles($node_id);
		
		}
		
		/**
		 * return first item from the list
		 */
		 
		return $file_list[0];
		
	}
	
}
