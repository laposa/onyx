<?php
/**
 * Onxshop global functions
 * KEEP IT SMALL
 *
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

/**
 * Every system message should be process through this function
 *
 * @param string message Text of message.
 * @param string type 'ok' or 'error' message
 * @param int level 0 - for view every time 1, 2 - debug levels
 & @param string error_class - (CSS) class
 * @return void
 * @access public
 */
 
function msg($msg, $type = "ok", $level = 0, $error_class = '') {

	global $_SESSION;
	
	/**
	 * convert array or object to string
	 */
	 
	if (is_array($msg) || is_object($msg)) $msg = print_r($msg, true);

	/**
	 * benchmark
	 */
	 
	if (ONXSHOP_BENCHMARK && ONXSHOP_IS_DEBUG_HOST) {
		$time_current = microtime(true);
		$time = $time_current - TIME_START;
		$time = round($time, 4);
		$msg = "{$time}s: $msg";
	}
	
	/**
	 * display only to debug host or 
	 */
	 
	if(ONXSHOP_IS_DEBUG_HOST || $level == 0) {
	    
	    if (!isset($_SESSION['messages'])) $_SESSION['messages'] = '';
	    
	    if ($level <= ONXSHOP_DEBUG_LEVEL) {
	    
	        $msg_safe = htmlspecialchars($msg);
			
	        switch ($type) {
	        	
	            case 'error':
	                $message = "<p class='onxshop_error_msg level_$level $error_class'>{$msg_safe}</p>\n";
	                if (is_object($GLOBALS['fb_logger'])) $GLOBALS['fb_logger']->log($msg, Zend_Log::ERR);
	            break;
	            case 'ok':
	                $message = "<p class='onxshop_ok_msg level_$level $error_class'>{$msg_safe}</p>\n";
	                if (is_object($GLOBALS['fb_logger'])) $GLOBALS['fb_logger']->log($msg, Zend_Log::INFO);
	            break;
	        }
	    
	    	/**
	    	 * direct output or store in _SESSION
	    	 */
	    	   
	        if (ONXSHOP_DEBUG_DIRECT == true) echo $message;
	        else if ($level == 0 || !is_object($GLOBALS['fb_logger'])) $_SESSION['messages'] .= $message;
		
			
			/**
			 * write to debug file
			 */
			 
			if (ONXSHOP_DEBUG_FILE) {
				
				$messages_dir = ONXSHOP_PROJECT_DIR . "var/log/messages/";
				
				if (!is_dir($messages_dir)) mkdir($messages_dir);
				
				if (is_dir($messages_dir) && is_writable($messages_dir)) {
					$time = strftime("%D %T", time());
					$session_id = session_id();
					$type = strtoupper($type);
					$filename = "$messages_dir{$_SERVER['REMOTE_ADDR']}-$session_id.log";
					file_put_contents($filename, "$time $type: $msg\n", FILE_APPEND);
				}
			}

	    }
	}
}

/**
 * onxshop aware http forward
 *
 * @param unknown_type $request
 * @param unknown_type $type
 * type = 0: local path
 * type = 1: router syntax
 * type = 2: external URL
 */
 
function onxshopGoTo($request, $type = 0) {

	msg("calling onxshopGoTo($request, $type)", 'ok', 2);
	
	session_write_close();
	
	if ($_SERVER['HTTPS']) $protocol = 'https';
	else $protocol = 'http';

	//protection against HTTP CRLF injection
	$request = preg_replace("/\r\n/", "", $request);
	
	if ($type == 0) {
	
		$request = ltrim($request, '/');
	
		if (preg_match('/^page\/[0-9]*$/', $request)) {
		
			$request = translateURL($request);
			header("Location: $protocol://{$_SERVER['HTTP_HOST']}$request");
		
		} else {
			
			header("Location: $protocol://{$_SERVER['HTTP_HOST']}/$request");
		}
		
	} else if ($type == 1) {

		$router = new Onxshop_Router();
		
		$Onxshop = $router->processAction($request);
		
		$output = $Onxshop->finalOutput();

		echo $output;
		
	} else if ($type == 2) {
	
		header("Location: $request");
	
	} else {
	
		header("Location: $protocol://{$_SERVER['HTTP_HOST']}/$request");
	
	}
	
	//exit application processing immediately
	exit;
}

/**
 * global function to translate URLs using common_uri_mapping
 *
 * @param unknown_type $request
 * @return unknown
 */
 
function translateURL($request) {

	require_once('models/common/common_uri_mapping.php');
	$Mapping = new common_uri_mapping();
	
    if ($Mapping->conf['seo']) {
    	$seo = $Mapping->stringToSeoUrl("/$request");
    	return $seo;
    } else {
    	return "/$request";
    }
    		
}

