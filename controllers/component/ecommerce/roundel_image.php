<?php
/** 
 * Copyright (c) 2013-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Ecommerce_Roundel_Image extends Onxshop_Controller {

	protected $font_path;
	protected $width;
	protected $height;
	protected $aa_factor;
	protected $bg_color_hex;
	protected $text_color_hex;
	
	const CACHE_DIRECTORY = ONXSHOP_PROJECT_DIR . "var/roundels/";

	/**
	 * main action
	 */
	
	public function mainAction()
	{
		
		if (preg_match("/^IMAGE/", $this->GET['bgcolor'])) {
			
			/**
			 * hard linked roundel image, pass to image_thumbnail script
			 */
			 
			$_GET['image'] = preg_replace("/^IMAGE /", "", $this->GET['bgcolor']);
			if (!is_numeric($_GET['width'])) $_GET['width'] = 200; // make sure mandatory width parameter is set
			include ('share/image_thumbnail.php');
			exit;
			
		} else {
		
			/**
			 * standard roundel generator
			 */
			 
			$this->initParams();
			$this->checkCache();
	
			$this->initCanvas();
			$this->drawCircle();
			$this->calculateTextAttibutes();
			$this->drawText();
			$this->outputToBrowser();
			
		}
		
		return true;
		
	}
	
	/**
	 * initParams
	 */

	protected function initParams()
	{
		// anti-aliasing factor
		$this->aa_factor = 4;

		// canvas size
		$this->width = (isset($this->GET['width']) ? $this->GET['width'] : 140) * $this->aa_factor;
		$this->height = $this->width;

		// colours
		$this->bg_color_hex = (isset($this->GET['bgcolor']) ? $this->GET['bgcolor'] : 'FFFF00');
		$this->text_color_hex = (isset($this->GET['textcolor']) ? $this->GET['textcolor'] : 'FF0000');

		// font
		$this->font_path = ONXSHOP_PROJECT_DIR . '/public_html/fonts/roundels.ttf';

		// text
		$this->text = array();

		if (!empty($this->GET['text1']) && is_numeric($this->GET['size1'])) 
			$this->text[] = array('text' => $this->GET['text1'], 'size' => $this->GET['size1']);

		if (!empty($this->GET['text2']) && is_numeric($this->GET['size2'])) 
			$this->text[] = array('text' => $this->GET['text2'], 'size' => $this->GET['size2']);

		if (!empty($this->GET['text3']) && is_numeric($this->GET['size3'])) 
			$this->text[] = array('text' => $this->GET['text3'], 'size' => $this->GET['size3']);

		if (count($this->text) == 0) die();
	}
	
	/**
	 * checkCache
	 */

	protected function checkCache()
	{
		$filename = md5(serialize(array(
			'width' => $this->width,
			'height' => $this->height,
			'bg_color_hex' => $this->bg_color_hex,
			'text_color_hex' => $this->text_color_hex,
			'text1' => (string) $this->text[0]['text'],
			'size1' => (int) $this->text[0]['size'],
			'text2' => (string) $this->text[1]['text'],
			'size2' => (int) $this->text[1]['size'],
			'text3' => (string) $this->text[2]['text'],
			'size3' => (int) $this->text[2]['size'],
		))) . ".png";

		$this->cachedFilePath = self::CACHE_DIRECTORY . $filename;

		if (file_exists($this->cachedFilePath)) {
			// cache hit
			header("Content-Type: image/png");
			echo file_get_contents($this->cachedFilePath);
			exit();
		}
	}
	
	/**
	 * initCanvas
	 */

	protected function initCanvas()
	{
		// init canvas 
		$this->canvas = imagecreatetruecolor($this->width, $this->height);

		// allow transparency
		imagealphablending($this->canvas, false);
		imagesavealpha($this->canvas, true);
		$transparent = imagecolorallocatealpha($this->canvas, $this->getColorComponent($this->bg_color_hex, 0), 
			$this->getColorComponent($this->bg_color_hex, 1), $this->getColorComponent($this->bg_color_hex, 2), 127);
		imagefilledrectangle($this->canvas, 0, 0, $this->width, $this->height, $transparent);

		// convert hex colors to GD color
		$this->bg_color = imagecolorallocate($this->canvas, $this->getColorComponent($this->bg_color_hex, 0), 
			$this->getColorComponent($this->bg_color_hex, 1), $this->getColorComponent($this->bg_color_hex, 2));

		$this->text_color = imagecolorallocate($this->canvas, $this->getColorComponent($this->text_color_hex, 0), 
			$this->getColorComponent($this->text_color_hex, 1), $this->getColorComponent($this->text_color_hex, 2));

	}
	
	/**
	 * drawCircle
	 */

	protected function drawCircle()
	{
		imagefilledellipse($this->canvas, round($this->width / 2), round($this->height / 2), 
			$this->width, $this->height, $this->bg_color);
	}

	/**
	 * calculateTextAttibutes
	 */
	 
	protected function calculateTextAttibutes()
	{
		$size = 10 * $this->aa_factor;
		$center_x = round($this->width / 2);
		$center_y = round($this->height / 2);
		$radius = round($this->width / 2 * 0.9);
		$fitsIn = true;

		while ($fitsIn) {

			$total_height = 0;
			$spacer_factor = 5;

			// calculate the total height of all lines first
			foreach ($this->text as $line) {

				if (strlen($line['text']) > 0 && $line['size'] > 0) {

					$font_size = round($size * ($line['size'] / 100));
					$spacer = round($font_size / $spacer_factor);
					$total_height += $font_size + 2 * $spacer;

				}
			}

			$y = $this->height / 2 - $total_height / 2;

			foreach ($this->text as $key => $line) {

				if (strlen($line['text']) > 0 && $line['size'] > 0) {

					$font_size = round($size * ($line['size'] / 100));
					$spacer = round($font_size / 5);
					$y += $spacer + $font_size;

					$box = imagettfbbox($font_size, 0, $this->font_path, $line['text']);
					$text_width = $box[2] - $box[0];
					$text_height = $box[3] - $box[5];

					$x = round(($this->width - $text_width) / 2);

					$fitsIn = $fitsIn &&
						$this->isPointInCircle($center_x, $center_y, $radius, $x + $box[0], $y + $box[1]) &&
						$this->isPointInCircle($center_x, $center_y, $radius, $x + $box[2], $y + $box[3]) &&
						$this->isPointInCircle($center_x, $center_y, $radius, $x + $box[4], $y + $box[5]) &&
						$this->isPointInCircle($center_x, $center_y, $radius, $x + $box[6], $y + $box[7]);

					$this->text[$key]['y'] = $y;
					$this->text[$key]['x'] = $x;
					$this->text[$key]['font_size'] = $font_size;

					$y += $spacer;

				}
			}

			$size += round($this->width / 30);

		}

	}

	/**
	 * drawText
	 */
	 
	protected function drawText()
	{
		foreach ($this->text as $line) {

			if (strlen($line['text']) > 0 && $line['size'] > 0) {

				imagettftext($this->canvas, $line['font_size'], 0, $line['x'], $line['y'], 
					(-1) * $this->text_color, $this->font_path, $line['text']);

			}
		}

	}
	
	/**
	 * isPointInCircle
	 */

	protected function isPointInCircle($center_x, $center_y, $radius, $x, $y)
	{
        $dx = $center_x - $x;
        $dy = $center_y - $y;
        $dx *= $dx;
        $dy *= $dy;
        $distance_squared = $dx + $dy;
        $radius_squared = $radius * $radius;
        return ($distance_squared <= $radius_squared);

	}
	
	/**
	 * outputToBrowser
	 */
	 
	protected function outputToBrowser()
	{
		// init result canvas
		$result = imagecreatetruecolor($this->width / $this->aa_factor, $this->height / $this->aa_factor);
		imagealphablending($result, false);
		imagesavealpha($result, true);
		$transparent = imagecolorallocatealpha($this->canvas, $this->getColorComponent($this->bg_color_hex, 0), 
			$this->getColorComponent($this->bg_color_hex, 1), $this->getColorComponent($this->bg_color_hex, 2), 127);
		imagefilledrectangle($result, 0, 0, $this->width / $this->aa_factor, $this->height / $this->aa_factor, $transparent);

		// perform samling
		imagecopyresampled($result, $this->canvas, 0, 0, 0, 0, $this->width / $this->aa_factor, 
			$this->height / $this->aa_factor, $this->width, $this->height);

		imagedestroy($this->canvas);

		// save to cache
		if (!file_exists(self::CACHE_DIRECTORY)) mkdir(self::CACHE_DIRECTORY);
		if (is_writable(self::CACHE_DIRECTORY)) imagepng($result, $this->cachedFilePath);
		
		// output to browser
		header("Content-Type: image/png");
		imagepng($result);
		imagedestroy($result);

		exit();
	}

	/**
	 * getColorComponent
	 */
	 
	protected function getColorComponent($hexRGB, $component)
	{
		$hexRGB = str_replace("#", "", $hexRGB);

		if (strlen($hexRGB) == 3) {
			$r = hexdec(substr($hexRGB, 0, 1) . substr($hexRGB, 0, 1));
			$g = hexdec(substr($hexRGB, 1, 1) . substr($hexRGB, 1, 1));
			$b = hexdec(substr($hexRGB, 2, 1) . substr($hexRGB, 2, 1));
		} else {
			$r = hexdec(substr($hexRGB, 0, 2));
			$g = hexdec(substr($hexRGB, 2, 2));
			$b = hexdec(substr($hexRGB, 4, 2));
		}
		$rgb = array($r, $g, $b);
		return $rgb[$component];
	}
}
