<?php
/**
 * class client_customer
 * 
 * Copyright (c) 2009-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class client_customer extends Onyx_Model {

    /**
     * primary key
     * @access private
     */
    var $id;
    /**
     * @access private
     */
    var $title_before;
    /**
     * @access private
     */
    var $first_name;
    /**
     * @access private
     */
    var $last_name;
    /**
     * @access private
     */
    var $title_after;
    /**
     * @access private
     */
    var $email;
    
    var $username;
    
    /**
     * @access private
     */
    var $telephone;
    /**
     * @access private
     */
    var $mobilephone;
    /**
     * @access private
     */
    var $nickname;
    /**
     * @access private
     */
    var $password;
    /**
     * current company
     * @access private
     */
    var $company_id;
    /**
     * REFERENCES address(id) ON UPDATE CASCADE ON DELETE CASCADE
     * @access private
     */
    var $invoices_address_id;
    /**
     * REFERENCES address(id) ON UPDATE CASCADE ON DELETE CASCADE
     * @access private
     */
    var $delivery_address_id;
    /**
     * m - male
     * f - female
     * @access private
     */
    var $gender;
    /**
     * @access private
     */
    var $created;
    
    var $currency_code;
    
    /**
     * 0 - disabled, i.e. temporarily locked (can't register)
     * 1 - registered
     * 2 - reserved
     * 3 - preserved for special purposes such as newsletter and survey (doesn't have any authentication method and can register by updating the same account details)
     * 4 - deleted (can register again)
     * 5 - guest checkout (can register again)
     */
    
    var $status;
    
    var $newsletter;
    
    var $birthday;

    var $other_data;
    
    var $modified;
    
    /**
     * 0 - standard (can be set by user)
     * 1 - request for trade (can be set by user)
     * 2 - approved trade (only backend user can change to this status)
     */
     
    var $account_type;
    
    var $agreed_with_latest_t_and_c;
    
    var $verified_email_address;
        
    /**
     * serialised oauth data
     */
     
    var $oauth;
    
    /**
     * use email + deleted_date unique constraint, this will prevent double active accounts
     * TODO: implement on user delete action
     */
     
    var $deleted_date;
    
    var $facebook_id;
    
    var $twitter_id;
    
    var $google_id;
    
    var $profile_image_url;
    
    var $store_id;
    
    var $janrain_id;
        
    var $_metaData = array(
        'id'=>array('label' => 'ID', 'validation'=>'int', 'required'=>true), 
        'title_before'=>array('label' => 'Title', 'validation'=>'string', 'required'=>false),
        'first_name'=>array('label' => 'First name', 'validation'=>'string', 'required'=>false),
        'last_name'=>array('label' => 'Last name', 'validation'=>'string', 'required'=>false),
        'title_after'=>array('label' => 'Title (after)', 'validation'=>'string', 'required'=>false),
        'email'=>array('label' => 'Email', 'validation'=>'email', 'required'=>true),
        'username'=>array('label' => 'Username', 'validation'=>'string', 'required'=>false),
        'telephone'=>array('label' => 'Phone number', 'validation'=>'string', 'required'=>false),
        'mobilephone'=>array('label' => 'Mobile number', 'validation'=>'string', 'required'=>false),
        'nickname'=>array('label' => 'Username', 'validation'=>'string', 'required'=>false),
        'password'=>array('label' => 'Password', 'validation'=>'string', 'required'=>false),
        'company_id'=>array('label' => 'Company', 'validation'=>'int', 'required'=>false),
        'invoices_address_id'=>array('label' => 'Invoice address', 'validation'=>'int', 'required'=>false),
        'delivery_address_id'=>array('label' => 'Delivery address', 'validation'=>'int', 'required'=>false),
        'gender'=>array('label' => 'Gender', 'validation'=>'string', 'required'=>false),
        'created'=>array('label' => 'Date created', 'validation'=>'datetime', 'required'=>true),
        'currency_code'=>array('label' => 'Preferred currency', 'validation'=>'string', 'required'=>false),
        'status'=>array('label' => 'Status', 'validation'=>'int', 'required'=>false),
        'newsletter'=>array('label' => 'Subscribe to newsletter', 'validation'=>'int', 'required'=>false),
        'birthday'=>array('label' => 'Birthday', 'validation'=>'date', 'required'=>false),
        'other_data'=>array('label' => 'Other', 'validation'=>'serialized', 'required'=>false),
        'modified'=>array('label' => 'Date modified', 'validation'=>'datetime', 'required'=>true),
        'account_type'=>array('label' => 'Account Type', 'validation'=>'int', 'required'=>false),
        'agreed_with_latest_t_and_c'=>array('label' => 'Agreed with t-and-c', 'validation'=>'int', 'required'=>false),
        'verified_email_address'=>array('label' => 'Verified Email Address', 'validation'=>'int', 'required'=>false),
        'oauth'=>array('label' => 'Oauth storege for tokens', 'validation'=>'serialized', 'required'=>false),
        'deleted_date'=>array('label' => 'Deleted date', 'validation'=>'datetime', 'required'=>false),
        'facebook_id'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'twitter_id'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'google_id'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'profile_image_url'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'store_id'=>array('label' => 'Home Store ID', 'validation'=>'int', 'required'=>false),
        'janrain_id'=>array('label' => 'Janrain UUID', 'validation'=>'string', 'required'=>false)
    );
    
    /**
     * create table sql
     * 
     * @return string
     * SQL command for table creating
     */
     
    private function getCreateTableSql() {
    
        $sql = "
CREATE TABLE client_customer (
    id serial NOT NULL PRIMARY KEY,
    title_before character varying(255),
    first_name character varying(255),
    last_name character varying(255),
    title_after character varying(255),
    email character varying(255),
    \"username\" character varying(255),
    telephone character varying(255),
    mobilephone character varying(255),
    nickname character varying(255),
    \"password\" character varying(255),
    company_id integer,
    invoices_address_id integer,
    delivery_address_id integer,
    gender character(1),
    created timestamp(0) without time zone,
    currency_code character(3),
    status smallint,
    newsletter smallint,
    birthday date,
    other_data text,
    modified timestamp(0) without time zone,
    account_type smallint NOT NULL DEFAULT 0,
    agreed_with_latest_t_and_c smallint NOT NULL DEFAULT 0,
    verified_email_address smallint NOT NULL DEFAULT 0,
    oauth text,
    deleted_date timestamp without time zone,
    facebook_id character varying(255),
    twitter_id character varying(255),
    google_id character varying(255),
    profile_image_url text,
    store_id integer REFERENCES ecommerce_store ON UPDATE CASCADE ON DELETE RESTRICT,
    janrain_id character varying(255)
);
ALTER TABLE ONLY client_customer ADD CONSTRAINT client_customer_email_key UNIQUE (email, deleted_date);
        ";
        
        return $sql;
    }
    
    /**
     * init configuration
     * 
     * @return array
     * customer configuration
     */
     
    static function initConfiguration() {
    
        if (array_key_exists('client_customer', $GLOBALS['onyx_conf'])) $conf = $GLOBALS['onyx_conf']['client_customer'];
        else $conf = array();
        
        /**
         * set default values if empty
         */
        if ($conf['registration_mail_to_address'] == '') $conf['registration_mail_to_address'] = $GLOBALS['onyx_conf']['global']['admin_email'];
        if ($conf['registration_mail_to_name'] == '') $conf['registration_mail_to_name'] = $GLOBALS['onyx_conf']['global']['admin_email_name'];
        if ($conf['registration_mail_send_to_customer'] == '') $conf['registration_mail_send_to_customer'] = 1;
        //what is the username for authentication? Can be email or username
        if (!($conf['login_type'] == 'email' || $conf['login_type'] == 'username')) $conf['login_type'] = 'email';
        //default avatar
        if (!$conf['default_profile_image_url']) $conf['default_profile_image_url'] = '/share/images/default/avatar.png';
        //facebook conf
        if (!$conf['facebook_login_scope']) $conf['facebook_login_scope'] = 'email';
        
        return $conf;
    }
    
    /**
     * get detail
     * 
     * @param integer $id
     * customer ID
     * 
     * @return array
     * SQL row with customer informations
     */
    
    function getDetail($id) {
    
        if (!is_numeric($id)) {
            msg('client_customer->getDetail: Id is not numeric', 'error');
            return false;
        }
        
        if ($data = $this->detail($id)) {

            $data['other_data'] = unserialize($data['other_data']);

            // load groups
            require_once('models/client/client_customer_group.php');
            $CustomerGroup = new client_customer_group();

            $data['group_ids'] = $CustomerGroup->getCustomersGroupIds($id);
            $data['group_id'] = (int) $data['group_ids'][0]; // backwards compatibility

            // load roles
            require_once('models/client/client_customer_role.php');
            $CustomerRole = new client_customer_role();

            $data['role_ids'] = $CustomerRole->getCustomersRoleIds($id);
        
            if (empty($data['profile_image_url'])) $data['profile_image_url'] = $this->conf['default_profile_image_url']; 
        
        }
        
        return $data;
    }
    
    /**
     * get client data
     * 
     * @param integer $id
     * client ID
     * 
     * @return array
     * customer informations
     */
     
    function getClientData($id = 0) {
    
        if (!is_numeric($id)) {
            msg('client_customer->getClientData: Id is not numeric', 'error');
            return false;
        }
        
        // basic info
        $client['customer'] = $this->getDetail($id);
        
        // address details
        require_once('models/client/client_address.php');
        $Address = new client_address();
        if (is_numeric($client['customer']['delivery_address_id']) && $client['customer']['delivery_address_id'] > 0) $client['address']['delivery'] = $Address->getDetail($client['customer']['delivery_address_id']);
        if (is_numeric($client['customer']['invoices_address_id']) && $client['customer']['invoices_address_id'] > 0) $client['address']['invoices'] = $Address->getDetail($client['customer']['invoices_address_id']);
        
        // company details
        if ($client['customer']['company_id'] > 0) {
            require_once('models/client/client_company.php');
            $Company = new client_company();
            $client['company'] = $Company->detail($client['customer']['company_id']);
        }
        
        return $client;
    }
    
    /**
     * check register and prepare data
     * 
     * @param array $customer_data
     * information array for update
     * 
     * @return array
     * completed input $customer_data or false if not valid
     */
     
    function prepareToRegister($customer_data) {
        
        if (!is_array($customer_data)) return false;
        
        //make email and username lowercase to avoid duplications
        $customer_data['email'] = strtolower($customer_data['email']);
        $customer_data['username'] = strtolower($customer_data['username']);
        //set default values
        $customer_data['company_id'] = 0;
        $customer_data['invoices_address_id'] = 0;
        $customer_data['delivery_address_id'] = 0;
        $customer_data['created'] = date('c');
        $customer_data['modified'] = date('c');
        if (empty(trim($customer_data['password']))) {
            $customer_data['status'] = 5; // guest account
        } else {
            $customer_data['status'] = 1; // standard account
        }
        $customer_data['other_data'] = serialize($customer_data['other_data']);
        if (!is_numeric($customer_data['account_type'])) $customer_data['account_type'] = 0;
        $customer_data['agreed_with_latest_t_and_c'] = 1;
        $customer_data['verified_email_address'] = 0;
        if (!is_numeric($customer_data['newsletter'])) $customer_data['newsletter'] = 0;
        if (trim($customer_data['password'])) $customer_data['password'] = md5($customer_data['password']);
        else unset($customer_data['password']);
        if (!is_numeric($customer_data['facebook_id'])) unset($customer_data['facebook_id']);
        if (!is_numeric($customer_data['twitter_id'])) unset($customer_data['twitter_id']);
        if (!is_numeric($customer_data['google_id'])) unset($customer_data['google_id']);
        if (trim($customer_data['profile_image_url']) == '') unset($customer_data['profile_image_url']);
        
        $this->setAll($customer_data);
        
        /**
         * make sure user cannot request account type greater then 1
         */
         
        if ($customer_data['account_type'] > 1) {
            msg("Account Type cannot be greater then 1");
            return false;
        }
    
        /**
         * check against existing accounts
         */
         
        if ($this->isSocialAccount($customer_data)) {
            
            //allow to pass, but issue notice
            if (!$this->checkLoginId($customer_data)) {     
            
                msg("Social registration to an existing account {$customer_data['email']}", 'ok', 1);
                    
            }
                        
        } else {
            
            //full check
            if (!$this->checkLoginId($customer_data)) {
                    
                msg(str_replace('%s', $customer_data['email'], I18N_CLIENT_CUSTOMER_EMAIL_EXISTS), 'error', 0, 'account_exists');   
                return false;
            
            }
        }
        
        if ($this->getValid()) {
            return $customer_data;
        } else {
            return false;
        }
    }
    
    /**
     * check if login id is available for new registration
     * 
     * @param array $customer_data
     * informations with items 'email' and 'username' for test existence
     * 
     * @return boolean
     * is this data available for registration? [true/false]
     */
    
    function checkLoginId($customer_data) {
    
        //make email and username lowercase to avoid duplications
        $customer_data['email'] = strtolower($customer_data['email']);
        $customer_data['username'] = strtolower($customer_data['username']);
        
        if ($this->conf['login_type'] == 'email') {
        
            if ($this->set('email', $customer_data['email'])) {
        
                $email_quoted = $this->db->quote($customer_data['email']);
                $sql = "lower(email) = $email_quoted AND status < 3";
                if ($customer_data['id'] > 0) $sql .= " AND id != {$customer_data['id']}";
                $customer_current = $this->listing($sql);
        
                if (count($customer_current) > 0) {
                    
                    return false;
                    
                } else {
        
                    return true;
        
                }
            } else {
        
                msg("Cannot set email {$customer_data['email']}", 'error');
                return false;
        
            }
        
        } else {
        
            if ($this->set('email', $customer_data['email']) && $this->set('username', $customer_data['username'])) {
        
                $email_quoted = $this->db->quote($customer_data['email']);
                $username_quoted = $this->db->quote($customer_data['username']);
                $customer_current = $this->listing("lower(email) = $email_quoted OR username = $username_quoted AND status < 3");
        
                if (count($customer_current) > 0) {
            
                    msg("User {$customer_data['email']} or {$customer_data['username']} is already registered", 'error');
                    return false;
            
                } else {
            
                    return true;
            
                }
        
            } else {
        
                return false;
        
            }
        }
    }
    
    /**
     * isSocialAccount
     */
     
    public function isSocialAccount($customer_data) {
    
        if (is_numeric($customer_data['facebook_id']) || is_numeric($customer_data['twitter_id']) || is_numeric($customer_data['google_id'])) return true;
        else return false;
        
    }

    /**
     * isGuestAccount
     */
     
    public function isGuestAccount($customer_data) {
    
        if ($customer_data['status'] == 5) return true;
        else return false;
        
    }
    
    /**
     * getUserByFacebookId
     */
     
    public function getUserByFacebookId($facebook_id) {
        
        if (!is_numeric($facebook_id)) return false;
        
        $sql = "facebook_id = $facebook_id AND status < 3";
        
        $customer_current = $this->listing($sql);
        
        if (count($customer_current) > 0) {
            
            return $this->getDetail($customer_current[0]['id']);
            
        }
    }
    
    /**
     * getUserByTwitterId
     */
     
    public function getUserByTwitterId($twitter_id) {
        
        if (!is_numeric($twitter_id)) return false;
        
        $sql = "twitter_id = $twitter_id AND status < 3";
        
        $customer_current = $this->listing($sql);
        
        if (count($customer_current) > 0) {
            
            return $this->getDetail($customer_current[0]['id']);
            
        }
    }
    
    /**
     * getUserByGoogleId
     */
     
    public function getUserByGoogleId($google_id) {
        
        if (!is_numeric($google_id)) return false;
        
        $sql = "google_id = $google_id AND status < 3";
        
        $customer_current = $this->listing($sql);
        
        if (count($customer_current) > 0) {
            
            return $this->getDetail($customer_current[0]['id']);
            
        }
    }
    
    /**
     * check if is registered for newsletter only
     * 
     * @param string $email
     * 
     * @return boolean
     * is this email address registered for newsleter? [true/false]
     */
    
    function checkLoginIdPreservedOnly($email) {
    
        $email = strtolower($email);
        $email_quoted = $this->db->quote($email);
        
        $customer_list = $this->listing("lower(email) = $email_quoted AND status = 3", 'id DESC');

        if (count($customer_list) > 0) {
            
            if (count($customer_list) == 1) {
            
                return $this->getDetail($customer_list[0]['id']);
            
            } else {
            
                //this shouldn't really happen in any circumstances
                msg("Multiple preserved registrations on email {$email}, using first found", 'error');
                return $this->getDetail($customer_list[0]['id']);
            
            }
            
        } else {
            return false;
        }
    }
    
    /**
     * insert a new customer with a check whether the same customer isn't already subscribed to the newsletter
     * and merge data in the old newsletter account in that case
     * 
     * @param array $data
     * customer data for registration or update
     * 
     * @return integer
     * customer ID or false if not saved
     */
     
    public function insertCustomer($data) {
    
        //make email and username lowercase to avoid duplications
        if (array_key_exists('email', $data)) $data['email'] = strtolower($data['email']);
        if (array_key_exists('username', $data)) $data['username'] = strtolower($data['username']);
        if (is_array($data['other_data'])) $data['other_data'] = serialize($data['other_data']);
        
        if ($preserved_account = $this->checkLoginIdPreservedOnly($data['email'])) {
            
            $id = $this->mergeAccount($preserved_account, $data);
            
        } else if ($old_account = $this->getClientByEmail($data['email'])) {
            //this can happen when associating social account to previously (fully) registered account
            $id = $this->mergeAccount($old_account, $data);
            
        } else {
            $data['created'] = date('c');
            $data['modified'] = date('c');
            $id = $this->insert($data);
        }
        
        if (is_numeric($id)) return $id;
        else return false;
    
    }
    
    /**
     * mergeAccount
     */
     
    public function mergeAccount($old, $new) {
    
        if (!is_array($old) || !is_array($new)) return false;
        
        //merge data, but keep old created time
        $merged_data = array_merge($old, $new);
        
        // remove extra attributes
        unset($merged_data['group_ids']);
        unset($merged_data['group_id']);
        unset($merged_data['role_ids']);
        
        $merged_data['created'] = $old['created'];
        $merged_data['modified'] = date('c');
        $id = $this->update($merged_data);
        
        return $id;
    }
    
    /**
     * mergePreservedAccount
     */
     
    public function mergePreservedAccount($old, $new) {
    
        if (!is_array($old) || !is_array($new)) return false;
        
        $merged_data = $old;
        
        // remove extra attributes
        unset($merged_data['group_ids']);
        unset($merged_data['group_id']);
        unset($merged_data['role_ids']);
        
        // merge only certain properties

        if (strlen($new['other_data']['city']) > 0) 
            $merged_data['other_data']['city'] = $new['other_data']['city'];

        if (strlen($new['other_data']['county']) > 0) 
            $merged_data['other_data']['county'] = $new['other_data']['county'];
            
        if (is_numeric($new['store_id']) > 0) 
            $merged_data['store_id'] = $new['store_id'];
            
        // legacy store_id under other_data
        if (is_numeric($new['other_data']['home_store_id']) > 0) 
            $merged_data['other_data']['home_store_id'] = $new['other_data']['home_store_id'];

        if (strlen($new['telephone']) > 0) 
            $merged_data['telephone'] = $new['telephone'];

        if (strlen($new['birthday']) > 0) 
            $merged_data['birthday'] = strftime('%Y-%m-%d', strtotime($new['birthday']));
            
        if (is_numeric($new['newsletter'])) 
            $merged_data['newsletter'] = $new['newsletter'];
        
        if (is_numeric($new['agreed_with_latest_t_and_c'])) 
            $merged_data['agreed_with_latest_t_and_c'] = $new['agreed_with_latest_t_and_c'];

        
        if ($this->updatePreservedCustomer($merged_data)) return $merged_data['id'];
        else return false;

    }
    
    /**
     * register customer with extended validation (valid password, address check and notification sent)
     * 
     * @param array $customer_data
     * information about customer
     * 
     * @param array $address_data
     * information about customer's address
     * 
     * @param array $company_data
     * information about customer's company
     * 
     * @return integer
     * customer ID or false if not saved
     */
     
    function registerCustomer($customer_data, $address_data = null, $company_data = null) {
        
        /**
         * check address is valid
         */
        
        if (is_array($address_data)) {
        
            require_once('models/client/client_address.php');
            $Address = new client_address();
            $address_data['delivery']['customer_id'] = 0;
            $Address->setAll($address_data['delivery']);
            
            if (!$Address->getValid()) {
                
                msg('Not a valid address', 'error');
                msg($address_data);
                return false;
            
            }
        }
        
        /**
         * insert customer
         */
         
        if ($customer_data = $this->prepareToRegister($customer_data)) {
            
            $id = $this->insertCustomer($customer_data);
            
            if ($id) {
            
                $customer_data['id'] = $id;
                
                /**
                 * insert company and update customer data
                 */
                 
                if(strlen(trim($company_data['name']))) {
                    
                    require_once('models/client/client_company.php');
                    
                    $company_data['customer_id'] = $customer_data['id'];
                    
                    $Company = new client_company($company_data);
                    
                    if ($company_id = $Company->insert($company_data)) {
                        $customer_data['company_id'] = $company_id;
                        $this->update($customer_data);
                    }
                }
                
                if ($customer_data['status'] != 5 && $this->conf['registration_mail_send_to_customer'] == 1) {

                    /**
                     * send notification email
                     */
                     
                    require_once('models/common/common_email.php');
                    $EmailForm = new common_email();
                    
                    //this allows use customer data and company data in the mail template
                    //is passed as DATA to template in common_email->_format
                    $GLOBALS['common_email']['customer'] = $customer_data;
                    $GLOBALS['common_email']['company'] = $company_data;
                    
                    if (!$EmailForm->sendEmail('registration', 'n/a', $customer_data['email'], $customer_data['first_name'] . " " . $customer_data['last_name'])) {
                        msg('New customer email sending failed.', 'error');
                    }
                    
                    //send it to the customer registration admin email
                    /*
                    if ($GLOBALS['onyx_conf']['global']['admin_email'] != $this->conf['registration_mail_to_address']) {
                        if (!$EmailForm->sendEmail('registration', 'n/a', $this->conf['registration_mail_to_address'], $this->conf['registration_mail_to_name'])) {
                            msg('New customer email sending failed.', 'error');
                        }
                    }*/
        
                    //send notification to admin
                    if (!$EmailForm->sendEmail('registration_notify', 'n/a', $this->conf['registration_mail_to_address'], $this->conf['registration_mail_to_name'])) {
                            msg('Admin notification email sending failed.', 'error');
                    }
                    
                }

                /**
                 * insert address and update customer data
                 */
                 
                $this->insertCustomerAddress($customer_data, $address_data);
                
                /**
                 * return customer ID
                 */
                 
                msg("client_customer.registerCustomer() of customer ID $id was successful.", 'ok', 1);
                
                return $id;
                
            } else {
                
                return false;
            
            }
        }
    }
    
    /**
     * insertCustomerAddress
     */
     
    public function insertCustomerAddress($customer_data, $address_data) {
        
        if (!is_array($customer_data)) return false;
        if (!is_array($address_data)) return false;
         
        require_once('models/client/client_address.php');
        $Address = new client_address();

        /**
         * insert delivery address
         */
        
        $address_data['delivery']['customer_id'] = $customer_data['id'];
        
        if ($delivery_address_id = $Address->insert($address_data['delivery'])) {
            $customer_data['delivery_address_id'] = $delivery_address_id;
        } else {
            msg("Your delivery address is not set!", 'error');
        }

        /**
         * insert invoice address
         */
         
        if (trim($address_data['invoices']['city']) != '') {
        
            $address_data['invoices']['customer_id'] = $customer_data['id'];
            
            if ($invoices_address_id = $Address->insert($address_data['invoices'])) {
                $customer_data['invoices_address_id'] = $invoices_address_id;
            } else {
                msg("Your invoices address is not set! If your invoices address is same as the delivery address, please leave the invoices address fields empty.", 'error');
            }
        } else {
            $customer_data['invoices_address_id'] = $delivery_address_id;   
        }
        
        
        /**
         * update customer record after setting addresses id
         */
         
        return $this->update($customer_data);
                
    }
    
    
    /**
     * This function update client_customer and client_company
     * 
     * @param array $client_data
     * client's information for update
     * 
     * @return boolean
     * is client's information updating successfully [true/false]
     */
    
    function updateClient($client_data) {
    
        require_once('models/client/client_company.php');
        $Company = new client_company();
        
        if (!isset($client_data['customer']['newsletter'])) $client_data['customer']['newsletter'] = 0;
    
        $client_data['customer']['modified'] = date('c');
        
        // company management
        if ($client_data['company']['name'] != '') {
            
            if ($company_id = $Company->updateCompanyForClient($client_data['company'], $client_data['customer']['id'])) {
                $client_data['customer']['company_id'] = $company_id;
            }
            
        }
        
        // unset empty values
        if (!is_numeric($client_data['customer']['company_id'])) unset($client_data['customer']['company_id']);
        if (!is_numeric($client_data['customer']['facebook_id'])) unset($client_data['customer']['facebook_id']);
        if (!is_numeric($client_data['customer']['twitter_id'])) unset($client_data['customer']['twitter_id']);
        if (!is_numeric($client_data['customer']['google_id'])) unset($client_data['customer']['google_id']);
        if ($client_data['customer']['birthday'] == '') unset($client_data['customer']['birthday']);
        
        // check if login id is available (i.e. don't overwrite by changing email address)
        if (!$this->checkLoginId($client_data['customer']) || 
            ($customer_data['customer'] == 1 && !$this->checkLoginIdPreservedOnly($customer_data['customer']['email']))) {
            msg("User email {$customer_data['customer']['email']} is already registered", 'error', 0, 'account_exists');
            return false;       
        }
        
        if ($this->updateCustomer($client_data['customer'])) {
            msg('Customer Data Updated', 'ok', 2);
            return true;
        } else {
            msg("Can't update Customer Data", 'ok', 2);
            return false;
        }
    }
    
    
    /**
     * This function update only client_customer
     * 
     * @param array $customer_data
     * customers's information for update
     * 
     * @return boolean
     * is customer's information updating successfully [true/false]
     */
    
    function updateCustomer($customer_data, $send_notify_email = false) {
    
        if (!is_numeric($customer_data['id'])) {
            msg("customer ID is not numeric in client_customer.updateCustomer", 'error', 2);
            return false;
        }
        
        // clean up attribute which are not part of the model
        unset($customer_data['group_ids']);
        unset($customer_data['group_id']);
        unset($customer_data['role_ids']);

        // make email and username lowercase to avoid duplications
        if (array_key_exists('email', $customer_data)) $customer_data['email'] = strtolower($customer_data['email']);
        if (array_key_exists('username', $customer_data)) $customer_data['username'] = strtolower($customer_data['username']);
        // record when change happened 
        $customer_data['modified'] = date('c');
        // other data is stored as PHP serialized format
        if (is_array($customer_data['other_data'])) $customer_data['other_data'] = serialize($customer_data['other_data']);
        
        // get currently saved data before update
        $client_current_data = $this->detail($customer_data['id']);
        
        /**
         * update password
         */
        
        if (array_key_exists('password', $customer_data)) {
        
            if (strlen($customer_data['password_new']) > 0) {
            
                if ($this->updatePassword($customer_data['password'], $customer_data['password_new'], $customer_data['password_new1'], $client_current_data)) {
                    $customer_data['password'] = $customer_data['password_new'];
                } else {
                    $customer_data['password'] = $client_current_data['password'];
                }
            } else {
                $customer_data['password'] = $client_current_data['password'];
            }
            
            //remove password attributes before update as password is already updated separetelly with check and md5
            unset($customer_data['password']);
            unset($customer_data['password_new']);
            unset($customer_data['password_new1']);
            
        }
        
        //this allows use customer data and company data in the mail template
        //is passed as DATA to template in common_email->_format
        $GLOBALS['common_email']['customer'] = $customer_data;
        
        /**
         * update remaining attributes
         */
         
        if ($this->update($customer_data)) {
            
            /**
             * initialise common_email
             */
             
            require_once('models/common/common_email.php');
            $EmailForm = new common_email();

            /**
             * send notification email only if requested
             */
             
            if ($send_notify_email) {
                
                //notify to new details 
                if (!$EmailForm->sendEmail('customer_data_updated', 'n/a', $customer_data['email'], $customer_data['first_name'] . " " . $customer_data['last_name'])) {
                    msg('Customer data updated email sending failed.', 'error');
                } else {
                    //msg('Sent');
                }
                
            }
            
            /**
             * if email changed, send notification to old email
             */
             
            if (array_key_exists('email', $customer_data)) {
            
                if ($client_current_data['email'] != $customer_data['email']) {
                    if (!$EmailForm->sendEmail('customer_email_changed', 'n/a', $client_current_data['email'], $client_current_data['first_name'] . " " . $client_current_data['last_name'])) {
                        msg('Customer data updated email sending failed.', 'error');
                    } else {
                        //msg('Sent1');
                    }
                }
            }
            
            return true;
        
        } else {
        
            return false;
        
        }
    }
    
    /**
     * login
     * 
     * @param string $username
     * user's login name
     * 
     * @param string $md5_password
     * user's password hashed by MD5
     * 
     * @return integer
     * result - logged customer's detail or false
     */
    
    function login($username, $md5_password = false) {
    
        //msg("calling login($username, $md5_password)", 'ok', 1);
        
        $username = strtolower($username);
        
        /**
         * check username/password and existance of account
         */
         
        if ($this->conf['login_type'] == 'username') {
            if ($md5_password) $customer_detail = $this->loginByUsername($username, $md5_password);
            else $customer_detail = $this->getClientByUsername($username);
        } else {
            if ($md5_password) $customer_detail = $this->loginByEmail($username, $md5_password);
            else $customer_detail = $this->getClientByEmail($username);
        }
        
        /**
         * check account status
         */
        
        if (is_array($customer_detail) && is_numeric($customer_detail['status'])) {
            
            switch ($customer_detail['status']) {
            
                case 0:
                    msg("Your account has been disabled or temporarily locked.", 'error');
                    return false;
                default:
                case 1:
                case 2:
                    return $customer_detail;
                case 3:
                    msg('Registered only for newsletter, please submit full registration to get full access to your account.', 'error');
                    return false;
                case 4:
                    msg('Your account has been deleted, but you can register again with the same email address.', 'error');
                    return false;
            }
            
        } else {
        
            return false;
            
        }
    }
    
    /**
     * login by email
     * 
     * @param string $email
     * user's e-mail address
     * 
     * @param string $md5_password
     * user's password hashed by MD5
     * 
     * @return integer
     * result - logged customer's detail or false
     */
    
    function loginByEmail($email, $md5_password) {
    
        $email = strtolower($email);
        
        $customer_detail = $this->getClientByEmail($email);

        if ($customer_detail) {
            if ($customer_detail['password'] === $md5_password) {
                msg('Login ok', 'ok', 2);
                return $customer_detail;
            } else {
                return false;
            }
        } else {
            msg('Wrong email/password', 'error', 1);
            msg('There is no user with this email', 'error', 2);
            return false;
        }
    }
    
    /**
     * login by username
     * 
     * @param string $username
     * user's login name
     * 
     * @param string $md5_password
     * user's password hashed by MD5
     * 
     * @return integer
     * result - logged customer's detail or false
     */
    
    function loginByUsername($username, $md5_password) {
    
        $username = strtolower($username);
        
        $username_quoted = $this->db->quote($username);
        $customer_detail = $this->listing("lower(username) = $username_quoted");

        if (count($customer_detail) > 0) {
            if ($customer_detail[0]['password'] === $md5_password) {
                msg('Login ok', 'ok', 2);
                return $customer_detail[0];
            }
        } else {
            msg('Wrong username/password', 'error', 1);
            msg('There is no user with this username', 'error', 2);
            return false;
        }
    }
    
    /**
     * logout
     */
     
    public function logout() {
        
        //TODO: save to log?
        
        return true;
    }
    
    /**
     * get greeting
     * 
     * @return string
     * a greetings text dependent on current system time
     */
    
    function _getGreeting() {
    
        $hour=Date("H");
        $minute=Date("i");

        if($minute >= "30") {$hour = $hour+1;}

        if($hour > "5" && $hour <= "7") {$greeting = "Good morning";}
        elseif($hour > "7" && $hour <= "11") {$greeting = "Good forenoon";}
        elseif($hour=="12") {$greeting="Good noon";}
        elseif($hour > "12" && $hour <= "16") {$greeting = "Good afternoon";}
        elseif($hour > "16" && $hour <= "19") {$greeting = "Good late afternoon";}
        elseif($hour > "19" && $hour <="22") {$greeting = "Good evening";}
        else{$greeting = "Go to the bed, now! Good night";}
        
        return $greeting;
    }
    
    /**
     * generate random password
     * 
     * @param integer $size
     * length of password for generate
     * 
     * @return string
     * generated password
     */

    function randomPassword ($size = 5) {
    
        //or use /usr/bin/pwgen?

        $hash= array("1","2","3","4","5","6","7","8","9","a","b","c","d","e","f","g","h","j","k","m","n","p","q","r","s","t","u","v","w","x","y","z");

        $password="";

        for ($i=0 ;$i<=$size-1 ;$i++) {
            $random=rand(0, count($hash)-1);
            $password.=$hash[$random];
        }
        return $password;
    }
    
    /**
     * update password
     * 
     * @param string $password
     * current password (before change)
     * 
     * @param string $password_new
     * new password
     * 
     * @param string $password_new1
     * new password for confirmation
     * 
     * @param string $client_current_data
     * client's data
     * 
     * @return string
     * a new password or false if update don't work
     */
     
    function updatePassword($password, $password_new, $password_new1, $client_current_data) {
    
        //hacked for resetPassword() function which is already md5
        if (md5($password) == $client_current_data['password'] || $password == $client_current_data['password']) {
            
            //make check only if new password verification is provided
            if ($password_new == $password_new1 || !$password_new1) {
            
                $password = $password_new;
                msg('Passwords match.', 'ok', 2);
                
                /**
                 * prepare date for update
                 */
                
                $client_data_for_update = array();
                $client_data_for_update['id'] = $client_current_data['id'];
                $client_data_for_update['password'] = md5($password); //hash password using md5 here
                
                /**
                 * update data
                 */
                 
                if ($this->update($client_data_for_update)) {
                    
                    msg("Password changed for {$client_current_data['email']}");
                    return $password;
                    
                } else {
                
                    msg("Can't update password.", 'error');
                    return false;
                
                }
                
            } else {
            
                msg(I18N_COMPONENT_CLIENT_REGISTRATION_PASSWORD_NOT_MATCH, 'error');
                return false;
            
            }
            
        } else {
            
            msg('Wrong old password!', 'error');
            return false;
        
        }
    }
    
    /**
     * reset password
     * 
     * @param string $email
     * customer's e-mail address
     * 
     * @param string $key
     * a key for this customer's password reset
     * 
     * @return boolean
     * is a reset successfull?
     * 
     * @see getPasswordKey
     */
    
    function resetPassword($email, $key) {
    
        $email = strtolower($email);
        
        $client = $this->getClientByEmail($email);
        
        if (is_array($client)) {
        
            $current_key = $this->getPasswordKey($email);
        
            if ($current_key == $key) {
        
                $client_current_data = $client;
                $password_new = $this->randomPassword();
                
                if ($this->updatePassword($client_current_data['password'], $password_new, $password_new, $client_current_data)) {
                    
                    msg("Password for $email has been updated", 'ok', 2);
                    
                    $customer_data = $client_current_data;
                    $customer_data['password'] = $password_new;
                    
                    /**
                     * send email
                     */
                     
                    require_once('models/common/common_email.php');
                    $EmailForm = new common_email();
                
                    //this allows use customer data and company data in the mail template
                    //is passed as DATA to template in common_email->_format
                    $GLOBALS['common_email']['customer'] = $customer_data;

                    if (!$EmailForm->sendEmail('password_reset', 'n/a', $customer_data['email'], $customer_data['first_name'] . " " . $customer_data['last_name'])) {
                        msg('Password reset email sending failed.', 'error');
                    }
                    
                    return true;
                }
        
            } else {
        
                msg("Wrong key!", 'error');
        
            }
        
        } else {
            //msg('failed', 'error');
            return false;
        }
    }
    
    /**
     * get password key
     * 
     * @param string $email
     * customer's e-mail address
     * 
     * @return string
     * a key for this customer's password
     */
    
    function getPasswordKey($email) {
    
        $email = strtolower($email);
        
        $client = $this->getClientByEmail($email);
        if (is_array($client)) {
            $key = md5($client['password']);
            return $key;
        } else {
            //msg('Attempt to update non existing email', 'error');
            return false;
        }
        
    }
    
    /**
     * get client by email
     * 
     * @param string $email
     * customer's e-mail address
     * 
     * @return array
     * customer's information or false if not found
     */
     
    function getClientByEmail($email) {
    
        $email = strtolower($email);
        
        if ($this->validation('email', 'email', $email)) {
        
            $email_quoted = $this->db->quote($email);
            $client_list = $this->listing("lower(email) = $email_quoted", "id DESC");
        
            if (is_array($client_list) && count($client_list) > 0) {

                if (count($client_list) == 1) {
                    
                    // if one email address is found, use it whatever status it is (further checks on valid status are made in login()
                    $customer_id = $client_list[0]['id'];

                } else {

                    /**
                     * if multiple email addresses are found, use the latest with
                     * status != 4 (not deleted) and
                     * status != 5 (not guest account)
                     */
                    foreach ($client_list as $item) {
                        if ($item['status'] !== 4 && $item['status'] !== 5) {
                            $customer_id = $item['id'];
                            break;
                        }
                    }

                    if (!is_numeric($customer_id)) msg("Cannot find any valid account for $email, but a deleted or guest account is present.", 'error', 1); // don't show this error message to the customer to prevent exposing registered accounts

                }

                if (is_numeric($customer_id)) {

                    return $this->getDetail($customer_id);

                } else {

                    return false;

                }

            } else {

                msg('Email is not registered', 'error', 1); // don't show this error message to the customer
                return false;

            }

        } else {

            return false;

        }
    }
    
    /**
     * get client by username
     * 
     * @param string $username
     * customer's username
     * 
     * @return array
     * customer's information or false if not found
     */
     
    function getClientByUsername($username) {
    
        $username = strtolower($username);
        
        if ($username) {
        
            $username_quoted = $this->db->quote($username);
            $client_list = $this->listing("lower(username) = $username_quoted", "id DESC");
        
            if (is_array($client_list) && count($client_list) > 0) {
                return $this->getDetail($client_list[0]['id']);
            } else {
                msg('Username is not registered', 'error', 2);
                return false;
            }
        } else {
            //msg('failed', 'error');
            return false;
        }
    }
    
    /**
     * newsletter subscribe
     * 
     * @param array $customer
     * customer's information for subscribe to newsletter
     * 
     * @param bool $force_update
     * if true, than client will be updated even he is already subscribed, i.e. registered
     *
     * @return integer
     * customer ID or false if not saved
     */
    
    function newsletterSubscribe($customer, $force_update = false) {
        
        $customer['email'] = strtolower($customer['email']);
        $customer['newsletter'] = 1;
        
        if ($customer_data = $this->getClientByEmail($customer['email'])) {

            /**
             * force update can be dangerous, use with caution and always filter input data
             * i.e. eliminate passing through attributes like the password field in newsletter_subscribe form
             */
             
            if ($force_update) {
                
                $data_to_save = $customer;
                if (!is_numeric($data_to_save['id'])) $data_to_save['id'] = $customer_data['id'];
                
            } else {
                
                // check if client is already subscribed to newsletter
                if ($customer_data['newsletter'] > 0) {
                    msg("Client with email {$customer['email']} is already subscribed", 'ok', 1);
                    return $customer_data['id'];
                }

                $data_to_save = array('id' => $customer_data['id'], 'newsletter' => 1);
            }
            
            // finally make the update
            if ($this->updatePreservedCustomer($data_to_save)) {
                return $customer_data['id'];
            } else {
                return false;
            }
            
        } else {
            // insert new
            if ($customer_id = $this->insertPreservedCustomer($customer)) {
                return $customer_id;
            } else {
                return false;
            }
        }
    }

    /**
     * insert customer as preserved record for special purposes such as newletter or survey
     * 
     * @param array $customer_data
     * basic customer's information
     * 
     * @return integer
     * customer ID or false if not saved
     */
    
    function insertPreservedCustomer($customer_data) {

        /**
         * set data to insert
         */
        
        $customer_data['status'] = 3;
        $customer_data['account_type'] = 0;
        if (!is_numeric($customer_data['agreed_with_latest_t_and_c'])) $customer_data['agreed_with_latest_t_and_c'] = 0;
        $customer_data['verified_email_address'] = 0;
        if (!is_numeric($customer_data['newsletter'])) $customer_data['newsletter'] = 0;
        
        return $this->insertCustomer($customer_data);

    }

    /**
     * update special type of customer
     * 
     * @param array $customer_data
     * customer's information
     * 
     * @return integer
     * customer ID or false if not saved
     */
    
    function updatePreservedCustomer($customer_data) {
        
        if ($this->updateCustomer($customer_data)) {
            return true;
        }

        return false;

    }
    
    /**
     * newsletter unsubscribe
     * 
     * @param string $email
     * customer's e-mail address
     * 
     * @return boolean
     * result of unsubscribe
     */

    function newsletterUnSubscribe($email) {
    
        $customer_data = $this->getClientByEmail($email);
        if ($customer_data) {
            if ($customer_data['newsletter'] == 1) {
                $customer_data['newsletter'] = 0;
                if ($this->updateCustomer($customer_data)) {
                    msg("Unsubscribed $email");
                    return true;
                } else {
                    msg("Can't unsubscribe $email");
                    return false;
                }
            } else {
                msg("Client with email $email is not subscribed", 'error');
            }
        } else {
            msg("Invalid customer", 'error');
        }
    }
    
    /**
     * get clients orders and details
     * this function is currently only used in backoffice
     * 
     * @param array $filter
     * filter rules
     * @param string $order_by
     * @param integer $limit
     * @param integer $offset
     
     * @return array
     * client's orders and details, or false if not found
     */
     
    public function getClientList($filter = false, $order_by = 'client_customer.id DESC', $limit = false, $offset = false) {
    
        if (ONYX_ECOMMERCE) return $this->getClientListHeavy($filter, $order_by, $limit, $offset);
        else return $this->getClientListSimple($filter, $order_by, $limit, $offset);
        
    }
    
    /**
     * get list of clients
     *
     * @param array $filter
     * filter rules
     * @param string $order_by
     * @param integer $limit
     * @param integer $offset
     *
     * @return array
     * list of clients
     */
     
    public function getClientListSimple($filter = false, $order_by = 'client_customer.id DESC', $limit = false, $offset = false) {
        
        $sql = $this->prepareCustomerListQuerySimple($filter, $order_by, $limit, $offset);
        
        return $this->executeSql($sql);
        
    }
    
    
    /**
     * get clients orders and details
     *
     * @param array $filter
     * @param string $order_by
     * @param integer $limit
     * @param integer $offset
     *
     * @return array
     * customer's orders and details, or false if not found
     */
     
    public function getClientListHeavy($filter = false, $order_by = 'client_customer.id DESC', $limit = false, $offset = false) {
        
        $sql = $this->prepareCustomerListQueryHeavy($filter, $order_by, $limit, $offset);
        
        return $this->executeSql($sql);
        
    }
    
    /**
     * getCustomerListCount
     *
     * @param integer $customer_id
     * @param array $filter
     *
     * @return integer $count
     */
     
    public function getCustomerListCount($filter = false) {
        
        if (ONYX_ECOMMERCE) $sql = $this->prepareCustomerListQueryHeavy($filter);
        else $sql = $this->prepareCustomerListQuerySimple($filter);;
        
        $sql = "SELECT count(*) as count FROM ($sql) AS subquery";

        $record = $this->executeSql($sql);

        return (int) $record[0]['count'];
    }
    
    /**
     * prepareCustomerListQuerySimple
     *
     * @param array $filter
     * @param string $order_by
     * @param integer $limit
     * @param integer $offset
     *
     * @return string $sql
     */

    private function prepareCustomerListQuerySimple($filter = false, $order_by = 'client_customer.id DESC', $limit = false, $offset = false)
    {
        /**
         * prepare WHERE query
         */
         
        $add_to_where = $this->prepareCustomerListFilterWhereQuery($filter);
        
        /**
         * query limit
         * 
         */
        
        $add_limit = '';
        if (is_numeric($limit) && $limit > 0) {
            $add_limit = "LIMIT $limit";
            if (is_numeric($offset) && $offset > 0) {
                $add_limit .= " OFFSET $offset";
            }
        }
        
        /**
         * SQL query
         */
        $sql = "SELECT 
            client_customer.id AS customer_id, 
            client_customer.created AS customer_created, 
            client_customer.email, 
            client_customer.title_before, 
            client_customer.first_name,
            client_customer.last_name,  
            client_customer.newsletter,
            client_customer.invoices_address_id,
            client_customer.company_id
            FROM client_customer
            $add_to_where
            ORDER BY $order_by
            $add_limit
            ";
            
        return $sql;
    }
    
    /**
     * prepareCustomerListQueryHeavy
     *
     * TODO: consider using HAVING clause
     * There is one important difference between SQL HAVING and SQL WHERE clauses. The SQL WHERE clause
     * condition is tested against each and every row of data, while the SQL HAVING clause condition
     * is tested against the groups and/or aggregates specified in the SQL GROUP BY clause and/or the
     * SQL SELECT column list.
     * It is important to understand that if a SQL statement contains both SQL WHERE and SQL HAVING clauses
     * the SQL WHERE clause is applied first, and the SQL HAVING clause is applied later to the groups 
     * and/or aggregates.
     *
     * @param array $filter
     * @param string $order_by
     * @param integer $limit
     * @param integer $offset
     *
     * @return string $sql
     */
     
    private function prepareCustomerListQueryHeavy($filter = false, $order_by = 'client_customer.id DESC', $limit = false, $offset = false)
    {
        
        /**
         * prepare WHERE query
         */
         
        $add_to_where = $this->prepareCustomerListFilterWhereQuery($filter);
        
        /**
         * this limits will be added to the end result
         */
         
        $subselect_add_to_where = $this->prepareCustomerListFilterWhereQuerySubselect($filter);
        
        //format product filter array to be ready for SQL
        if (is_array($filter['product_bought'])) $filter['product_bought'] = implode(',', $filter['product_bought']);
        
        /**
         * local_* fields
         */
         
        $table_info = $this->getTableInformation();
        $local_fields = "";
        foreach ($table_info as $attribute) {
            if (preg_match("/^local_/", $attribute['COLUMN_NAME'])) $local_fields .= ", client_customer.{$attribute['COLUMN_NAME']}";
        }
        
        /**
         * query limit
         * 
         */
        
        $add_limit = '';
        if (is_numeric($limit) && $limit > 0) {
            $add_limit = "LIMIT $limit";
            if (is_numeric($offset) && $offset > 0) {
                $add_limit .= " OFFSET $offset";
            }
        }
        
        /**
         * create SQL query
         */
        
        //custom SQL query when product filter is in use
        if ((is_numeric($filter['product_bought']) || preg_match('/^([0-9]{1,},?){1,}$/', $filter['product_bought'])) && $filter['product_bought'] > 0)
        {
        $sql = "SELECT
            client_customer.id AS customer_id, 
            client_customer.created AS customer_created, 

            (   SELECT ecommerce_invoice.created FROM ecommerce_invoice
                INNER JOIN ecommerce_basket ON (ecommerce_basket.customer_id = client_customer.id)
                INNER JOIN ecommerce_order ON (ecommerce_order.basket_id = ecommerce_basket.id)
                ORDER BY ecommerce_invoice.id DESC
                LIMIT 1
            ) AS last_order,

            client_customer.email, 
            client_customer.title_before, 
            client_customer.first_name,
            client_customer.last_name,  
            client_customer.newsletter,
            client_customer.invoices_address_id,
            client_address.country_id,
            client_customer.company_id,
            (   SELECT COUNT(DISTINCT ecommerce_basket.id) FROM ecommerce_basket 
                INNER JOIN ecommerce_basket_content ON (ecommerce_basket_content.basket_id = ecommerce_basket.id AND ecommerce_basket_content.product_variety_id IN
                    (SELECT id FROM ecommerce_product_variety WHERE product_id IN ({$filter['product_bought']})))
                WHERE ecommerce_basket.customer_id = client_customer.id
            ) AS count_baskets,

            (   SELECT COUNT(DISTINCT ecommerce_basket.id) FROM ecommerce_basket 
                INNER JOIN ecommerce_basket_content ON (ecommerce_basket_content.basket_id = ecommerce_basket.id AND ecommerce_basket_content.product_variety_id IN
                    (SELECT id FROM ecommerce_product_variety WHERE product_id IN ({$filter['product_bought']})))
                INNER JOIN ecommerce_order ON (ecommerce_order.basket_id = ecommerce_basket.id)
                WHERE ecommerce_basket.customer_id = client_customer.id
            ) AS count_orders,

            (   SELECT SUM(ecommerce_basket_content.quantity) FROM ecommerce_basket 
                INNER JOIN ecommerce_basket_content ON (ecommerce_basket_content.basket_id = ecommerce_basket.id AND ecommerce_basket_content.product_variety_id IN
                    (SELECT id FROM ecommerce_product_variety WHERE product_id IN ({$filter['product_bought']})))
                WHERE ecommerce_basket.customer_id = client_customer.id
            ) AS count_items,

            (   SELECT SUM(ecommerce_basket_content.quantity * ecommerce_price.value) FROM ecommerce_basket 
                INNER JOIN ecommerce_basket_content ON (ecommerce_basket_content.basket_id = ecommerce_basket.id AND ecommerce_basket_content.product_variety_id IN
                    (SELECT id FROM ecommerce_product_variety WHERE product_id IN ({$filter['product_bought']})))
                INNER JOIN ecommerce_order ON (ecommerce_order.basket_id = ecommerce_basket.id)
                INNER JOIN ecommerce_price ON (ecommerce_price.id = ecommerce_basket_content.price_id)
                WHERE ecommerce_basket.customer_id = client_customer.id
            ) AS goods_net
            $local_fields
            FROM client_customer
            INNER JOIN ecommerce_basket ON (ecommerce_basket.customer_id = client_customer.id)
            INNER JOIN ecommerce_basket_content ON (ecommerce_basket_content.basket_id = ecommerce_basket.id AND ecommerce_basket_content.product_variety_id IN
                    (SELECT id FROM ecommerce_product_variety WHERE product_id IN ({$filter['product_bought']})))
            INNER JOIN ecommerce_order ON (ecommerce_order.basket_id = ecommerce_basket.id)
            INNER JOIN ecommerce_invoice ON  (ecommerce_invoice.order_id = ecommerce_order.id) 
            LEFT OUTER JOIN client_address ON (client_address.id = client_customer.invoices_address_id)
            $add_to_where
            GROUP BY
            client_customer.id,
            client_customer.created,
            client_customer.email, 
            client_customer.title_before,
            client_customer.first_name, 
            client_customer.last_name, 
            client_customer.newsletter,
            client_customer.invoices_address_id,
            client_address.country_id,
            client_customer.company_id
            $local_fields
            ORDER BY $order_by
            $add_limit
            ";
        }
        else
        {
        $sql = "SELECT
            client_customer.id AS customer_id, 
            client_customer.created AS customer_created, 
            MAX(ecommerce_invoice.created) AS last_order,
            client_customer.status, 
            client_customer.email, 
            client_customer.title_before, 
            client_customer.first_name,
            client_customer.last_name,  
            client_customer.newsletter,
            client_customer.invoices_address_id,
            client_address.country_id,
            client_customer.company_id,
            client_customer.telephone,
            client_customer.birthday,
            client_customer.store_id,
            COUNT(ecommerce_basket.id) AS count_baskets,
            COUNT(ecommerce_invoice.id) AS count_orders,
            (SELECT SUM(quantity) FROM ecommerce_basket_content INNER JOIN ecommerce_basket ON (ecommerce_basket.customer_id = client_customer.id AND ecommerce_basket.id = ecommerce_basket_content.basket_id)) AS count_items,
            SUM(ecommerce_invoice.goods_net) AS goods_net
            $local_fields
            FROM client_customer
            LEFT OUTER JOIN ecommerce_basket ON (ecommerce_basket.customer_id = client_customer.id)
            LEFT OUTER JOIN ecommerce_order ON (ecommerce_order.basket_id = ecommerce_basket.id)
            LEFT OUTER JOIN ecommerce_invoice ON  (ecommerce_invoice.order_id = ecommerce_order.id) 
            LEFT OUTER JOIN client_address ON (client_address.id = client_customer.invoices_address_id)
            $product_join
            $add_to_where
            GROUP BY
            client_customer.id,
            client_customer.created,
            client_customer.status, 
            client_customer.email, 
            client_customer.title_before,
            client_customer.first_name, 
            client_customer.last_name, 
            client_customer.newsletter,
            client_customer.invoices_address_id,
            client_address.country_id,
            client_customer.company_id,
            client_customer.telephone,
            client_customer.birthday,
            client_customer.store_id
            $local_fields
            ORDER BY $order_by
            $add_limit
            ";
            
        }

        /**
         * add filter to end result
         */
        
        if ($subselect_add_to_where) $sql = "SELECT * FROM ($sql) AS subquery WHERE 1=1 $subselect_add_to_where";
        
        /**
         * return string
         */
         
        return $sql;
    }
    
    /**
     * Prepare WHERE part of SQL query according to given filter
     *
     * @param array $filter
     *
     * @return string $add_to_where
     *
     */
     
    private function prepareCustomerListFilterWhereQuery($filter = false)
    {
        $add_to_where = 'WHERE 1=1 ';
        
        /**
         * group_id filter
         */
        
        if (is_numeric($filter['group_id'])) {
            if ($filter['group_id'] < 0) $add_to_where .= '';
            else if ($filter['group_id'] == 0) $add_to_where .= " AND (SELECT count(*) FROM client_customer_group WHERE client_customer_group.customer_id = client_customer.id) = 0";
            else if ($filter['group_id'] > 0) $add_to_where .= " AND client_customer.id IN (SELECT customer_id FROM client_customer_group WHERE group_id = {$filter['group_id']})";
        }
        
        /**
         * query filter
         * 
         */
        
        if (is_numeric($filter['query'])) {
            $add_to_where .= " AND client_customer.id = {$filter['query']}";
        } else if (isset($filter['query']) && $filter['query'] !== '') {
            // we could use ILIKE there, but it's not available in mysql
            $query = strtoupper(addslashes($filter['query']));
            //try to explode query by space
            $e_query = explode(" ", $query);
            if (count($e_query) == 2) {
                $add_to_where .= " AND (UPPER(first_name) LIKE '%{$e_query[0]}%' OR UPPER(last_name) LIKE '%{$e_query[1]}%')";
            } else {
                $add_to_where .= " AND (UPPER(email) LIKE '%$query%' OR UPPER(first_name) LIKE '%$query%' OR UPPER(last_name) LIKE '%$query%' OR UPPER(username) LIKE '%$query%')";
            }
        }

        // invoice status filter
        if (is_numeric($filter['invoice_status']) && $filter['invoice_status'] > 0) {
            $add_to_where .= " AND ecommerce_invoice.status = {$filter['invoice_status']}";
        }
        
        //country filter
        if (is_numeric($filter['country_id']) && $filter['country_id'] > 0) {
            $add_to_where .= " AND country_id = {$filter['country_id']}";
        }
        
        // account type (company) filter
        if (is_numeric($filter['account_type'])) {
            
            if ($filter['account_type'] != -1) $add_to_where .= " AND account_type = {$filter['account_type']}";
            
        }
        
        // filter option to search for backoffice users,
        // who are associated via client_customer_role
        if ($filter['backoffice_role_only'] == 1) {
            
            $bo_users_list = $this->getCustomersWithRole();
        
            $bo_users_list_ids = array();
            
            foreach ($bo_users_list as $customer) {
                
                if (is_numeric($customer['id'])) $bo_users_list_ids[] = $customer['id'];
                
            }
            
            if (is_array($bo_users_list_ids) && count($bo_users_list_ids) > 0) {
                
                $bo_users_list_ids_imploded = implode(',', $bo_users_list_ids);
                $add_to_where .= " AND client_customer.id IN ($bo_users_list_ids_imploded)";
            
            }
        }
        
        //created between filter
        if ($filter['created_from'] != false && $filter['created_to'] != false) {
            if  (!preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $filter['created_from']) || !preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $filter['created_to'])) {
                msg("Invalid format for register between. Must be YYYY-MM-DD", "error");
                return false;
            }
            $add_to_where .=" AND client_customer.created BETWEEN '{$filter['created_from']}' AND '{$filter['created_to']}'";
        }
        
        //activity between filter
        if ($filter['activity_from'] != false && $filter['activity_to'] != false) {
            if  (!preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $filter['activity_from']) || !preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $filter['activity_to'])) {
                msg("Invalid format for activity between. Must be YYYY-MM-DD", "error");
                return false;
            }
            $add_to_where .=" AND ecommerce_invoice.created BETWEEN '{$filter['activity_from']}' AND '{$filter['activity_to']}'";
        }
        
        //customer ID
        if (is_numeric($filter['customer_id']) &&  $filter['customer_id'] > 0) $add_to_where .= "AND client_customer.id = {$filter['customer_id']}";
        
                
        return $add_to_where;
    }
    
    /**
     * prepare subselect
     *
     * @param array $filter
     *
     * @return string $subselect_add_to_where
     */
     
    private function prepareCustomerListFilterWhereQuerySubselect($filter = false)
    {
        
        $subselect_add_to_where = false;
        
        if (is_numeric($filter['count_orders']) || is_numeric($filter['goods_net'])) {

            $subselect_add_to_where = '';
            
            //SUBSELECT count_orders filter
            if (is_numeric($filter['count_orders'])) {
                $subselect_add_to_where .= " AND count_orders > {$filter['count_orders']}";
            }
            
            //SUBSELECT goods_net filter
            if (is_numeric($filter['goods_net'])) {
                $subselect_add_to_where .= " AND goods_net > {$filter['goods_net']}";
            }
        }
        
        return $subselect_add_to_where;
    }
    
    /**
     * Get list of products bought by customer
     * 
     * @param string $order
     * direction of sort [ASC/DESC]
     * 
     * @param integer $limit
     * max. number of returned records
     * 
     * @param integer $customer_id
     * ID of customer, or false for all
     * 
     * @param integer $period_limit
     * period in last days of buy
     * 
     * @return array
     * list of products
     */
     
    function getProductsByCustomer($order = 'DESC', $limit = 10, $customer_id = false, $period_limit = 30) {
        
        if ($order == 'DESC' || $order == 'ASC') $order = $order;
        else $order = 'DESC';
        
        if (is_numeric($limit) && $limit > 0) $limit = "LIMIT $limit";
        else $limit = '';
        
        if (is_numeric($customer_id)) $add_sql = "AND basket.customer_id = $customer_id";
        else $add_sql = '';
        
        if (is_numeric($period_limit) && $period_limit > 0) {
            $add_sql .= " AND extract('days' FROM (now() - basket.created) ) < $period_limit";
        }
        
        
        $sql = "SELECT DISTINCT product_variety.product_id AS product_id, 
                product_variety_id, 
                sum(basket_content.quantity) AS count, 
                product.name AS product_name, 
                product_variety.name AS variety_name 
            FROM ecommerce_basket_content basket_content
            LEFT OUTER JOIN ecommerce_product_variety product_variety ON (product_variety.id = product_variety_id)
            LEFT OUTER JOIN ecommerce_product product ON (product.id = product_variety.product_id)
            LEFT OUTER JOIN ecommerce_basket basket ON (basket.id = basket_content.basket_id)
            LEFT OUTER JOIN ecommerce_order eorder ON (eorder.basket_id = basket.id)
            LEFT OUTER JOIN ecommerce_invoice invoice ON (invoice.order_id = eorder.id)
            WHERE invoice.status = 1 AND product.publish = 1 $add_sql
            GROUP BY product_id, product_variety_id, product_name, variety_name 
            ORDER BY count $order $limit";
        
        return $this->executeSql($sql);
        
    }
    
    /**
     * add customers to group
     * input is list from getClientList
     * 
     * @param array $customer_list
     * IDs of customers to assign into group
     * 
     * @param integer $group_id
     * ID of group
     * 
     * @see getClientList
     */
     
    public function addCustomersToGroupFromList($customer_list, $group_id, $group_ids_remove) {
    
        if (!is_array($customer_list) || count($customer_list) == 0) return false;
        if (!is_numeric($group_id)) return false;
         
        $id_list = '';
        
        foreach ($customer_list as $item) {
            $id_list .= "{$item['customer_id']},";  
        }
            
        $id_list = rtrim($id_list, ",");

        if (is_array($group_ids_remove)) $group_ids = $group_ids_remove;
        else $group_ids = array();
        $group_ids[] = $group_id;
        $group_ids = implode(",", $group_ids);

        // first clear all group
        $sql = "DELETE FROM client_customer_group WHERE group_id IN ($group_ids)";
        $this->executeSql($sql);

        // then insert
        $sql = "INSERT INTO client_customer_group (group_id, customer_id)
            SELECT $group_id AS group_id, client_customer.id AS customer_id
            FROM client_customer 
            WHERE id IN ($id_list)";

        $this->executeSql($sql);

        return true;
    }
    
    /**
     * addCustomerToGroup (if not set already)
     * 
     * @param  int $customer_id Customer Id
     * @param  int $group_id     Group Id
     * @return int              Number of updated rows
     */
     
    function addCustomerToGroup($customer_id, $group_id) {
        
        if (!is_numeric($customer_id)) return false;
        if (!is_numeric($group_id)) return false;

        require_once('models/client/client_customer_group.php');
        $CustomerGroup = new client_customer_group();
        
        return $CustomerGroup->assignGroupToCustomer($group_id, $customer_id);
        
    }
    
    /**
     * generateAndSaveOnyxToken
     */
     
    public function generateAndSaveOnyxToken($customer_id) {
        
        require_once('models/client/client_customer_token.php');
        $Token = new client_customer_token();
        $Token->setCacheable(false);
        
        $token = $Token->generateToken($customer_id);
        
        if ($token) {
            
            if (onyxDetectProtocol() == 'https') $secure = true;
        else $secure = false;

            setcookie(ONYX_TOKEN_NAME, $token, time()+3600*24*600, "/", "", $secure, true);
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * getRoleIds
     */
     
    public function getRoleIds($customer_id) {
        
        if (!is_numeric($customer_id)) return false;
        
        require_once 'models/client/client_customer_role.php';
        $Role = new client_customer_role();
        
        return $Role->getCustomersRoleIds($customer_id);
        
    }
    
    /**
     * updateGroups
     */
     
    public function updateGroups($customer_id, $group_ids) {
        
        if (!is_numeric($customer_id)) return false;
        if (!is_array($group_ids)) $group_ids = array(); // set empty array to remove all groups association
        
        require_once('models/client/client_customer_group.php');
        $CustomerGroup = new client_customer_group();
        
        return $CustomerGroup->updateCustomerGroups($customer_id, $group_ids);
        
    }
    
    /**
     * updateRoles
     */
     
    public function updateRoles($customer_id, $role_ids) {
        
        if (!is_numeric($customer_id)) return false;
        if (!is_array($role_ids)) $role_ids = array(); // set empty array to remove all roles association
        
        require_once('models/client/client_customer_role.php');
        $CustomerRole = new client_customer_role();
        
        return $CustomerRole->updateCustomerRoles($customer_id, $role_ids);
        
    }
    
    /**
     * getCustomersWithRole
     */
     
    public function getCustomersWithRole($role_id = false) {
        
        require_once 'models/client/client_customer_role.php';
        $Role = new client_customer_role();
        
        $customer_ids = $Role->getCustomerIdsWithRole($role_id);
        
        $customer_list = array();
        
        foreach ($customer_ids as $customer_id) {
            
            $customer_list[] = $this->detail($customer_id);
            
        }
        
        return $customer_list;
    }
}
