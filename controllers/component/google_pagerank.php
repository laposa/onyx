<?php
/** 
 * Copyright (c) 2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Google_Pagerank extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		$url_to_check = $this->GET['url_to_check'];
		
		if ($url_to_check) {
			$google_pagerank = $this->pagerank("http://$url_to_check/");
			$this->tpl->assign('GOOGLE_PAGERANK', $google_pagerank);
			$this->tpl->parse('content.result');
		}
		return true;
		
	}
	
	/**
	 * http://fusionswift.com/blog/2010/04/google-pagerank-script-in-php/
	 */
	 
	function genhash ($url) {
		$hash = 'Mining PageRank is AGAINST GOOGLE\'S TERMS OF SERVICE. Yes, I\'m talking to you, scammer.';
		$c = 16909125;
		$length = strlen($url);
		$hashpieces = str_split($hash);
		$urlpieces = str_split($url);
		for ($d = 0; $d < $length; $d++) {
			$c = $c ^ (ord($hashpieces[$d]) ^ ord($urlpieces[$d]));
			$c = $this->zerofill($c, 23) | $c << 9;
	 	}
		return '8' . $this->hexencode($c);
	}
	
	function zerofill($a, $b) {
		$z = hexdec(80000000);
	  	if ($z & $a) {
	  		$a = ($a>>1);
			$a &= (~$z);
			$a |= 0x40000000;
			$a = ($a>>($b-1));
		} else {
			$a = ($a>>$b);
		}
		return $a;
	}
	
	function hexencode($str) {
		$out  = $this->hex8($this->zerofill($str, 24));
		$out .= $this->hex8($this->zerofill($str, 16) & 255);
		$out .= $this->hex8($this->zerofill($str, 8 ) & 255);
		$out .= $this->hex8($str & 255);
		return $out;
	}
	
	function hex8 ($str) {
		$str = dechex($str);
		(strlen($str) == 1 ? $str = '0' . $str: null);
		return $str;
	}
	
	function pagerank($url) {
		$googleurl = 'http://toolbarqueries.google.com/search?features=Rank&sourceid=navclient-ff&client=navclient-auto-ff&googleip=O;66.249.81.104;104&ch=' . $this->genhash($url) . '&q=info:' . urlencode($url);
		if(function_exists('curl_init')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $googleurl);
			$out = curl_exec($ch);
			curl_close($ch);
		} else {
			$out = file_get_contents($googleurl);
		}
		return substr($out, 9);
	}
}
