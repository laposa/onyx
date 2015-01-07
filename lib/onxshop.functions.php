<?php
/**
 * Onxshop global functions
 * KEEP IT SMALL
 *
 * Copyright (c) 2005-2015 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

/**
 * Every system message should be process through this function
 *
 * @param string message Text of message.
 * @param string type 'ok' or 'error' message
 * @param int level 0 - for view every time 1, 2 - debug levels
 * @param string error_class - (CSS) class
 * @return boolean
 * @access public
 */
 
function msg($msg, $type = "ok", $level = 0, $error_class = '') {
	
    if ($level > ONXSHOP_DEBUG_LEVEL) return false; // process only if matching log level
	
	global $_SESSION;
	
	/**
	 * convert array or object to string
	 */
	 
	if (is_array($msg) || is_object($msg)) $msg = print_r($msg, true);

	/**
	 * including timing for benchmark
	 */
	 
	if (ONXSHOP_BENCHMARK && ONXSHOP_IS_DEBUG_HOST) {
		$time_current = microtime(true);
		$time = $time_current - TIME_START;
		$time = round($time, 4);
		$msg = "{$time}s: $msg";
	}

	/**
	 * include backtrace (only with errors)
	 */
	 
	if (ONXSHOP_DEBUG_INCLUDE_BACKTRACE && $type == 'error') {
	
		$backtrace = debug_backtrace();
		
		// format same way as debug_print_backtrace, i.e. #0  c() called at [/tmp/include.php:10]
		$backtrace_formatted = '';
		
		foreach ($backtrace as $k=>$item) {
			
			$backtrace_formatted .= " #$k  {$item['function']} called at [{$item['file']}:{$item['line']}]";
			
		}
		
	}
	
	/**
	 * include user info
	 */
	 
	if (ONXSHOP_DEBUG_INCLUDE_USER_ID) {
		
		$user_info = '';
		
		if ($backoffice_user_email = $_SESSION['authentication']['user_details']['email']) {
			$user_info .= "BO user: {$backoffice_user_email} ";
		}
		
		if ($customer_id = $_SESSION['client']['customer']['id']) {
			$user_info .= "Customer ID: $customer_id ";
		}
		
		if ($user_info) $user_info = "(" . rtrim($user_info) . ") ";
	}
    
	/**
	 * store in session and manage in controller where message can be parsed to the template
	 * level 0 messages are always saved to session to be shown in template
	 */
	
	if (ONXSHOP_DEBUG_OUTPUT_SESSION || $level == 0) {
	    
		if (!isset($_SESSION['messages'])) $_SESSION['messages'] = '';
		
		if ($type == 'error') $_SESSION['messages'] .= "<p class='onxshop_error_msg level_$level $error_class'>". htmlspecialchars($msg) ."</p>\n";
		else $_SESSION['messages'] .= "<p class='onxshop_ok_msg level_$level $error_class'>". htmlspecialchars($msg) ."</p>\n";
		
	}
	
	/**
	 * firebug
	 */
	 
	if (ONXSHOP_DEBUG_OUTPUT_FIREBUG) {
		
		if (is_object($GLOBALS['fb_logger'])) {
			
			if ($type == 'error') $GLOBALS['fb_logger']->log($msg, Zend_Log::ERR);
			else $GLOBALS['fb_logger']->log($msg, Zend_Log::INFO);
		
		}
	
	}
	
	/**
	 * direct output - send immediatelly to client
	 */
	 
	if (ONXSHOP_DEBUG_OUTPUT_DIRECT) echo $msg;
	
	/**
	 * write to debug file
	 */
	 
	if (ONXSHOP_DEBUG_OUTPUT_FILE) {
		
		$messages_dir = ONXSHOP_PROJECT_DIR . "var/log/messages/";
		
		if (!is_dir($messages_dir)) mkdir($messages_dir);
		
		if (is_dir($messages_dir) && is_writable($messages_dir)) {
			$time = strftime("%F %T", time()); // use ISO date format to allow easy sorting
			$session_id = session_id();
			$type = strtoupper($type);
			$filename = "$messages_dir{$_SERVER['REMOTE_ADDR']}-$session_id.log";
			file_put_contents($filename, "$time $type: $msg\n", FILE_APPEND);
		}
	}
	
	/**
	 * send to standard PHP error log
	 */
	 
	if (ONXSHOP_DEBUG_OUTPUT_ERROR_LOG) {
		
		error_log($user_info . $msg . $backtrace_formatted);
		
	}

	return true;
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
		
		if (preg_match('/^(page\/[0-9]{1,})(.*)$/', $request, $matches)) {
			
			$request_path = translateURL($matches[1]);
			$request_params = $matches[2];
			
			header("Location: $protocol://{$_SERVER['HTTP_HOST']}{$request_path}{$request_params}");
		
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
 * @deprecated
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

/**
 * parse textile
 */
	 
function textile($text) {

	require_once('Zend/Markup.php');
	
	// Creates instance of Zend_Markup_Renderer_Html,
	// with Zend_Markup_Parser_BbCode as its parser
	$textilecode = Zend_Markup::factory('Textile');
	
	return $textilecode->render($text);
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

/**
 * Limits the string based on the character count. Preserves complete words
 * so the character count may not be exactly as specified.
 */
function character_limiter($str, $n = 500, $end_char = '&hellip;')
{
	if (strlen($str) < $n) return $str;
	// a bit complicated, but faster than preg_replace with \s+
	$str = preg_replace('/ {2,}/', ' ', str_replace(array("\r", "\n", "\t", "\x0B", "\x0C"), ' ', $str));
	if (strlen($str) <= $n) return $str;
	$out = '';
	foreach (explode(' ', trim($str)) as $val) {
		$out .= $val.' ';
		if (strlen($out) >= $n) {
			$out = trim($out);
			return (strlen($out) === strlen($str)) ? $out : $out . $end_char;
		}
	}
}

/**
 * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
 * keys to arrays rather than overwriting the value in the first array with the duplicate
 * value in the second array, as array_merge does. I.e., with array_merge_recursive,
 * this happens (documented behavior):
 *
 * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('org value', 'new value'));
 *
 * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
 * Matching keys' values in the second array overwrite those in the first array, as is the
 * case with array_merge, i.e.:
 *
 * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('new value'));
 *
 * Parameters are passed by reference, though only for performance reasons. They're not
 * altered by this function.
 *
 * @param array $array1
 * @param array $array2
 * @return array
 * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
 * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
 */
function array_merge_recursive_distinct ( array &$array1, array &$array2 )
{
  $merged = $array1;

  foreach ( $array2 as $key => &$value )
  {
    if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
    {
      $merged [$key] = array_merge_recursive_distinct ( $merged [$key], $value );
    }
    else
    {
      $merged [$key] = $value;
    }
  }

  return $merged;
}

/**
 * Prepend given $str with $prefix if given $str is not empty
 */
function prefix($str, $prefix)
{
	if (!empty($str)) return $prefix . $str;
	return $str;
}


/**
 * Append given $suffix to $str if given $str is not empty
 */
function suffix($str, $suffix)
{
	if (!empty($str)) return $str . $suffix;
	return $str;
}

/**
 * Create hash from a given string
 * Uses ONXSHOP_ENCRYPTION_SALT as a salt.
 * Returs false if ONXSHOP_ENCRYPTION_SALT is not set or empty.
 * 
 * @return String Hashed value (sha256)
 */
function makeHash($value)
{
	if (!defined('ONXSHOP_ENCRYPTION_SALT') || ONXSHOP_ENCRYPTION_SALT == '') {
		msg("ONXSHOP_ENCRYPTION_SALT not set", "error", 1);
		return false;
	}

	return hash('sha256', ONXSHOP_HASH_SALT . ( (string) $value ));
}

/**
 * Compare a given string with its hash
 * Uses ONXSHOP_ENCRYPTION_SALT as a salt.
 * Returs false if ONXSHOP_ENCRYPTION_SALT is not set or empty
 * or if the hashes don't match.
 * 
 * @return Boolean
 */
function verifyHash($value, $hash)
{
	if (!defined('ONXSHOP_ENCRYPTION_SALT') || ONXSHOP_ENCRYPTION_SALT == '') {
		msg("ONXSHOP_ENCRYPTION_SALT not set", "error", 1);
		return false;
	}

	return (hash('sha256', ONXSHOP_HASH_SALT . ( (string) $value )) == strtolower(trim($hash)));
}

/**
 * Check if given date in format Y-m-d
 * is a valid date
 */
function isValidDate($date)
{
	$d = DateTime::createFromFormat('Y-m-d', $date);
	return $d && $d->format('Y-m-d') == $date;
}

/**
 * onxshop_flush_cache
 * TODO: this should be using $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
 */
 
function onxshop_flush_cache() {
	
	require_once('models/common/common_file.php');
	$File = new common_file();
		
	// file backend cache
	if ($File->rm(ONXSHOP_PROJECT_DIR . "var/cache/*")) $file_clear_status = true;
	else $file_clear_status = false;
	
	// APC backend cache
	if (function_exists('apc_clear_cache'))  {
		$apc_clear_status = apc_clear_cache('user');
	} else {
		$apc_clear_status = true;
	}
	
	// Libmemcached
	if (class_exists('Memcached')) {
		$m = new Memcached();
		$m->addServer('localhost', 11211);
		$libmemcached_clear_status = $m->flush();
	} else {
		$libmemcached_clear_status = true;
	}
	 
	if ($file_clear_status && $apc_clear_status && $libmemcached_clear_status) return true;
	
}
