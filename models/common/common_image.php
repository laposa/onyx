<?php
/**
 * Copyright (c) 2009-2019 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('models/common/common_file.php');

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
        'link_to_node_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'customer_id'=>array('label' => '', 'validation'=>'int', 'required'=>false)
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
    link_to_node_id integer,
    customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT
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
    
        if (array_key_exists('common_image', $GLOBALS['onyx_conf'])) $conf = $GLOBALS['onyx_conf']['common_image'];
        else $conf = array();
        
        
        /**
         * set default values if empty
         *
         * This settings doesn't have any effect in share/image_thumbnail.php
         * due to missing database connection for better performance.
         *
         * See https://github.com/laposa/onyx/issues/8
         * 
         * You can create local overwrite in ONYX_PROJECT_DIR . 'conf/common_image.php':
         *
         *  $GLOBALS['onyx_conf']['common_image'] = array(
         *      'width_max' => 1600,
         *      'thumbnail_width_max' => 1600
         *  );
         */
        
        if (!isset($conf['width_max']) || !is_numeric($conf['width_max'])) $conf['width_max'] = 2000;
        if (!isset($conf['height_max']) || !is_numeric($conf['height_max'])) $conf['height_max'] = 1000;
        if (!isset($conf['thumbnail_width_min']) || !is_numeric($conf['thumbnail_width_min'])) $conf['thumbnail_width_min'] = 25;
        if (!isset($conf['thumbnail_width_max']) || !is_numeric($conf['thumbnail_width_max'])) $conf['thumbnail_width_max'] = 2000;
        if (!isset($conf['thumbnail_step']) || !is_numeric($conf['thumbnail_step'])) $conf['thumbnail_step'] = 1;
        if (!isset($conf['jpeg_quality']) || !is_numeric($conf['jpeg_quality'])) $conf['jpeg_quality'] = 80;
                
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
        if (!is_readable(ONYX_PROJECT_DIR . $file)) return false;
        
        $thumbnails_directory =  "var/thumbnails/";
        $thumbnails_directory_rp = ONYX_PROJECT_DIR . $thumbnails_directory;
        
        //prepare variables
        if (is_numeric($required_height)) {
            $directory =  "{$thumbnails_directory_rp}{$required_width}x{$required_height}";
            $thumb_file = "{$thumbnails_directory}{$required_width}x{$required_height}/" . md5($file);
        } else {
            $directory = "{$thumbnails_directory_rp}$required_width";
            $thumb_file = "{$thumbnails_directory}$required_width/" . md5($file);
        }
        
        $file_rp = ONYX_PROJECT_DIR . $file;
        
        $image_resize_options = "_{$method}_{$gravity}_{$fill}"; // TODO check valid options
        if ($image_resize_options == "___") $image_resize_options = "";
        if ($image_resize_options) $thumb_file = $thumb_file . $image_resize_options;
        
        $thumb_file_rp = ONYX_PROJECT_DIR . $thumb_file;
        
        //check if the destination directory exists
        if (!is_readable($directory)) {
            
            //check if thumbnails directory exists
            if (!is_readable($thumbnails_directory_rp)) {
                if (!mkdir($thumbnails_directory_rp)) {
                    msg("common_image.resize(): Cannot create folder $thumbnails_directory_rp", 'error');
                }
            }
            
            if (!mkdir($directory)) {
                msg("common_image.resize(): Cannot create folder $directory", 'error');
            }
        }
        
        //determinate real image size
        $size = common_image::getImageSize($file_rp);
        
        // i.e. SVG will return false
        if (!is_array($size)) return false;
        
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
         * see  http://www.imagemagick.org/Usage/thumbnails/#cut
         *      http://www.imagemagick.org/Usage/resize/#shrink
         */
        if (is_numeric($required_height)) {
            $background = common_image::supportsTransparency($file_rp) ? 'none' : 'white';
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
            
            $colorspace = self::isOldImageMagickVersion() ? 'RGB' : 'sRGB';
            $shell_command = "convert {$file_rp_escaped} -auto-orient -colorspace {$colorspace} -depth 8 -quality {$jpeg_quality} -thumbnail {$width}x{$height}{$fill} {$other_im_params} {$thumb_file_rp_escaped}";
            
            $result = local_exec($shell_command);
            
            if (is_readable($thumb_file_rp)) return $thumb_file;
            else return false;
        }
            
    }


    /**
     * Determines if ImageMagick is of version less than 6.7.5.
     */
    static protected function isOldImageMagickVersion() {

        $shell_command = "convert -version";
        $result = local_exec($shell_command);
        if (preg_match('/Version\:\s+ImageMagick\s+(\d)\.(\d)\.(\d)/', $result, $matches)) {
            if ($matches[1] < 6) return true;
            if ($matches[1] == 6 && $matches[2] < 7) return true;
            if ($matches[1] == 6 && $matches[2] == 7 && $matches[3] < 5) return true;
        }

        return false;
    }

    /**
     * getTeaserImageForNodeId
     */
     
    public function getTeaserImageForNodeId($node_id, $role = 'teaser') {
                 
        return $this->getImageForNodeId($node_id, $role = 'teaser');
        
    }
    
    /**
     * getImageForNodeId
     */
     
    public function getImageForNodeId($node_id, $role = 'teaser') {
        
        /**
         * try to get explicit image role
         */
         
        $file_list = $this->listFiles($node_id, $role);
        
        /**
         * if the list is empty, get any image
         */
        
        if (count($file_list) == 0) {
        
            $file_list = $this->listFiles($node_id);
        
        }
        
        /**
         * return first item from the list
         */
         
        return $file_list[0] ?? [];
        
    }

    /**
     * Check whatever given image type supports alpha transparency (such as PNGs and GIFs)
     * @param  string  $filename Image file path
     * @return boolean
     */
    public static function supportsTransparency($filename)
    {
        if (!file_exists($filename)) return false;

        $type = exif_imagetype($filename);

        switch ($type) {
            case IMAGETYPE_GIF:
            case IMAGETYPE_PNG:
                return true;
        }

        return false;
    }

    /**
     * getAuthorStats
     */
     
    public function getAuthorStats($customer_id) {
        
        if (!is_numeric($customer_id)) return false;
        
        $stats = array();
        
        // this uses list from templates/bo/component/file_role
        $stats['total'] = $this->count("customer_id = $customer_id");
        $stats['main'] = $this->count("role = 'main' AND customer_id = $customer_id");
        $stats['teaser'] = $this->count("role = 'teaser' AND customer_id = $customer_id");
        $stats['header'] = $this->count("role = 'header' AND customer_id = $customer_id");
        $stats['feature'] = $this->count("role = 'feature' AND customer_id = $customer_id");
        $stats['promotion'] = $this->count("role = 'promotion' AND customer_id = $customer_id");
        $stats['background'] = $this->count("role = 'background' AND customer_id = $customer_id");
        $stats['RTE'] = $this->count("role = 'RTE' AND customer_id = $customer_id");
        $stats['opengraph'] = $this->count("role = 'opengraph' AND customer_id = $customer_id");
                
        return $stats;
        
    }
    
}
