<?php
/**
 * Copyright (c) 2005-2022 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('models/client/client_customer.php');
require_once('models/client/client_role_permission.php');

use Symfony\Component\HttpFoundation\IpUtils;

class Onyx_Bo_Authentication
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
        switch (ONYX_AUTH_TYPE)  {

            case 'imap':

                $this->superuserAuthAdapter = new IMAPAuthAdapter();
                break;

            case 'postgresql':

                $this->superuserAuthAdapter = new PSQLAuthAdapter();
                break;
            
            case 'mysql':

                $this->superuserAuthAdapter = new MYSQLAuthAdapter();
                break;

            default:

                $this->superuserAuthAdapter = new FlatAuthAdapter();
        }

        // administrators are authenticated using client_customer_acl
        $this->adminAuthAdapter = new ClientAuthAdapter();

        // create instance of client_role_permission
        self::$Permission = new client_role_permission();
        self::$Permission->setCacheable(false);

    }



    /**
     * Static instance accessor
     * 
     * @return Onyx_Bo_Authentication Class instance
     */
    public static function getInstance()
    {
        if (self::$instance === false) self::$instance = new Onyx_Bo_Authentication();
        return self::$instance;
    }

    /**
     * checkIpWhitelist
     */
    public function checkIpWhitelist() {

        if (!defined('ONYX_AUTH_CIDR_WHITELIST') || ONYX_AUTH_CIDR_WHITELIST === false) return true; //whitelist not set, allow access to everyone
        
        $whitelist = explode(',', ONYX_AUTH_CIDR_WHITELIST);
        $http_client_ip = $_SERVER["REMOTE_ADDR"];

        foreach($whitelist as $cidr) {
            if (IpUtils::checkIp($http_client_ip, $cidr)) {
                return true;
            }
        }

        return false;
        
    }

    /**
     * Login if HTTP Auth credentials present.
     * Show HTTP Auth login dialog otherwise.
     *
     * @return  boolean True on success or if logged in already
     */
    public function login()
    {

        if (!$this->checkIpWhitelist()) {
            http_response_code(401);
            $msg = "Access to backoffice denied. Recorded IP address is {$_SERVER["REMOTE_ADDR"]}";
            error_log($msg);
            die($msg);
        }
        
        if ($this->isAuthenticated()) return true;

        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        if (!$username) $this->showHttpAuthDialog();

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
        self::$superuserEmulation = false;
        msg('Logout completed');
    }



    /**
     * Is user authenticated within current session?
     * 
     * @return boolean 
     */
    public function isAuthenticated()
    {
        return (bool) $_SESSION['authentication']['authenticated'] || self::$superuserEmulation;
    }



    /**
     * Is authenticated user a superuser?
     * 
     * @return boolean 
     */
    public function isSuperuser()
    {
        return (bool) $_SESSION['authentication']['superuser'] || self::$superuserEmulation;
    }



    /**
     * Is current instalation of ecommerce type?
     */
    public function isEcommerce()
    {
        return ONYX_ECOMMERCE;
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
     * Get user ID
     * 
     * @return int User ID
     */
    public function getUserId()
    {
        if ($this->isAuthenticated()) {

            $user_details = $this->getUserDetails();
            
            return $user_details['id'];

        }

        return false;
    }



    /**
     * Has currently authenticated user permission to perform
     * a given operation on a given resource?
     * 
     * @return boolean 
     */
    public function hasPermission($resource, $operation)
    {
        if (self::$superuserEmulation) return true;
        if (!$this->isAuthenticated()) return false;
        if ($this->isSuperuser()) return true;

        $customer_id = $_SESSION['authentication']['user_details']['id'];
        return self::$Permission->checkPermissionByCustomer($customer_id, $resource, $operation);
    }



    /**
     * Has currently authenticated user permission to perform
     * at least one single operation on a given resource?
     * 
     * @return boolean 
     */
    public function hasAnyPermission($resource)
    {
        return $this->hasPermission($resource, "_any_");
    }



    /**
     * Emulate superuser temporarily (just for the current script run)
     */
    public function emulateSuperuserTemporarily()
    {
        self::$superuserEmulation = true;
    }



    /**
     * Disable superuser emulation
     */
    public function disableSuperuserEmulation()
    {
        self::$superuserEmulation = false;
    }



    /**
     * Show HTTP Auth dialog (exists the script)
     */
    private function showHttpAuthDialog()
    {
        
        /**
         * Option 1: show OS/browser native dialog window
         */
         
        //Header("WWW-authenticate: Basic realm=\"CMS\"");
        //Header("HTTP/1.0 401 Unauthorized");
        
        /**
         * Option 2: show custom dialog window
         */
         
        $result = new Onyx_Router('bo_login');
        echo $result->Onyx->getContent();
    
        /**
         * no more script processing
         */
         
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
        $host = "{" . ONYX_AUTH_SERVER . ":143/imap/notls}";
        @$mailbox = imap_open($host, $username, $password, OP_DEBUG);

        if ($mailbox) {

            imap_close($mailbox);

            return array(
                'id' => 0,
                'username' => $username,
                'email' => $username . '@' . ONYX_AUTH_SERVER
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
            ONYX_AUTH_SERVER,
            ONYX_DB_PORT,
            ONYX_DB_NAME,
            $username,
            $password
        );

        @$dbconn = pg_connect($conn_string);

        if ($dbconn) {

            pg_close($dbconn);

            return array(
                'id' => 0,
                'username' => $username,
                'email' => $username . '@' . ONYX_AUTH_SERVER
            );

        }

        return false;

    }

}


/**
 * MySQL Authenication Adapter
 */
class MYSQLAuthAdapter implements AuthAdapter
{

    public function authenticate($username, $password)
    {

        @$dbconn = mysql_connect(ONYX_AUTH_SERVER . ':' . ONYX_DB_PORT, $username, $password);

        if ($dbconn) {

            mysql_close($dbconn);

            return array(
                'id' => 0,
                'username' => $username,
                'email' => $username . '@' . ONYX_AUTH_SERVER
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
        if (
            defined('ONYX_EDITOR_USERNAME') && 
            $username == constant('ONYX_EDITOR_USERNAME') && 
            $password == constant('ONYX_EDITOR_PASSWORD')
        ) {
            
            return array(
                'id' => 0,
                'username' => $username,
                'email' => $username . '@flat'
            );
            
        } 

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
