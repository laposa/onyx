<?php
/**
 * class client_customer
 * 
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class client_customer extends Onxshop_Model {

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
	 * 3 - registered only for newsletter (can register by updating the same account detail)
	 * 4 - deleted (can register again)
	 */
	
	var $status;
	
	var $newsletter;
	
	var $birthday;

	var $other_data;
	
	var $modified;
	
	/**
	 * 0 - standard
	 * 1 - request for trade
	 * 2 - approved trade
	 */
	 
	var $account_type;
	
	var $agreed_with_latest_t_and_c;
	
	var $verified_email_address;
	
	/**
	 * REFERENCES client_group(id) ON UPDATE CASCADE ON DELETE RESTRICT
	 * @access private
	 */
	var $group_id;
	
	var $_hashMap = array(
		'id'=>array('label' => 'ID', 'validation'=>'int', 'required'=>true), 
		'title_before'=>array('label' => 'Title', 'validation'=>'string', 'required'=>true),
		'first_name'=>array('label' => 'First name', 'validation'=>'string', 'required'=>true),
		'last_name'=>array('label' => 'Last name', 'validation'=>'string', 'required'=>true),
		'title_after'=>array('label' => 'Title (after)', 'validation'=>'string', 'required'=>false),
		'email'=>array('label' => 'Email', 'validation'=>'email', 'required'=>true),
		'username'=>array('label' => 'Username', 'validation'=>'string', 'required'=>false),
		'telephone'=>array('label' => 'Phone number', 'validation'=>'string', 'required'=>true),
		'mobilephone'=>array('label' => 'Mobile number', 'validation'=>'string', 'required'=>false),
		'nickname'=>array('label' => 'Username', 'validation'=>'string', 'required'=>false),
		'password'=>array('label' => 'Password', 'validation'=>'string', 'required'=>true),
		'company_id'=>array('label' => 'Company', 'validation'=>'int', 'required'=>false),
		'invoices_address_id'=>array('label' => 'Invoice address', 'validation'=>'int', 'required'=>true),
		'delivery_address_id'=>array('label' => 'Delivery address', 'validation'=>'int', 'required'=>true),
		'gender'=>array('label' => 'Gender', 'validation'=>'string', 'required'=>false),
		'created'=>array('label' => 'Date created', 'validation'=>'datetime', 'required'=>true),
		'currency_code'=>array('label' => 'Preferred currency', 'validation'=>'string', 'required'=>false),
		'status'=>array('label' => 'Status', 'validation'=>'int', 'required'=>false),
		'newsletter'=>array('label' => 'Subscribe to newsletter', 'validation'=>'int', 'required'=>false),
		'birthday'=>array('label' => 'Birthday', 'validation'=>'int', 'required'=>false),
		'other_data'=>array('label' => 'Other', 'validation'=>'serialized', 'required'=>false),
		'modified'=>array('label' => 'Date modified', 'validation'=>'datetime', 'required'=>true),
		'account_type'=>array('label' => 'Account Type', 'validation'=>'int', 'required'=>false),
		'agreed_with_latest_t_and_c'=>array('label' => 'Agreed with t-and-c', 'validation'=>'int', 'required'=>false),
		'verified_email_address'=>array('label' => 'Verified Email Address', 'validation'=>'int', 'required'=>false),
		'group_id'=>array('label' => 'Client Group', 'validation'=>'int', 'required'=>false)
	);
	
	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {
	
		if (array_key_exists('client_customer', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['client_customer'];
		else $conf = array();
		
		/**
		 * set default values if empty
		 */
		if ($conf['registration_mail_to_address'] == '') $conf['registration_mail_to_address'] = $GLOBALS['onxshop_conf']['global']['admin_email'];
		if ($conf['registration_mail_to_name'] == '') $conf['registration_mail_to_name'] = $GLOBALS['onxshop_conf']['global']['admin_email_name'];
		//what is the username for authentication? Can be email or username
		if (!($conf['login_type'] == 'email' || $conf['login_type'] == 'username')) $conf['login_type'] = 'email';

		return $conf;
	}
	
	/**
	 * get detail
	 */
	
	function getDetail($id) {
	
		if (!is_numeric($id)) {
			msg('client_customer->getDetail: Id is not numeric', 'error');
			return false;
		}
		
		$data = $this->detail($id);
		
		$data['other_data'] = unserialize($data['other_data']);
		
		return $data;
	}
	
	/**
	 * get client data
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
		if (is_numeric($client['customer']['delivery_address_id'])) $client['address']['delivery'] = $Address->detail($client['customer']['delivery_address_id']);
		if (is_numeric($client['customer']['invoices_address_id'])) $client['address']['invoices'] = $Address->detail($client['customer']['invoices_address_id']);
		
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
	 */
	 
	function prepareToRegister($customer_data) {
		
		//set default values
		$customer_data['company_id'] = 0;
		$customer_data['invoices_address_id'] = 0;
		$customer_data['delivery_address_id'] = 0;
		$customer_data['created'] = date('c');
		$customer_data['modified'] = date('c');
		$customer_data['status'] = 1;
		$customer_data['other_data'] = serialize($customer_data['other_data']);
		if (!is_numeric($customer_data['account_type'])) $customer_data['account_type'] = 0;
		$customer_data['agreed_with_latest_t_and_c'] = 1;
		$customer_data['verified_email_address'] = 0;
		if (!is_numeric($customer_data['newsletter'])) $customer_data['newsletter'] = 0;
		$customer_data['password'] = md5($customer_data['password']);
		
		$this->setAll($customer_data);
	
		if (!$this->checkLoginId($customer_data)) return false;
		
		if ($this->getValid()) {
			return $customer_data;
		} else {
			return false;
		}
	}
	
	/**
	 * check if login id is available for new registration
	 */
	
	function checkLoginId($customer_data) {
	
		if ($this->conf['login_type'] == 'email') {
			if ($this->set('email', $customer_data['email'])) {
				$customer_current = $this->listing("email='{$customer_data['email']}' AND status < 3");
				if (count($customer_current) > 0) {
					msg("User email {$customer_data['email']} is already registered", 'error');
					return false;
				} else {
					return true;
				}
			} else {
				return false;
			}
		} else {
			if ($this->set('email', $customer_data['email']) && $this->set('username', $customer_data['username'])) {
				$customer_current = $this->listing("email='{$customer_data['email']}' OR username='{$customer_data['username']}' AND status < 3");
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
	 * check if is registered for newsletter only
	 */
	
	function checkLoginIdSubscribedNewsletterOnly($email) {
	
		$customer_list = $this->listing("email='{$email}' AND status = 3", 'id DESC');

		if (count($customer_list) > 0) {
			if (count($customer_list) == 1) {
				return $customer_list[0]; 
			} else {
				//this shouldn't really happen in any circumstances
				msg("Multiple newsletter registrations on email {$email}, using first found", 'error');
				return $customer_list[0];
			}
			
		} else {
			return false;
		}
	}
	
	/**
	 * insert a new customer with a check whether the same customer isn't already subscribed to the newsletter
	 * and merge data in the old newsletter account in that case
	 */
	 
	public function insertCustomer($data) {
	
		if ($newsletter_account = $this->checkLoginIdSubscribedNewsletterOnly($data['email'])) {
			//merge data, but keep old created time
			$update_data = array_merge($newsletter_account, $data);
			$update_data['created'] = $newsletter_account['created'];
			$update_data['modified'] = date('c');
			$id = $this->update($update_data);
		} else {
			$data['created'] = date('c');
			$data['modified'] = date('c');
			$id = $this->insert($data);
		}
		
		if (is_numeric($id)) return $id;
		else return false;
	
	}
	
	/**
	 * register customer with extended validation (valid password, address check and notification sent)
	 */
	 
	function registerCustomer($customer_data, $address_data, $company_data = null) {
		
		require_once('models/client/client_address.php');
		require_once('models/client/client_company.php');
		
		$Address = new client_address();
		$address_data['delivery']['customer_id'] = 0;
		$Address->setAll($address_data['delivery']);
		
		if ($Address->getValid() && $customer_data = $this->prepareToRegister($customer_data)) {
			
			$id = $this->insertCustomer($customer_data);
			
			if ($id) {
			
				$customer_data['id'] = $id;
				
				/**
				 * insert company and update customer data
				 */
				 
				if(strlen(trim($company_data['name']))) {
					$company_data['customer_id'] = $customer_data['id'];
					$Company = new client_company($company_data);
					if ($company_id = $Company->insert($company_data)) {
						$customer_data['company_id'] = $company_id;
						$this->update($customer_data);
					}
				}
				
				/**
				 * send notification email
				 */
				 
				require_once('models/common/common_email_form.php');
    			$EmailForm = new common_email_form();
    			
    			//this allows use customer data and company data in the mail template
    			//is passed as DATA to template in common_email_form->_format
    			$GLOBALS['common_email_form']['customer'] = $customer_data;
    			$GLOBALS['common_email_form']['company'] = $company_data;
    			
    			if (!$EmailForm->sendEmail('registration', 'n/a', $customer_data['email'], $customer_data['first_name'] . " " . $customer_data['last_name'])) {
    				msg('New customer email sending failed.', 'error');
    			}
    			
    			//send it to the customer registration admin email
    			/*
    			if ($GLOBALS['onxshop_conf']['global']['admin_email'] != $this->conf['registration_mail_to_address']) {
    				if (!$EmailForm->sendEmail('registration', 'n/a', $this->conf['registration_mail_to_address'], $this->conf['registration_mail_to_name'])) {
    					msg('New customer email sending failed.', 'error');
    				}
    			}*/
    
    			//send notification to admin
    			if (!$EmailForm->sendEmail('registration_notify', 'n/a', $this->conf['registration_mail_to_address'], $this->conf['registration_mail_to_name'])) {
    					msg('Admin notification email sending failed.', 'error');
    			}
				
				
				/**
				 * insert delivery address
				 */
				
				$address_data['delivery']['customer_id'] = $id;
    			
				if ($delivery_address_id = $Address->insert($address_data['delivery'])) {
					$customer_data['delivery_address_id'] = $delivery_address_id;
				} else {
					msg("Your delivery address is not set!", 'error');
				}
		
				/**
				 * insert invoice address
				 */
				 
				if (trim($address_data['invoices']['city']) != '') {
				
					$address_data['invoices']['customer_id'] = $id;
					
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
				 
				$this->update($customer_data);
				
				msg("client_customer.registerCustomer() of customer ID $id was successful.", 'ok', 1);
				
				return $id;
			} else {
				return false;
			}
		}
	}
	
	
	/*
	 * This function update client_customer and client_company
	 */
	
	function updateClient($client_data) {
	
		require_once('models/client/client_company.php');
		$Company = new client_company();
		
		if (!isset($client_data['customer']['newsletter'])) $client_data['customer']['newsletter'] = 0;
	
		//TEMP!!!
		$client_data['customer']['company_id'] = 0;
	
		$client_data['customer']['modified'] = date('c');
		
		//company management
		if ($client_data['company']['name'] != '') {
			$client_data['company']['customer_id'] = $client_data['customer']['id'];
			// TODO: look into the old record and compare (by name, reg.no.?)
			// TEMP: allways insert new one
			$id = $Company->insert($client_data['company']);
			if ($id) {
				$client_data['customer']['company_id'] = $id;
			}
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
	 */
	
	function updateCustomer($customer_data) {
		
		$customer_data['modified'] = date('c');
	
		$client_current_data = $this->detail($customer_data['id']);
		
		if (strlen($customer_data['password_new']) > 0) {
			if ($this->updatePassword($customer_data['password'], $customer_data['password_new'], $customer_data['password_new1'], $client_current_data)) {
				$customer_data['password'] = $customer_data['password_new'];
			} else {
				$customer_data['password'] = $client_current_data['password'];
			}
		} else {
			$customer_data['password'] = $client_current_data['password'];
		}
	
		//remove password_new before update
		unset($customer_data['password_new']);
		unset($customer_data['password_new1']);
		
		//this allows use customer data and company data in the mail template
		//is passed as DATA to template in common_email_form->_format
		$GLOBALS['common_email_form']['customer'] = $customer_data;

		if ($this->update($customer_data)) {
			//send email
			require_once('models/common/common_email_form.php');
			$EmailForm = new common_email_form();
			//notify to new details	
			if (!$EmailForm->sendEmail('customer_data_updated', 'n/a', $customer_data['email'], $customer_data['first_name'] . " " . $customer_data['last_name'])) {
				msg('Customer data updated email sending failed.', 'error');
			} else {
			//msg('Sent');
			}

			//if email changed, send notify to old email as well
			if ($client_current_data['email'] != $customer_data['email']) {
				if (!$EmailForm->sendEmail('customer_data_updated', 'n/a', $client_current_data['email'], $client_current_data['first_name'] . " " . $client_current_data['last_name'])) {
					msg('Customer data updated email sending failed.', 'error');
				} else {
					//msg('Sent1');
				}
			}
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * login
	 */
	
	function login($username, $md5_password) {
	
		/**
		 * check username/password and existance of account
		 */
		 
		if ($this->conf['login_type'] == 'username') {
			$customer_detail = $this->loginByUsername($username, $md5_password);
		} else {
			$customer_detail = $this->loginByEmail($username, $md5_password);
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
	 */
	
	function loginByEmail($email, $md5_password) {
	
		$customer_detail = $this->getClientByEmail($email);

		if ($customer_detail) {
			if ($customer_detail['password'] == $md5_password) {
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
	 */
	
	function loginByUsername($username, $md5_password) {
	
		$customer_detail = $this->listing("username='$username'");

		if (count($customer_detail) > 0) {
			if ($customer_detail[0]['password'] == $md5_password) {
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
	 * get greeting
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
	 */

	function randomPassword ($size = 5) {
	
		//or use /usr/bin/pwgen?

		$hash= array("1","2","3","4","5","6","7","8","9","0","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");

		$password="";

		for ($i=0 ;$i<=$size-1 ;$i++) {
			$random=rand(0, count($hash)-1);
			$password.=$hash[$random];
		}
		return $password;
	}
	
	/**
	 * update password
	 */
	 
	function updatePassword($password, $password_new, $password_new1, $client_current_data) {
	
		if (md5($password) == $client_current_data['password']) {
			
			if ($password_new == $password_new1) {
			
				$password = $password_new;
				msg('Passwords match.', 'ok', 2);
				
				$customer_data = $client_current_data;
				$customer_data['password'] = $password; //keep clean, not MD5
				
				//send email
				require_once('models/common/common_email_form.php');
    
    			$EmailForm = new common_email_form();
    			
    			//this allows use customer data and company data in the mail template
    			//is passed as DATA to template in common_email_form->_format
    			$GLOBALS['common_email_form']['customer'] = $customer_data;
    			
    			//hash password using md5 here
    			$client_current_data['password'] = md5($password);
    			
				if ($this->update($client_current_data)) {
				 	if (!$EmailForm->sendEmail('change_password', 'n/a', $customer_data['email'], $customer_data['first_name'] . " " . $customer_data['last_name'])) {
	    				msg('Update password email sending failed.', 'error');
    				}
				} else {
					msg("Can't update password.", 'error');
				}
				
				return $password;
			} else {
				msg('New passwords does not match!', 'error');
				return false;
			}
		} else {
			msg('Wrong old password!', 'error');
			return false;
		}
	}
	
	/**
	 * reset password
	 */
	
	function resetPassword($email, $key) {
	
		$client = $this->getClientByEmail($email);
		if (is_array($client)) {
			$current_key = $this->getPasswordKey($email);
			if ($current_key == $key) {
				$client_current_data = $client;
				$password_new = $this->randomPassword();
				if ($this->updatePassword($client_current_data['password'], $password_new, $password_new, $client_current_data)) {
					msg("Password for $email has been updated", 'ok', 2);
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
	 */
	
	function getPasswordKey($email) {
	
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
	 */
	 
	function getClientByEmail($email) {
	
		if ($this->validation('email', 'email', $email)) {
		
			$client_list = $this->listing("email = '$email'", "id DESC");
		
			if (is_array($client_list) && count($client_list) > 0) {
				return $client_list[0];
			} else {
				msg('Email is not registered', 'error', 2);
				return false;
			}
		} else {
			//msg('failed', 'error');
			return false;
		}
	}
	
	/**
	 * newsletter subscribe
	 */
	
	function newsletterSubscribe($customer) {
		
		if ($customer_data = $this->getClientByEmail($customer['email'])) {
			//update existing
			if ($customer_data['newsletter'] == 0) {
				$customer_data['newsletter'] = 1;
				if ($this->updateCustomer($customer_data)) {
					return true;
				} else {
					return false;
				}
			} else {
				msg("Client with email {$customer['email']} is already subscribed", 'error');
				return true;
			}
			
		} else {
			//insert new
			if ($this->insertNewletterCustomer($customer)) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * insert newsletter user
	 */
	
	function insertNewletterCustomer($customer_data) {
		
		/**
		 * customize required fields
		 */
		 
		$this->_hashMap['title_before']['required'] = false;
		$this->_hashMap['telephone']['required'] = false;
		$this->_hashMap['password']['required'] = false;
		$this->_hashMap['invoices_address_id']['required'] = false;
		$this->_hashMap['delivery_address_id']['required'] = false;

		/**
		 * set data to insert
		 */
		
		$customer_data['status'] = 3;
		$customer_data['account_type'] = 0;
		$customer_data['agreed_with_latest_t_and_c'] = 0;
		$customer_data['verified_email_address'] = 0;
		$customer_data['newsletter'] = 1;
		
		return $this->insertCustomer($customer_data);
		
	}
	
	/**
	 * newsletter unsubscribe
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
	 * customer_id 0 returns orders of all customers
	 *
	 * @param unknown_type $customer_id
	 * @return unknown
	 */
	 
	function getClientList($customer_id = 0, $filter = false) {
	
		return $this->getClientListHeavy($customer_id, $filter);
	
	}
	
	/**
	 * get list of clients
	 *
	 * @param unknown_type $filter
	 * @return unknown
	 */
	 
	function getClientListSimple($filter = false) {
		
		$add_to_where = '';
		
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


		//group filter
		if (is_numeric($filter['customer_group_id']) && $filter['customer_group_id'] > 0) {
			$add_to_where .= " AND customer_group_id = {$filter['customer_group_id']}";
		}
		
		//account type (company) filter
		if (is_numeric($filter['account_type'])) {
			if ($filter['account_type'] != -1) $add_to_where .= " AND account_type = {$filter['account_type']}";
		}

		
		/**
		 * SQL query
		 */
		$sql = "
SELECT 
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
WHERE 1=1 AND client_customer.status < 4 
$add_to_where
ORDER BY client_customer.id
";
		//msg($sql);
		
		return $this->executeSql($sql);
		
	}
	
	
	/**
	 * get clients orders and details
	 * customer_id 0 returns orders of all customers
	 *
	 * TODO: consider using HAVING clause
		There is one important difference between SQL HAVING and SQL WHERE clauses. The SQL WHERE clause condition is tested against each and every row of data, while the SQL HAVING clause condition is tested against the groups and/or aggregates specified in the SQL GROUP BY clause and/or the SQL SELECT column list.
		
		It is important to understand that if a SQL statement contains both SQL WHERE and SQL HAVING clauses the SQL WHERE clause is applied first, and the SQL HAVING clause is applied later to the groups and/or aggregates.

	 * @param unknown_type $customer_id
	 * @return unknown
	 */
	 
	function getClientListHeavy($customer_id = 0, $filter = false) {
		
		if (!is_numeric($customer_id)) return false;
		
		$add_to_where = 'WHERE 1=1 ';
		
		/**
		 * group_id filter
		 */
		
		if (is_numeric($filter['group_id'])) {
			if ($filter['group_id'] < 0) $add_to_where .= '';
			else if ($filter['group_id'] == 0) $add_to_where .= " AND client_customer.group_id IS NULL";
			else if ($filter['group_id'] > 0) $add_to_where .= " AND client_customer.group_id = {$filter['group_id']}";
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
		
		//account type (company) filter
		if (is_numeric($filter['account_type'])) {
			if ($filter['account_type'] != -1) $add_to_where .= " AND account_type = {$filter['account_type']}";
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
		if ($customer_id > 0) $add_to_where .= "AND client_customer.id = $customer_id";
		
		/**
		 * this limits will be added to the end result
		 */
		 
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
		
		/**
		 * SQL query
		 */
		//product filter
		if (is_numeric($filter['product_bought']) && $filter['product_bought'] > 0)
		{
		$sql = "
SELECT
client_customer.id AS customer_id, 
client_customer.created AS customer_created, 

(	SELECT ecommerce_invoice.created FROM ecommerce_invoice
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

(	SELECT COUNT(DISTINCT ecommerce_basket.id) FROM ecommerce_basket 
	INNER JOIN ecommerce_basket_content ON (ecommerce_basket_content.basket_id = ecommerce_basket.id AND ecommerce_basket_content.product_variety_id IN
		(SELECT id FROM ecommerce_product_variety WHERE product_id = {$filter['product_bought']}))
	WHERE ecommerce_basket.customer_id = client_customer.id
) AS count_baskets,

(	SELECT COUNT(DISTINCT ecommerce_basket.id) FROM ecommerce_basket 
	INNER JOIN ecommerce_basket_content ON (ecommerce_basket_content.basket_id = ecommerce_basket.id AND ecommerce_basket_content.product_variety_id IN
		(SELECT id FROM ecommerce_product_variety WHERE product_id = {$filter['product_bought']}))
	INNER JOIN ecommerce_order ON (ecommerce_order.basket_id = ecommerce_basket.id)
	WHERE ecommerce_basket.customer_id = client_customer.id
) AS count_orders,

(	SELECT SUM(ecommerce_basket_content.quantity) FROM ecommerce_basket 
	INNER JOIN ecommerce_basket_content ON (ecommerce_basket_content.basket_id = ecommerce_basket.id AND ecommerce_basket_content.product_variety_id IN
		(SELECT id FROM ecommerce_product_variety WHERE product_id = {$filter['product_bought']}))
	WHERE ecommerce_basket.customer_id = client_customer.id
) AS count_items,

(	SELECT SUM(ecommerce_basket_content.quantity * ecommerce_price.value) FROM ecommerce_basket 
	INNER JOIN ecommerce_basket_content ON (ecommerce_basket_content.basket_id = ecommerce_basket.id AND ecommerce_basket_content.product_variety_id IN
		(SELECT id FROM ecommerce_product_variety WHERE product_id = {$filter['product_bought']}))
	INNER JOIN ecommerce_order ON (ecommerce_order.basket_id = ecommerce_basket.id)
	INNER JOIN ecommerce_price ON (ecommerce_price.id = ecommerce_basket_content.price_id)
	WHERE ecommerce_basket.customer_id = client_customer.id
) AS goods_net

FROM client_customer
INNER JOIN ecommerce_basket ON (ecommerce_basket.customer_id = client_customer.id)
INNER JOIN ecommerce_basket_content ON (ecommerce_basket_content.basket_id = ecommerce_basket.id AND ecommerce_basket_content.product_variety_id IN
		(SELECT id FROM ecommerce_product_variety WHERE product_id = {$filter['product_bought']}))
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
ORDER BY client_customer.id";
		}
		else
		{
		$sql = "
SELECT
client_customer.id AS customer_id, 
client_customer.created AS customer_created, 
MAX(ecommerce_invoice.created) AS last_order,
client_customer.email, 
client_customer.title_before, 
client_customer.first_name,
client_customer.last_name,  
client_customer.newsletter,
client_customer.invoices_address_id,
client_address.country_id,
client_customer.company_id, 
COUNT(ecommerce_basket.id) AS count_baskets,
COUNT(ecommerce_invoice.id) AS count_orders,
(SELECT SUM(quantity) FROM ecommerce_basket_content INNER JOIN ecommerce_basket ON (ecommerce_basket.customer_id = client_customer.id AND ecommerce_basket.id = ecommerce_basket_content.basket_id)) AS count_items,
SUM(ecommerce_invoice.goods_net) AS goods_net
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
client_customer.email, 
client_customer.title_before,
client_customer.first_name, 
client_customer.last_name, 
client_customer.newsletter,
client_customer.invoices_address_id,
client_address.country_id,
client_customer.company_id
ORDER BY client_customer.id";
		}

		/**
		 * add filter to end result
		 */
		 
		if ($subselect_add_to_where) $sql = "SELECT * FROM ($sql) AS subquery WHERE 1=1 $subselect_add_to_where";

		//msg($sql);
		
		return $this->executeSql($sql);
		
	}
	
	
	/**
	 * Get list of products bought by customer
	 * 
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
		
		
		$sql = "
		SELECT DISTINCT product_variety.product_id AS product_id, product_variety_id, count(product_variety_id) AS count, product.name AS product_name, product_variety.name AS variety_name 
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
	 * move customers to group
	 * input is list from getClientList
	 */
	 
	public function moveCustomersToGroupFromList($customer_list, $group_id) {
	
		if (!is_array($customer_list) || count($customer_list) == 0) return false;
		if (!is_numeric($group_id)) return false;
		 
		/**
		 * this can be very heavy, but there is no simpler way apart from writing very complicated, redundant SQL query
		 */
		 
		$id_list = '';
		
		foreach ($customer_list as $item) {
			$id_list .= "{$item['customer_id']},";	
		}
			
		$id_list = rtrim($id_list, ",");
		
		$update_sql = "UPDATE client_customer SET group_id = $group_id WHERE id IN ($id_list)";
		
		//msg($update_sql);
		
		if ($this->executeSql($update_sql)) return true;
		else return false;
			
	}
}
