<?php
/** 
 * Copyright (c) 2005-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Component_Captcha extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {

        /** 
         * get input variables
         */
         
        if ($this->GET['width'] >= 10 && $this->GET['width'] <= 200) $width = (int) $this->GET['width'];
        else $width = 70;

        if ($this->GET['height'] >= 10 && $this->GET['height'] <= 100) $height = (int) $this->GET['height'];
        else $height = 22;

        if ($this->GET['length'] >= 3 && $this->GET['length'] <= 10) $length = (int) $this->GET['length'];
        else $length = 5;

        if ($this->GET['fontSize'] >= 8 && $this->GET['fontSize'] <= 30) $fontSize = (int) $this->GET['fontSize'];
        else $fontSize = 12;

        if (preg_match('/[0-9a-fA-F]{6}/', $this->GET['textColor'])) $textColor = (string) $this->GET['textColor'];
        else $textColor = "000000";

        if (preg_match('/[0-9a-fA-F]{6}/', $this->GET['backgroundColor'])) $backgroundColor = (string) $this->GET['backgroundColor'];
        else $backgroundColor = "DDDDDD";

        if (is_numeric($this->GET['id'])) $node_id = (int) $this->GET["id"];
        else $node_id = 0;
        
        // captcha
        $word = $this->generateRandomWord();

        // save code to session
        if (!is_array($_SESSION['captcha'])) $_SESSION['captcha'] = array();
        $_SESSION['captcha'][$node_id] = $word;

        // show image
        $this->generateCaptchaImage($word, $width, $height, $fontSize, $textColor, $backgroundColor);
        exit();

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
        $font = ONYX_DIR . "/share/fonts/VeraMoBd.ttf";
        $box = imagettfbbox($fontSize, 0, $font, $word);
        $textWidth = $box[2] - $box[0];
        $y = ($height / 2) + ($fontSize / 2);
        $x = ($width - $textWidth) / 2;
        imagettftext($img, $fontSize, 0, $x, $y, $front, $font, $word);

        // output image
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
        header("Cache-Control: no-store, no-cache, must-revalidate"); 
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache"); 
        header("Content-Type: image/png");
        imagepng($img);
    }

}
