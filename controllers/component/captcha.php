<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Component_Captcha extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		// captcha
		$word = $this->generateRandomWord();

		// save code to session
		$_SESSION['captcha'][$this->GET['node_id']] = $word;

		// show image
		$this->tpl->assign("CAPTCHA_IMAGE", $this->generateCaptchaImage($word));

		return true;
	}
	
	protected function generateRandomWord($length = 5)
	{
		$str = '';
		for($i = 0; $i < $length; $i++) $str .= chr(rand(97, 122));
		return $str;
	}

	protected function generateCaptchaImage($word, $width = 70, $height = 22, $fontSize = 12, $textColor = "000000", $backgroundColor = "DDDDDD")
	{
		// setup blank image
		$img = imagecreatetruecolor($width, $height);
		$front = imagecolorallocate($img, base_convert(substr($textColor, 0, 2), 16, 10), base_convert(substr($textColor, 2, 2), 16, 10), base_convert(substr($textColor, 4, 2), 16, 10));
		$back = imagecolorallocate($img, base_convert(substr($backgroundColor, 0, 2), 16, 10), base_convert(substr($backgroundColor, 2, 2), 16, 10), base_convert(substr($backgroundColor, 4, 2), 16, 10));
		imagefilledrectangle($img, 0, 0, $width, $height, $back);

		// draw text
		$font = ONXSHOP_DIR . "/share/fonts/VeraMoBd.ttf";
		$box = imagettfbbox($fontSize, 0, $font, $word);
		$textWidth = $box[2] - $box[0];
		$y = ($height / 2) + ($fontSize / 2);
		$x = ($width - $textWidth) / 2;
		imagettftext($img, $fontSize, 0, $x, $y, $front, $font, $word);

		// output image
		ob_start();
		imagepng($img);
		$data = ob_get_contents();
		ob_end_clean();

		// return encoded data
		return "data:image/png;base64," . base64_encode($data);
	}

}
