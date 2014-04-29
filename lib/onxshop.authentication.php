<?php
/**
 * Copyright (c) 2005-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Authentication {

	var $id;
	var $gid;
	var $username;
	var $password;

	/**
	 * login
	 *
	 * @return unknown
	 */
	 
	function login() {
	
		$this->username = $_SERVER['PHP_AUTH_USER'];
		$this->password = $_SERVER['PHP_AUTH_PW'];
		$_SESSION['authentication']['authenticity'] = $this->_http_auth();
		$_SESSION['authentication']['username'] = $this->username;
		
		$_SESSION['authentication']['logon'] = $_SESSION['authentication']['authenticity']; // deprecated, remove in Onxshop 1.8
		
		return $_SESSION['authentication']['authenticity'];
	}
	
	/**
	 * logout
	 *
	 * @return unknown
	 */
	 
	function logout() {
	
		if ($this->_logout()) {
		
			$_SESSION['authentication']['authenticity'] = 0;
			$_SESSION['authentication']['logon'] = 0; // deprecated, remove in Onxshop 1.8
			msg('Logout completed');
			
			return true;
			
		} else {
		
			return false;
		
		}
	}
	
	/**
	 * get user detail
	 *
	 * @param unknown_type $id
	 * @return unknown
	 */
	 
	function getUserDetail($id) {
	
		if (!is_numeric($id)) return false;

		if ($id == 1) {
			$detail['id'] = $id;
			$detail['username'] = 'editor';
			$detail['name'] = 'Editor';
		} else {
			/*$csv = local_exec('cat /etc/passwd');
			$passwd = CSV2Array($csv, ':', '');
			foreach ($passwd as $line) {
				$d['username'] = $line[0];
				$d['id'] = $line[2];
				$d['name'] = rtrim($line[4], ',');
				if ($d['id'] === $id) $detail = $d;
			}*/
			$detail['id'] = 1000;
			$detail['username'] = 'editor';
			$detail['name'] = 'Editor';
		}

		return $detail;
	}
	
	/**
	 * login
	 *
	 * @return unknown
	 */
	 
	function _login() {

		if (!$this->_checkAccess($this->username)) return false;

		switch (ONXSHOP_AUTH_TYPE)  {
			case 'imap':
				$auth = $this->authIMAP($this->username, $this->password, "{".ONXSHOP_AUTH_SERVER.":143/imap/notls}");
			break;
			case 'postgresql':
				$auth = $this->authPg($this->username, $this->password, ONXSHOP_AUTH_SERVER);
			break;
			case 'onlyeditor':
				$auth = $this->authFlat($this->username, $this->password);
			break;
		}
		
		if ($auth) {
		
			msg('Username and Password OK.', 'ok', 2);
			$id = local_exec("id " . escapeshellarg($this->username));
			$id = intval($id);
		
			if ($id == 0) {
				msg("numeric id for user {$this->username} was not found, using 1000", 'error', 1);
				$id = 1000;
			}
		
		} else {
			$id = false;
		}
		
		
		if ($id) {
		
			$this->id = $id;
			return true;
		
		} else {
		
			msg(_('Wrong username/password.'), 'error', 1);
			return false;
		
		}
	}

	/**
	 * Check if the user has acccess to this project
	 */

	function _checkAccess($username) {
	
		$coreuser = preg_replace("/^([^-]*)-.*$/", "\\1", $username);
		if ($username === ONXSHOP_DB_USER || $coreuser === ONXSHOP_DB_USER) {
			return true;
		} else {
			msg("You have no permissions to edit this project", 'error');
			return false;
		}
	}

	/**
	 * IMAP method
	 */
	 
	function authIMAP($user, $pass, $host = 'localhost') {
	
		@$mailbox = imap_open($host, $user, $pass, OP_DEBUG);
		if ($mailbox) {
			imap_close($mailbox);
			return true;
		} else {
			return false;
		}

	}

	/**
	 * PostgreSQL method
	 */
	 
	function authPg($user, $pass, $host = 'localhost', $port = 5432) {
	
		$conn_string = "host=$host port=$port dbname=" . ONXSHOP_DB_NAME . " user=$user password=$pass";
		@$dbconn = pg_connect($conn_string);

		if ($dbconn) {
			pg_close($dbconn);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * flat method
	 */
	 
	function authFlat($user, $pass) {

		if (defined('ONXSHOP_EDITOR_USERNAME') && $user == constant('ONXSHOP_EDITOR_USERNAME') && $pass == constant('ONXSHOP_EDITOR_PASSWORD')) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * http auth
	 *
	 * @return unknown
	 */
	 
	function _http_auth() {
	
		if ($_SESSION['authentication']['isin'] == 0 || !$_SERVER['PHP_AUTH_USER']) {
			
			Header( "WWW-authenticate: Basic realm=\"CMS\"");
			Header( "HTTP/1.0 401 Unauthorized");
			$_SESSION['authentication']['isin'] = 1;
			exit;
		
		} else {
		
			if (!$this->_login()) {
				
				$_SESSION['authentication']['isin'] = 0;
				
				return 0;
				
			} else {
				
				$_SESSION['authentication']['isin'] = 1;
				
				return $this->id;
			}
		}
	}


	/**
	 * logout
	 *
	 */
	 
	function _logout() {
	
		$_SERVER['PHP_AUTH_USER'] = null;
		
		$_SESSION['authentication']['isin'] = 0;
		
		// delete the session cookie.
		if (ini_get("session.use_cookies")) {
		    $params = session_get_cookie_params();
		    setcookie(session_name(), '', time() - 42000,
		        $params["path"], $params["domain"],
		        $params["secure"], $params["httponly"]
		    );
		}
				
		return true;
	}

}
