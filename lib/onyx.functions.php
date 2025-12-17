<?php
/**
 * Onyx global functions
 * KEEP IT SMALL
 *
 * Copyright (c) 2005-2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('lib/onyx.container.php');
use Symfony\Component\HttpClient\HttpClient;

/**
 * Every system message should be processed through this function
 *
 * @param string message Text of message.
 * @param string type 'ok' or 'error' message
 * @param int level 0 - for view every time 1, 2 - debug levels
 * @param string error_class - (CSS) class
 * @return boolean
 * @access public
 */

function msg($msg, $type = "ok", $level = 0, $error_class = '') {

    $backtrace_formatted = '';

    if ($level > ONYX_DEBUG_LEVEL) return false; // process only if matching log level

    global $_SESSION;

    /**
     * convert array or object to string
     */

    if (is_array($msg) || is_object($msg)) $msg = print_r($msg, true);

    /**
     * including timing for benchmark
     */

    if (ONYX_BENCHMARK && ONYX_IS_DEBUG_HOST) {
        $time_current = microtime(true);
        $time = $time_current - TIME_START;
        $time = round($time, 4);
        $msg = "{$time}s: $msg";
    }

    /**
     * include backtrace (only with errors)
     */

    if (ONYX_DEBUG_INCLUDE_BACKTRACE && $type == 'error') {

        $backtrace = debug_backtrace();

        // format same way as debug_print_backtrace, i.e. #0  c() called at [/tmp/include.php:10]

        foreach ($backtrace as $k=>$item) {

            $backtrace_formatted .= " #$k  {$item['function']} called at [{$item['file']}:{$item['line']}]";

        }

    }

    /**
     * include user info
     */

    if (ONYX_DEBUG_INCLUDE_USER_ID) {

        $user_info = '';

        if (isset($_SESSION['authentication']['user_details']['email']) && $backoffice_user_email = $_SESSION['authentication']['user_details']['email']) {
            $user_info .= "BO user: {$backoffice_user_email} ";
        }

        if (isset($_SESSION['client']['customer']['id']) && $customer_id = $_SESSION['client']['customer']['id']) {
            $user_info .= "Customer ID: $customer_id ";
        }

        if ($user_info) $user_info = "(" . rtrim($user_info) . ") ";
    }

    /**
     * store in session and manage in controller where message can be parsed to the template
     * level 0 messages are always saved to session to be shown in template
     */

    if (ONYX_DEBUG_OUTPUT_SESSION || $level == 0) {

        if (!isset($_SESSION['messages'])) $_SESSION['messages'] = '';

        if ($type == 'error') $_SESSION['messages'] .= "<p class='onyx-error-msg level-$level $error_class'>". htmlspecialchars($msg) ."</p>\n";
        else $_SESSION['messages'] .= "<p class='onyx-ok-msg level-$level $error_class'>". htmlspecialchars($msg) ."</p>\n";

    }

    /**
     * direct output - send immediatelly to client
     */

    if (ONYX_DEBUG_OUTPUT_DIRECT) echo $msg;

    /**
     * write to debug file
     */

    if (ONYX_DEBUG_OUTPUT_FILE) {

        $messages_dir = ONYX_PROJECT_DIR . "var/log/messages/";

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

    if (ONYX_DEBUG_OUTPUT_ERROR_LOG) {

        error_log($user_info . $msg . $backtrace_formatted);

    }

    return true;
}

/**
 * onyxDetectProtocol to find if we are using SSL
 */

function onyxDetectProtocol() {

    if (ONYX_SSL == true) $protocol = 'https';
    else $protocol = 'http';

    return $protocol;
}

/**
 * onyxDetectPort to find non standard port
 */

function onyxDetectPort($protocol = false) {

    if (!in_array(ONYX_PORT, [80,443])) $port = ":" . ONYX_PORT;
    else $port = '';

    return $port;
}

/**
 * onyx aware http forward
 *
 * @param unknown_type $request
 * @param unknown_type $type
 * type = 0: local path
 * type = 1: router syntax
 * type = 2: external URL
 */

function onyxGoTo($request, $type = 0) {

    msg("calling onyxGoTo($request, $type)", 'ok', 2);

    session_write_close();

    $protocol = onyxDetectProtocol();

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

        $router = new Onyx_Router();

        $Onyx = $router->processAction($request);

        $output = $Onyx->finalOutput();

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
 * @param string $request
 * @return string
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
 * global function to getting clean title using common_uri_mapping
 *
 * @param string $text
 * @param string $and_string
 * @return string
 */

function onyxSlug($text, $and_string = I18N_AND) {

    $slug = str_replace('/', '-', trim($text));
    $slug = recodeUTF8ToAscii($slug);
    $slug = strtolower($slug);
    $slug = preg_replace("/\s/", "-", $slug);
    $slug = preg_replace("/&[^([a-zA-Z;)]/", $and_string . '-', $slug);
    $slug = preg_replace("/[^\w\-\/\.]/", '', $slug);
    $slug = preg_replace("/\-{2,}/", '-', $slug);
    $slug = trim($slug, '-');

    return $slug;
}

/**
 * global function to replace money_format removed in PHP 8.0
 *
 * @param float $amount
 * @return string
 */

function money_format($amount) {
    if (!is_numeric($amount)) {
        msg("money_format: amount is not numeric", "error", 1);
        return false;
    }

    $locale = $GLOBALS['onyx_conf']['global']['locale'] ?? 'en_GB.UTF-8';
    $currency = $GLOBALS['onyx_conf']['global']['default_currency'] ?? 'GBP';

    $fmt = new NumberFormatter($locale, NumberFormatter::CURRENCY);
    $currency = $fmt->formatCurrency((float) $amount, $currency);
    return $currency;
}

/**
 * return active template directory
 *
 * @param string $file
 * @param string $prefix
 * @return string
 * @deprecated
 */

function getTemplateDir($file, $prefix = '') {

    if (file_exists(ONYX_PROJECT_DIR . "templates/$prefix$file")) {
        $template_dir = ONYX_PROJECT_DIR . "templates/$prefix";
    } else if (file_exists(ONYX_DIR . "templates/$prefix$file")) {
        $template_dir = ONYX_DIR . "templates/$prefix";
    } else {
        $template_dir = '';
    }

    return $template_dir;
}

/**
 * find whether template file exists
 *
 * @param string $template_name
 * @return boolean
 *
 */

function templateExists($template_name) {

    if (file_exists(ONYX_PROJECT_DIR . 'templates/' . $template_name . '.html')) return true;
    if (file_exists(ONYX_DIR . 'templates/' . $template_name . '.html')) return true;
    else return false;

}

/**
 * shell_exec wrapper calling local shell scripts
 *
 * @param string $command
 * @return string|bool
 */

function local_exec($command) {

    $command = escapeshellcmd($command);
    msg("Calling: local_exec($command)", "ok", 2);
    //explode to get filename
    $c = explode(" ", $command);

    $command_file = ONYX_PROJECT_DIR . 'bin/' . $c[0];
    $command_full = ONYX_PROJECT_DIR . 'bin/' . $command;

    if (!file_exists($command_file)) {
        $command_file = ONYX_DIR . 'bin/' . $c[0];
        $command_full = ONYX_DIR . 'bin/' . $command;
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
 * Clean UTF8 for XML and JSON
 *
 * http://stackoverflow.com/questions/12229572/php-generated-xml-shows-invalid-char-value-27-message
 */

function utf8_for_xml($string) {

    return preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $string);

}

/**
 *
 * @param string $string
 * text in UTF8 encoding
 *
 * @return string
 * text recoded into ASCII
 */

function recodeUTF8ToAscii($string) {

    $string = trim($string);

    if (function_exists("recode_string")) {

        $string = recode_string("utf-8..flat", $string);

    } else if (function_exists("iconv")) {

        $string = iconv("UTF-8", "ASCII//TRANSLIT", $string);

    } else if (function_exists("mb_convert_encoding")) {

        $string = mb_convert_encoding($string, "HTML-ENTITIES", "UTF-8");
        $string = preg_replace('/\&(.)[^;]*;/', "\\1", $string);

    }

    return $string;
}

/**
 * Convert HTML to text
 */

function html2text($input, $remove_new_lines = false){
    $html = new \Html2Text\Html2Text($input);
    $plain_text = $html->getText();

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
        else {$sort='';}
        $sort.="\$cols['".$k['key']."']";
        if($k['sort'] ?? null){$sort.=',SORT_'.strtoupper($k['sort']);}
        if($k['type'] ?? null){$sort.=',SORT_'.strtoupper($k['type']);}
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
 * Uses ONYX_ENCRYPTION_SALT as a salt.
 * Returs false if ONYX_ENCRYPTION_SALT is not set or empty.
 *
 * @return string Hashed value (sha256)
 */
function makeHash($value)
{
    if (!defined('ONYX_ENCRYPTION_SALT') || ONYX_ENCRYPTION_SALT == '') {
        msg("ONYX_ENCRYPTION_SALT not set", "error", 1);
        return false;
    }

    return hash('sha256', ONYX_ENCRYPTION_SALT . ( (string) $value ));
}

/**
 * Compare a given string with its hash
 * Uses ONYX_ENCRYPTION_SALT as a salt.
 * Returs false if ONYX_ENCRYPTION_SALT is not set or empty
 * or if the hashes don't match.
 *
 * @return bool
 */
function verifyHash($value, $hash)
{
    if (!defined('ONYX_ENCRYPTION_SALT') || ONYX_ENCRYPTION_SALT == '') {
        msg("ONYX_ENCRYPTION_SALT not set", "error", 1);
        return false;
    }

    return (hash('sha256', ONYX_ENCRYPTION_SALT . ( (string) $value )) == strtolower(trim($hash)));
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
 * onyx_flush_cache
 *
 * @return bool
 */
function onyx_flush_cache() {
    // clean Symfony cache
    $registry = Onyx_Container::getInstance();
    $dbCacheClearStatus = $registry->get('onyx_db_cache')->clear();
    $pageCacheClearStatus = $registry->get('onyx_page_cache')->clear();
    $generalCacheClearStatus = $registry->get('onyx_cache')->clear();

    // remove all files in cache directory
    require_once('models/common/common_file.php');
    $File = new common_file();
    if ($File->rm(ONYX_PROJECT_DIR . "var/cache/*") && $File->rm(ONYX_PROJECT_DIR . "var/tmp/*")) $fileClearStatus = true;
    else $fileClearStatus = false;

    if ($dbCacheClearStatus && $pageCacheClearStatus && $generalCacheClearStatus && $fileClearStatus) return true;
    else return false;
}

/**
 * Format time in seconds and
 * add proper units.
 *
 * 3.552342 => 3.552 s
 * 0.552342 => 552 ms
 * 0.000342 => 0.34 ms
 */
function format_time($seconds) {
    if ($seconds > 1) return round($seconds, 3) . " s";
    $ms = $seconds * 1000;
    if ($ms > 1) return round($ms) . "&nbsp;ms";
    return round($ms, 2) . "&nbsp;ms";
}

/**
 * Dumps variable to the Tracy's debug bar
 * @param  mixed  $variable Variable to dump
 * @param  string $title    Variable name to show (optional)
 */
function bar_dump($variable, $title = null) {
    if (ONYX_TRACY) Tracy\Debugger::barDump($variable, $title);
    else var_dump($variable);
}

/**
 * Encode integer value using custom character set
 * @param  int $value Integer value to be encoded
 * @return string Encoded value
 */
function encodeInt($value)
{
    $codeset = "QRST12XY34FGwxyzABCDEHIJZ789abcdefpqrs56ijklmnouUVWtghvKLMNOP";
    $base = strlen($codeset);
    $encoded = "";
    while ($value > 0) {
      $encoded = substr($codeset, ($value % $base), 1) . $encoded;
      $value = floor($value/$base);
    }
    return $encoded;
}

/**
 * Decode integer from custom character set
 * @param  string $value Encoded value
 * @return int Decoded integer value
 */
function decodeInt($encoded)
{
    $codeset = "QRST12XY34FGwxyzABCDEHIJZ789abcdefpqrs56ijklmnouUVWtghvKLMNOP";
    $base = strlen($codeset);
    $c = 0;
    for ($i = strlen($encoded); $i; $i--) {
      $c += strpos($codeset, substr($encoded, (-1 * ( $i - strlen($encoded) )),1))
            * pow($base,$i-1);
    }
    return $c;
}

/**
 * Encode integer value using custom character set and append
 * salted MD5 checksum to allow verification
 */
function decryptInt($hash, $divider = "0")
{
    $divider = "0"; // must not be used in the character set (see encodeInt)
    $checksum_size = 4; // max. 4 for 32-bit integer
    if (strpos($hash, $divider) === false) return false;
    $parts = explode($divider, $hash);
    $check = (int) decodeInt($parts[0]);
    $num = (int) decodeInt($parts[1]);
    $hash = md5($num . ONYX_ENCRYPTION_SALT);
    $calculated = (int) hexdec(substr($hash, 0, $checksum_size));
    if ($check != $calculated) return false;
    return $num;
}

/**
 * Encode integer value using custom character set and append
 * salted MD5 checksum to allow verification.
 *
 * Please note, this function is not cryptographically safe.
 * The original integer value can be decoded very easily. The
 * purpose of the function is to make it harder to iterate
 * records indexed by the original integer values, but also
 * keep the encrypted value very small and within specific
 * character set.
 *
 * @param  int    $value   Integer value to be encoded
 * @return string Encoded integer
 */
function encryptInt($value)
{
    $divider = "0"; // must not be used in the character set (see encodeInt)
    $checksum_size = 4; // max. 4 for 32-bit integer
    $hash = md5($value . ONYX_ENCRYPTION_SALT);
    $check = (int) hexdec(substr($hash, 0, $checksum_size));
    return encodeInt($check) . $divider . encodeInt($value);
}

/**
 * convert arabic numbers to roman
 */
function convertNumeralArabicToRoman($number) {
    $n = intval($number);
    $lookup = [
        'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
        'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1
    ];

    $result = '';
    foreach ($lookup as $roman => $value)
    {
        $matches = intval($n / $value);
        $result .= str_repeat($roman, $matches);
        $n = $n % $value;
    }

    return $result;
}

/**
 * rangeDownload
 * source: https://mobiforge.com/design-development/content-delivery-mobile-devices
 */

function rangeDownload($file) {

    $fp = @fopen($file, 'rb');

    $size   = filesize($file); // File size
    $length = $size;           // Content length
    $start  = 0;               // Start byte
    $end    = $size - 1;       // End byte
    // Now that we've gotten so far without errors we send the accept range header
    /* At the moment we only support single ranges.
     * Multiple ranges requires some more work to ensure it works correctly
     * and comply with the spesifications: http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
     *
     * Multirange support annouces itself with:
     * header('Accept-Ranges: bytes');
     *
     * Multirange content must be sent with multipart/byteranges mediatype,
     * (mediatype = mimetype)
     * as well as a boundry header to indicate the various chunks of data.
     */
    header("Accept-Ranges: 0-$length");
    // header('Accept-Ranges: bytes');
    // multipart/byteranges
    // http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
    if (isset($_SERVER['HTTP_RANGE'])) {

        $c_start = $start;
        $c_end   = $end;
        // Extract the range string
        [, $range] = explode('=', $_SERVER['HTTP_RANGE'], 2);
        // Make sure the client hasn't sent us a multibyte range
        if (strpos($range, ',') !== false) {

            // (?) Shoud this be issued here, or should the first
            // range be used? Or should the header be ignored and
            // we output the whole content?
            header('HTTP/1.1 416 Requested Range Not Satisfiable');
            header("Content-Range: bytes $start-$end/$size");
            // (?) Echo some info to the client?
            exit;
        }
        // If the range starts with an '-' we start from the beginning
        // If not, we forward the file pointer
        // And make sure to get the end byte if spesified
        if ($range0 == '-') {

            // The n-number of the last bytes is requested
            $c_start = $size - substr($range, 1);
        }
        else {

            $range  = explode('-', $range);
            $c_start = $range[0];
            $c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
        }
        /* Check the range and make sure it's treated according to the specs.
         * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
         */
        // End bytes can not be larger than $end.
        $c_end = ($c_end > $end) ? $end : $c_end;
        // Validate the requested range and return an error if it's not correct.
        if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {

            header('HTTP/1.1 416 Requested Range Not Satisfiable');
            header("Content-Range: bytes $start-$end/$size");
            // (?) Echo some info to the client?
            exit;
        }
        $start  = $c_start;
        $end    = $c_end;
        $length = $end - $start + 1; // Calculate new content length
        fseek($fp, $start);
        header('HTTP/1.1 206 Partial Content');
    }
    // Notify the client the byte range we'll be outputting
    header("Content-Range: bytes $start-$end/$size");
    header("Content-Length: $length");

    // Start buffered download
    $buffer = 1024 * 8;
    while(!feof($fp) && ($p = ftell($fp)) <= $end) {

        if ($p + $buffer > $end) {

            // In case we're only outputtin a chunk, make sure we don't
            // read past the length
            $buffer = $end - $p + 1;
        }
        set_time_limit(0); // Reset time limit for big files
        echo fread($fp, $buffer);
        flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
    }

    fclose($fp);

}


/**
 * security check
 * it's allowed to see only content of var/ directory
 */

function onyxCheckForAllowedPath($realpath, $restrict_download = false) {

    $allowed_directories = array();
    $allowed_directories[] = ONYX_PROJECT_DIR;

    if (defined('ONYX_PROJECT_EXTERNAL_DIRECTORIES') && ONYX_PROJECT_EXTERNAL_DIRECTORIES != '') {
        $allowed_directories[] = ONYX_PROJECT_EXTERNAL_DIRECTORIES;
    }

    $check_status = array();

    foreach ($allowed_directories as $directory) {

        /**
         * $restrict_download will limit view or download option only to var/files/ directory
         * it needs to be disabled for viewing images as they are also stored in other directories
         * for example in thumbnails or vouchers
         */

        if ($restrict_download) {

        	if (class_exists('Onyx_Bo_Authentication') && Onyx_Bo_Authentication::getInstance()->isAuthenticated()) {

        	    // backoffice user can download any content from var/ directory
        		$check = addcslashes($directory, '/') . 'var\/';

        	} else {

        		// guest user can download only content of var/files
                $check = addcslashes($directory, '/') . 'var\/files\/';
        	}

        } else {

            // we can allow to see files from the whole var/, it's used for for images as there is restriction to see only image types
            $check = addcslashes($directory, '/') . 'var\/';

        }

        /**
         * make check
         */

        if (preg_match("/$check/", $realpath)) {

            $check_status[$directory] = true;

        } else {

            $check_status[$directory] = false;

        }
    }

    /**
     * allow if at least one check is passed
     */

    if (!in_array(true, $check_status)) {

        header("HTTP/1.0 403 Forbidden");
        echo "This path is forbidden!";
        exit;

    }
}

/**
 * Format a timestamp to display its age (5 days ago, in 3 days, etc.).
 * https://stackoverflow.com/questions/8629788/php-strtotime-reverse
 * @param   int     $timestamp
 * @param   int     $now
 * @return  string
 */
function timetostr($timestamp, $now = null) {
    $age = ($now ?: time()) - $timestamp;
    $future = ($age < 0);
    $age = abs($age);

    $age = (int)($age / 60);        // minutes ago
    if ($age == 0) return $future ? "momentarily" : "just now";

    $scales = [
        ["minute", "minutes", 60],
        ["hour", "hours", 24],
        ["day", "days", 7],
        ["week", "weeks", 4.348214286],     // average with leap year every 4 years
        ["month", "months", 12],
        ["year", "years", 10],
        ["decade", "decades", 10],
        ["century", "centuries", 1000],
        ["millenium", "millenia", PHP_INT_MAX]
    ];

    foreach ($scales as [$singular, $plural, $factor]) {
        if ($age == 0)
            return $future
                ? "in less than 1 $singular"
                : "less than 1 $singular ago";
        if ($age == 1)
            return $future
                ? "in 1 $singular"
                : "1 $singular ago";
        if ($age < $factor)
            return $future
                ? "in $age $plural"
                : "$age $plural ago";
        $age = (int)($age / $factor);
    }
}

/**
 * detects mimetype by extension - unreliable, but fast
 * @param   string     $filename
 * @return  string
 */

function mime_content_type_fast($filename) {

    $mime_types = array(

        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',

        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',

        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );

    $filename_parts = explode('.',$filename);
    $ext = strtolower(array_pop($filename_parts));

    if (array_key_exists($ext, $mime_types)) {

        return $mime_types[$ext];

    } else if (function_exists('mime_content_type')) {

        return mime_content_type($filename);

    } else {

        return 'application/octet-stream';

    }
}

/**
 * Verifies passed token against google recaptcha
 * @param $token
 */
function verifyReCaptchaToken($token) {
    try {
        $client = HttpClient::create();
        $response = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'body' => ['secret' => ONYX_RECAPTCHA_PRIVATE_KEY, 'response' => $token],
        ]);
        $response = $response->toArray();
        if ($response['success']) {
            if ($response['score'] < ONYX_RECAPTCHA_MIN_SCORE) return false;
            else return true;
        } else {
            return false;
        }
    } catch (Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface $e) {
        return false;
    }
}

 /**
 * onyxGlobalConfSetValue
 * @param $name constant name
 * @param $value
 */

function onyxGlobalConfSetValue($name, $value) {

    if (defined($name)) {
        return false; // already set
    }

    // check env variable
    if (strlen(getenv($name)) > 0) {
        $value = getenv($name);
        if (in_array(strtolower(($value)), ['true', 'false'])) {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
    }

    define($name, $value);
}

/**
 * Send HTMX-compatible response for node update operations
 * 
 * @param array|string $errors - Validation errors or error message
 * @param int $status_code - HTTP status code (default 400)
 * @param string $message - Main error message
 * @return void
 */
function  sendNodeUpdateResponse($errors, $status_code = 400, $message = 'Validation failed') {
    http_response_code($status_code);
    header('Content-Type: application/json');
    
    if (is_string($errors)) {
        $errors = ['general' => $errors];
    }

    switch($status_code) {
        case 200:
            $status = 'success';
            break;
        case 400:
        case 401:
        case 403:
        case 404:
        case 500:
            $status = 'error';
            break;
        default:
            $status = 'unknown';
            break;
    }
    
    $response = [
        'status' => $status,
        'message' => $message,
        'errors' => $errors,
        'timestamp' => date('c'),
        'status_code' => $status_code
    ];
    
    // HTMX trigger for error handling
    header('HX-Trigger: {"nodeUpdateResponse": ' . json_encode($response) . '}');
    
    // Return JSON error response
    echo json_encode($response);
    exit();
}
