<?php
/**
 * Copyright (c) 2005-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('models/client/client_customer.php');
require_once('models/client/client_role_permission.php');
require_once(ONXSHOP_DIR . 'conf/permissions.php');

class Onxshop_Bo_Authentication
{

	/**
	 * Class instance
	 */
	private static $instance = false;



	/**
	 * client_role_permission model instance
	 */
	private static $Permission;



	/**
	 * Superuser emulation flag
	 */
	private static $superuserEmulation = false;



	/**
	 * Superuser Authentication Adapter
	 */
	private $superuserAuthAdapter;



	/**
	 * Admin Authentication Adapter
	 */
	private $adminAuthAdapter;



	/**
	 * Private constructor to ensure class won't be instantiated.
	 */
	private function __construct()
	{

		// instantiate superuser AuthAdapter as per configuration settings
		switch (ONXSHOP_AUTH_TYPE)  {

			case 'imap':

				$this->superuserAuthAdapter = new IMAPAuthAdapter();
				break;

			case 'postgresql':

				$this->superuserAuthAdapter = new PSQLAuthAdapter();
				break;

			default:

				$this->superuserAuthAdapter = new FlatAuthAdapter();
		}

		// administrators are authenticated using client_customer_acl
		$this->adminAuthAdapter = new ClientAuthAdapter();

		// create instance of client_role_permission
		$this->Permission = new client_role_permission();
		$this->Permission->setCacheable(false);

	}



	/**
	 * Static instance accessor
	 * 
	 * @return Onxshop_Bo_Authentication Class instance
	 */
	public static function getInstance()
	{
		if (self::$instance === false) self::$instance = new Onxshop_Bo_Authentication();
		return self::$instance;
	}



	/**
	 * Login if HTTP Auth credentials present.
	 * Show HTTP Auth login dialog otherwise.
	 *
	 * @return  boolean True on success or if logged in already
	 */
	public function login()
	{
		if ($this->isAuthenticated()) return true;

		$username = $_SERVER['PHP_AUTH_USER'];
		$password = $_SERVER['PHP_AUTH_PW'];

		if (!$_SESSION['authentication']['http_auth_requested'] || !$username) $this->showHttpAuthDialog();

		// reset all
		$_SESSION['authentication'] = null;

		if ($username && $password) {

			if (filter_var($username, FILTER_VALIDATE_EMAIL)) {

				$user = $this->adminAuthAdapter->authenticate($username, $password);
				$superuser = false;

			} else {

				$user = $this->superuserAuthAdapter->authenticate($username, $password);
				$superuser = true;

			}

			if ($user) {

				$_SESSION['authentication']['user_details'] = $user;
				$_SESSION['authentication']['authenticated'] = 1;
				$_SESSION['authentication']['superuser'] = $superuser;

				// backwards compatibility
				$_SESSION['authentication']['authenticity'] = 1;
				$_SESSION['authentication']['logon'] = 1;

				return true;
			}

		}

		return false;
	}



	/**
	 * Logout from back office
	 */
	public function logout()
	{
		$_SESSION['authentication'] = null;
		msg('Logout completed');
	}



	/**
	 * Is user authenticated within current session?
	 * 
	 * @return boolean 
	 */
	public function isAuthenticated()
	{
		return (bool) $_SESSION['authentication']['authenticated'];
	}



	/**
	 * Is authenticated user a superuser?
	 * 
	 * @return boolean 
	 */
	public function isSuperuser()
	{
		return (bool) $_SESSION['authentication']['superuser'];
	}



	/**
	 * Is current instalation of ecommerce type?
	 */
	public function isEcommerce()
	{
		return (ONXSHOP_PACKAGE_NAME == 'standard' || ONXSHOP_PACKAGE_NAME == 'premium');
	}



	/**
	 * Get logged user details
	 * 
	 * @return Array User details
	 */
	public function getUserDetails()
	{
		if ($this->isAuthenticated()) {

			return $_SESSION['authentication']['user_details'];

		}

		return false;
	}



	/**
	 * Has currently authenticated given permission? 
	 * 
	 * @return boolean 
	 */
	public function hasPermission($permission, $scope = null)
	{
		if (self::$superuserEmulation) return true;
		if (!$this->isAuthenticated()) return false;
		if ($this->isSuperuser()) return true;

		$customer_id = $_SESSION['authentication']['user_details']['id'];
		return $this->Permission->checkPermissionByCustomer($customer_id, $permission, $scope);
	}



	/**
	 * Emulate superuser temporarily (just for the current script run)
	 */
	public function emulateSuperuserTemporarily()
	{
		self::$superuserEmulation = true;
	}



	/**
	 * Show HTTP Auth dialog (exists the script)
	 */
	private function showHttpAuthDialog()
	{
		$_SESSION['authentication'] = array('http_auth_requested' => 1);

		Header("WWW-authenticate: Basic realm=\"CMS\"");
		Header("HTTP/1.0 401 Unauthorized");
		exit;
	}

}



/**
 * Authentication Adapter Interface
 */
interface AuthAdapter
{

	/**
	 * Authenicate function should return user details (array)
	 * on success and false on failure
	 * 
	 * @param  String $username Username
	 * @param  String $password Password
	 * @return boolean|Array
	 */
	public function authenticate($username, $password);

}



/**
 * IMAP Authenication Adapter
 */
class IMAPAuthAdapter implements AuthAdapter
{

	public function authenticate($username, $password)
	{
		$host = "{" . ONXSHOP_AUTH_SERVER . ":143/imap/notls}";
		@$mailbox = imap_open($host, $username, $password, OP_DEBUG);

		if ($mailbox) {

			imap_close($mailbox);

			return array(
				'username' => $username
			);

		}
		
		return false;

	}

}

/**
 * PostgreSQL Authenication Adapter
 */
class PSQLAuthAdapter implements AuthAdapter
{

	public function authenticate($username, $password)
	{
		$conn_string = sprintf("host=%s port=%d dbname=%s user=%s password=%s", 
			ONXSHOP_AUTH_SERVER,
			ONXSHOP_DB_PORT,
			ONXSHOP_DB_NAME,
			$username,
			$password
		);

		@$dbconn = pg_connect($conn_string);

		if ($dbconn) {

			pg_close($dbconn);

			return array(
				'username' => $username
			);

		}

		return false;

	}

}



/**
 * Flat Authenication Adapter
 */
class FlatAuthAdapter implements AuthAdapter
{

	public function authenticate($username, $password)
	{
		return (
			defined('ONXSHOP_EDITOR_USERNAME') && 
			$username == constant('ONXSHOP_EDITOR_USERNAME') && 
			$password == constant('ONXSHOP_EDITOR_PASSWORD')
		); 

	}

}



/**
 * Client Authenication Adapter
 */
class ClientAuthAdapter implements AuthAdapter
{

	public function authenticate($username, $password)
	{
		$Client_Customer = new client_customer();
		$Client_Customer->setCacheable(false);
		$customer_detail = $Client_Customer->login($username, md5($password));

		if ($customer_detail) {

			$Permission = new client_role_permission();
			$Permission->setCacheable(false);

			if ($Permission->isBackofficeUser($customer_detail['id'])) {
				return $customer_detail;
			}
		}

		return false;
	}

}