/**
 * return active template directory
 *
 * @param unknown_type $file
 * @param unknown_type $prefix
 * @return unknown
 */

function getTemplateDir($file, $prefix = '') {

	if (file_exists(ONXSHOP_PROJECT_DIR . "templates/$prefix$file")) {
		$template_dir = ONXSHOP_PROJECT_DIR . "templates/$prefix";
	} else if (file_exists(ONXSHOP_DIR . "templates/$prefix$file")) {
		$template_dir = ONXSHOP_DIR . "templates/$prefix";
	} else {
		$template_dir = '';
	}
	
	return $template_dir;
}

/**
 * shell_exec wrapper calling local shell scripts
 *
 * @param unknown_type $command
 * @return unknown
 */
 
function local_exec($command) {

	
	$command = escapeshellcmd($command);
	msg("Calling: local_exec($command)", "ok", 2);
	//explode to get filename
	$c = explode(" ", $command);

	$command_file = ONXSHOP_PROJECT_DIR . 'bin/' . $c[0];
	$command_full = ONXSHOP_PROJECT_DIR . 'bin/' . $command;

	if (!file_exists($command_file)) {
		$command_file = ONXSHOP_DIR . 'bin/' . $c[0];
		$command_full = ONXSHOP_DIR . 'bin/' . $command;
	}

	if (file_exists($command_file)) {
		if (is_executable($command_file)) {
			msg("Calling: local_exec($command_full)", "ok", 3);
			ob_start();
			passthru($command_full, $status);
			$result = ob_get_contents();
			if ($status > 0) {
				msg("command: $command_full, return: $result, status: $status", 'error', 3);
			}
			ob_end_clean(); //Use this instead of ob_flush()
			return $result;
		} else {
			msg("Command $command_file is not executable", 'error');
			return false;
		}
		
	} else {
		msg("Command $command_file not found", 'error');
		return false;
	}
}

/**
 * used for data export
 *
 * @param unknown_type $string
 * @param unknown_type $quote_style
 * @return unknown
 */
 
function xmlentities($string, $quote_style=ENT_QUOTES) {
	
	static $utf8Entities, $htmlEntities;
	
	if(!isset($utf8Entities)) {		
	    $table = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
		$htmlEntities = array_values($table);
	  	$entitiesDecoded = array_keys($table);
	  	for($u=0, $num=count($entitiesDecoded); $u < $num; $u++) {
			$utf8Entities[$u] = '&#'.ord($entitiesDecoded[$u]).';';
	  	}
	}
  	
  	$string = str_replace($htmlEntities, $utf8Entities, $string);
  	
  	$search = array('&lsquo;', '&rsquo;', '&ldquo;', '&rdquo;', '&mdash;', '&ndash;', '&amp;'); 
	$replace = array("&#8216;", "&#8217;", '&#8220;', '&#8221;', '&#8212;', '&#8211;', '&#38;'); 
  	
    return str_replace($search, $replace, $string);
    
}

/**
 * Convert HTML to text
 */

function html2text($input, $remove_new_lines = false){

	require_once('lib/class.html2text.php');
	$h2t = new html2text($input);
	$plain_text = $h2t->get_text();
	
	if ($remove_new_lines) $plain_text = preg_replace('/\n/', ' ', $plain_text);
	
	return $plain_text;
}


##                ##
##  PHPMultiSort  ##
##                ##
// Takes:
//        $data,  multidim array
//        $keys,  array(array(key=>col1, sort=>desc), array(key=>col2, type=>numeric))

function php_multisort($data,$keys){
	
	if (!is_array($data)) return false;
 
	if (count($data) == 0) return array();
	
	// List As Columns
	foreach ($data as $key => $row) {
		foreach ($keys as $k){
			$cols[$k['key']][$key] = $row[$k['key']];
		}
	}
	
	// List original keys
	$idkeys=array_keys($data);
	// Sort Expression
	$i=0;
	foreach ($keys as $k){
		if($i>0){$sort.=',';}
		$sort.='$cols['.$k['key'].']';
		if($k['sort']){$sort.=',SORT_'.strtoupper($k['sort']);}
		if($k['type']){$sort.=',SORT_'.strtoupper($k['type']);}
		$i++;
	}
	$sort.=',$idkeys';
	// Sort Funct
	$sort='array_multisort('.$sort.');';
	eval($sort);
	// Rebuild Full Array
	foreach($idkeys as $idkey){
		$result[$idkey]=$data[$idkey];
	}
	
	return $result;
} 

