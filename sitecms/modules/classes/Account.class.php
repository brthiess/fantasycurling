<?php

/*-----------------------------------/
* Represents a user account and includes all functionality associated with accounts
* @author	Pixel Army
* @date		13-09-06
* @file		Account.class.php
*/

class Account{

	/*-----------------------------------/
	* @var account_role
	* Role id or role name required for successful login
	* Does not accept multiple and does not cascade
	* If no role required for login, leave as null
	* A single account can be assigned multiple roles
	*/
	public $account_role;
	
	/*-----------------------------------/
	* @var account_id
	* Unique representation of an account
	*/
	public $account_id;
	
	/*-----------------------------------/
	* @var secure_ip
	* If IP and/or host names are constantly changing with every request (proxy server), 
	* set to false to disable IP and host name verification on login
	*/
	public $secure_ip;
	
	/*-----------------------------------/
	* @var db
	* Mysqli database object
	*/
	public $db;
	
	/*-----------------------------------/
	* @var root
	* Global root variable
	*/
	private $root;

	/*-----------------------------------/
	* @var path
	* Global path variable
	*/
	private $path;

	/*-----------------------------------/
	* Public constructor function
	*
	* @author	Pixel Army
	* @param	$account_id	Account ID of the account to be loaded
	* @param	$secure_ip	Enable/disable IP and hose verification on login
	* @return	Account		New Account object
	* @throws	Exception
	*/
	public function __construct($account_role=NULL, $account_id=NULL, $secure_ip=true){
		
		global $root, $path;
		$this->root = &$root;
		$this->path = &$path;
		
		//Set database instance
		if(class_exists('Database')){
			$this->db = new Database();
		}else{
			throw new Exception('Missing class file `Database`');
		}	
		
		//Set the account_id
		$this->account_role = $account_role;
		$this->account_id = is_null($account_id) && $this->login_status() ? $this->login_status() : $account_id;
		$this->secure_ip = $secure_ip;

		//Load the rest of the profile informaiton
		if(!is_null($this->account_id)){
			try{
				$this->load_profile();
			}catch(Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}
	
	/*-----------------------------------/
	* Gets the current Account instance
	*
	* @author	Pixel Army
	* @return	Object	Current Account instance
	*/
	public static function get_instance(){
        return self::$Account;
    }

	/*-----------------------------------/
	* Loads all the account and profile information into this object
	*
	* @author	Pixel Army
	* @return	NULL
	* @throws	Exception
	*/
	private function load_profile(){
		$account_sql = array();
		$profile_sql = array();

		//Get Account Info
		$params = array($this->account_id);
		$query = $this->db->query("SELECT * FROM `accounts` WHERE `account_id` = ?", $params);
		
		if($query && !$this->db->error() && $this->db->num_rows() > 0){
			$account_sql = $this->db->fetch_array();
			$account_sql = $account_sql[0];
			$account_sql['roles'] = $this->get_account_roles($this->account_id);
			
			$params = array($this->account_id);
			$query = $this->db->query("SELECT * FROM `account_profiles` WHERE `account_id` = ?",$params);
			if($query && !$this->db->error() && $this->db->num_rows() > 0){
				$profile_sql = $this->db->fetch_array();
				$profile_sql = $profile_sql[0];
			}
			
			//Merge both the account and profile information into one array
			$account = array_merge($account_sql, $profile_sql);
			
			//Set the Account Object's variables
			foreach($account as $info_key => $info_val){
				$this->$info_key = $info_val;
			}
			
			//Load account roles into object
			$this->roles = $this->get_account_roles($this->account_id);
			
			//Load account permissions into object
			$this->permissions = $this->get_account_permissions($this->account_id);
			
		}else{
			throw new Exception('The provided Account ID was not found.');
		}
		
	}

	/*-----------------------------------/
	* Gets list of account roles
	*
	* @author	Pixel Army
	* @param	$account_id	Pass account id to return roles for that account only
	* @return	Array	Array of account roles
	*/
	public function get_account_roles($account_id=NULL){
		$response = array();
		if(!is_null($account_id)){
			$query = $this->db->query("SELECT `account_roles`.`role_id`, `account_roles`.`parent_id`, `account_roles`.`role_name` FROM `account_roles` LEFT JOIN `account_permissions` ON `account_roles`.`role_id` = `account_permissions`.`role_id` WHERE `account_permissions`.`account_id` = ?", array($account_id));
		}else{
			$query = $this->db->query("SELECT * FROM `account_roles`");
		}
		if($query && !$this->db->error()){
			$result = $this->db->fetch_array();
			foreach($result as $row){
				$response[$row['role_id']] = $row;	
			}
		}
		return $response;
	}
	
	/*-----------------------------------/
	* Gets list of cms permissions for given user
	*
	* @author	Pixel Army
	* @param	$account_id	Pass account id to return permissions for that account only
	* @return	Array	Array of cms permissions
	*/
	public function get_account_permissions($account_id=NULL){
		$response = array();
		if(!is_null($account_id)){
			$query = $this->db->query("SELECT `cms_sections`.`section_id`, `cms_sections`.`parent_id`, `cms_sections`.`name` FROM `cms_sections` LEFT JOIN `cms_permissions` ON `cms_sections`.`section_id` = `cms_permissions`.`section_id` WHERE `cms_permissions`.`account_id` = ?", array($account_id));
		}else{
			$query = $this->db->query("SELECT * FROM `cms_sections`");
		}
		if($query && !$this->db->error()){
			$result = $this->db->fetch_array();
			foreach($result as $row){
				$response[$row['section_id']] = $row;	
			}
		}
		return $response;
	}
	
	/*-----------------------------------/
	* Gets any database field
	*
	* @author	Pixel Army
	* @param	$table	The table name to be queried
	* @param	$field	The field name to be returned
	* @return	mixed	The value of the requested field
	*/
	public function get_db_param($table, $field, $account_id=NULL){
		$account_id = is_null($account_id) ? $this->account_id : $account_id;
		
		$params = array($account_id);
		$query = $this->db->query("SELECT `$field` FROM `$table` WHERE `account_id` = ?", $params);
		
		if($query && !$this->db->error()){
			$response = $this->db->fetch_array();
			$response = $response[0];
		}
		
		return $response[$field];
	}

	/*-----------------------------------/
	* Checks to see if the passed value is unique in a table
	*
	* @author	Pixel Army
	* @param	$table	The table name to be searched
	* @param	$field	The field name to be checked
	* @param	$value	The value to be checked
	* @param	$account_id	If passed, the unique check will skip over rows with that account (for updating accounts)
	* @return	int		Number of existing rows with that value
	*/
	private function is_unique($table, $field, $value, $account_id=NULL){
		
		if(!is_null($account_id)){
			$params = array($value,$account_id);
			$query = $this->db->query("SELECT * FROM `$table` WHERE `$field` = ? && account_id != ?",$params);
		} else {
			$params = array($value);
			$query = $this->db->query("SELECT * FROM `$table` WHERE `$field` = ?",$params);
		}		
		
		if($query && !$this->db->error()){
			$response = $this->db->fetch_array();
		}
		
		return $this->db->num_rows();		
	}

	/*-----------------------------------/
	* Gets the accounts status, before returning the value, it checks
	* to see if the account is expired, if so it updates the Account
	* Status and return the new value
	*
	* @author	Pixel Army
	* @return	String	String representation of account's status
	* @throws	Exception
	*/
	public function get_status(){

		//Check to see if account has expired, if so update the databse
		if($this->is_expired()){
			try{
				$this->set_status('Expired');
			}catch(Exception $e){
				throw new Exception($e->getMessage());
			}
		}

		//Get the current account status and return it
		return $this->get_db_param('accounts', 'status');
	}

	/*-----------------------------------/
	* Set the accounts status to the given value
	*
	* @author	Pixel Army
	* @param	$status		The status to set the account to
	* @param	$account_id	The account ID being updated
	* @return	NULL
	* @throws	Exception
	*/
	public function set_status($status, $account_id=NULL){
		$account_id = !is_null($account_id) ? $account_id : $this->account_id;
		
		//Get the allowed status values
		$allowed_values = $this->get_enum_values('accounts','status');
		
		if(in_array($status, $allowed_values)){
			$params = array($status,$account_id);
			$query = $this->db->query("UPDATE `accounts` SET `status` = ? WHERE `account_id` = ?",$params);
			return true;
		}else{
			throw new Exception('Status `'.$status.'` is not allowed. Allowed values: '.implode(', ', $allowed_values).'.');
		}
	}


	/*-----------------------------------/
	* Get the info of the last user visit/login
	*
	* @author	Pixel Army
	* @return	Array	Info of the last login
	* @throws	Exception
	*/
	public function get_last_login(){
		if($this->login_status()){
			
			$params = array($this->account_id);
			$query = $this->db->query("SELECT * FROM `account_session_log` WHERE `account_id` = ? ORDER BY `visit_time` DESC LIMIT 1",$params);
			
			if($query && !$this->db->error()){
				$response = $this->db->fetch_array();
				$response = $response[0];
			}
			
			return $response;
		}else{
			throw new Exception('User not logged in.');
		}
	}

	/*-----------------------------------/
	* Check the expiry of the account
	*
	* @author	Pixel Army
	* @return	boolean		True = expired, false = active
	*/
	public function is_expired(){

		//Get the expiry date
		$exp_date = $this->expiry;
		return strtotime($exp_date) < time() && $exp_date != '' ? true : false;
	}

	/*-----------------------------------/
	* Gets the allowed values of ENUM field
	*
	* @author	Pixel Army
	* @param	$table	The name of the table
	* @param	$field	The name of the field
	* @return	Array	The supported values
	*/
	private function get_enum_values($table, $field){
		$params = array($field);
		$query = $this->db->query("SHOW COLUMNS FROM `$table` WHERE `Field` = ?",$params);
		
		if($query && !$this->db->error()){
			$response = $this->db->fetch_array();
			$response = $response[0];
		}
		
		$allowed_values = $response['Type'];
		return explode("','",str_replace(array("enum('", "')"), '', $allowed_values));
	}

	/*-----------------------------------/
	* Gets the column field names of a table
	*
	* @author	Pixel Army
	* @param	$table	The name of the table
	* @return	Array	The existing fields
	*/
	private function get_fields($table){
		$fields = array();
		
		$query = $this->db->query("SHOW COLUMNS FROM `$table`");
		
		if($query && !$this->db->error()){
			$fields_sql = $this->db->fetch_array();
		}
		
		foreach($fields_sql as $field){
			array_push($fields,$field['Field']);
		}
		
		return $fields;
	}

	/*-----------------------------------/
	* Checks against predefined regular expressions
	*
	* @author	Pixel Army
	* @param	$value	The value to be checked
	* @param	$rkey	The Regex key to be used
	* @return	int		Returns matches number, or false upon error
	* @throws	Exception
	*/
	private function validate($value, $rkey){
		$regex = array(
			'email' => '/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/i',
			'phone' => '/^[\d]+$/i',
			'postalcode' => '/^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$/i',
			'zipcode' => '/^\d{5}([\-]?\d{4})?$/i'
		);

		if(in_array($rkey, array_keys($regex))){
			return preg_match($regex[$rkey], $value);
		}else{
			throw new Exception("`$rkey` is not a supported validation type.");
		}
	}

	/*-----------------------------------/
	* Registers a user for an account
	*
	* Usage:
	* The params array will be in the following format
	* Array( [0] =>
	* 		Array(
	* 			'param' => 'first_name', 'value' => 'John', 'label' => 'First name', 'required' => false, 'unique' => true, 'validate' => false, 'hash' => false
	* 		)
	* )
	*
	* 'validate' param can be false or type of validation needed (Supported: email, phone, postalcode, zipcode, profanity) that's built in
	* or an actual Regular Expression to validate against custom rules
	*
	* WARNING: 'hash' is only set to true for passwords, any param set with hash = true will get the value hashed using sha1_salt
	* 			Hashed values cannot be obtained again!
	*
	* @author	Pixel Army
	* @param	$params			Array of options (i.e. username, password, email, etc.)
	* @param	$roles			Array of account roles (can pass IDs or names) Will default to `User` if none provided
	* @param	$confirm_email	Sends an email to confirm account
	* @param	$send_welcome	Send a welcome email to user
	* @param	$autologin		If set to true, the user will be logged in after registration (default = true)
	* @return	boolean			True upon successful registration
	* @throws	Exception
	*/
	public function register($params, $roles=array(), $confirm_email=true, $send_welcome=false, $autologin=false){
		$response = array();
		$subquery_keys = $subquery_values = array();
		$credentials = array('unique_id'=>'', 'password'=>'');
		$account_roles = array();
		
		//Parameters cannot be empty
		if(is_array($params) && count($params) > 0){
		
			//Go through each param and validate
			foreach($params as $key => $param){

				//The database table the param is in
				if(in_array($param['param'], $this->get_fields('accounts'))){
					$param_tbl = 'accounts';
				}else if(in_array($param['param'],$this->get_fields('account_profiles'))){
					$param_tbl = 'account_profiles';
				}

				//If param was found in either accounts or profile table
				if(isset($param_tbl)){
					
					//Check to see if param value is not empty
					if((!is_array($param['value']) && $param['value'] != '') || (is_array($param['value']) && !empty($param['value']))){

						//If this is a unique value then check to see if it exists in the accounts table
						if($param['unique'] === true && $this->is_unique($param_tbl, $param['param'], $param['value']) != 0){
							
							//If the param email was found, remove any old pending registrations
							if($param['param'] == 'email'){
								
								$sql_params = array($param['value'],"Pending");
								$query = $this->db->query("SELECT status FROM `accounts` WHERE `email` = ? && status = ?", $sql_params);
		
								if($query && !$this->db->error() && $this->db->num_rows() > 0){
									$sql_params = array($param['value'],"Pending");
									$delete = $this->db->query("DELETE db, ap FROM `accounts` db LEFT JOIN `account_profiles` ap ON ap.account_id = db.account_id WHERE db.email = ? && db.status = ?",$sql_params);
								} else {
									$response['error'][] = "The $param[param] `$param[value]` already exists. Please choose another.";
								}
								
							}else{
								$response['error'][] = "The $param[param] `$param[value]` already exists. Please choose another.";
							}
						}

						//If needs validation then apply regex
						if($param['validate'] !== false && $param['validate'] != ''){

							//Filter profanity
							if($param['validate'] == 'profanity'){
								
								if($this->filter_phrase($param['value'])){
									$response['error'][] = "Value entered for `$param[label]` is unavailable. Please choose another.";	
								}
							
							//Regex filter
							}else{
								
								//If passed a custom regex
								if(substr($param['validate'],0,1) == '/' && substr($param['validate'],-1) == '/'){

									$regex_match = preg_match($param['validate'], $param['value']);

								//If passed a predefined regex key
								}else{

									try{
										$regex_match = $this->validate($param['value'], $param['validate']);
									}catch(Exception $e){
										$response['error'][] = $e->getMessage();
									}
								}

								if(!$regex_match){
									$response['error'][] = "Value entered for `$param[label]` is invalid.";
								}
							}
						}
						
					}else if($param['required'] === true){
						$response['error'][] = "`$param[label]` is a required field and cannot be empty.";
					}
				}else{
					$response['error'][] = "`$param[label]` does not exist in the accounts or the profiles tables.";
				}

				//Check to see if this param is login credentials
				if($param['unique'] === true){
					$credentials['unique_id'] = array('param'=>$param['param'], 'value'=>$param['value']);
				}else if($param['hash'] === true){
					$credentials['password'] = array('param'=>$param['param'], 'value'=>$param['value']);
				}

				//Compile the query string
				$subquery_keys[$param_tbl][] = $param['param'];

				if($param['hash'] === true){
					$hash = $this->sha1_salt($param['value']);
					$param['value'] = $hash['encrypt'];

					$subquery_values[$param_tbl][] = $param['value'];
					$subquery_keys[$param_tbl][] = $param['param'].'_salt';
					$subquery_values[$param_tbl][] = $hash['salt'];

				}else{
					$subquery_values[$param_tbl][] = $param['value'];
				}
			}
			
			//Check if roles are valid
			if(empty($roles)) {
				$roles[] = 'User';	
			}
			if(is_array($roles) && count($roles) > 0){
				foreach($roles as $role){
					$query = $this->db->query("SELECT `role_id` FROM `account_roles` WHERE " .(is_numeric($role) ? "`role_id`" : "`role_name`"). " = ?", array($role));
					if($query && !$this->db->error()){
						if($this->db->num_rows() > 0){
							$result = $this->db->fetch_array();
							$account_roles[] = $result[0]['role_id'];
						}else{
							$response['error'][] = "Account role `$role` is not valid.";
						}
					}else{
						throw new Exception($this->db->error());	
					}
				}
			}

			// Check if creating a Front-end only user
			$frontend_user = false;
			if(in_array('User', $roles) || in_array(2, $roles)) {
				$frontend_user = true;
			}
			
		}else{
			$response['error'][] = "Incorrect format or missing parameters.";	
		}

		//Only if no errors occured, register account
		if(empty($response['error'])){
			
			//Start new mysqli transaction
			$this->db->new_transaction();
			
			//Insert account info
			$sql_params = $subquery_values['accounts'];
			$values = "";
			foreach($sql_params as $sql){
				$values .= "?,";
			}
			$values = substr($values, 0, -1);		
			$query = $this->db->query("INSERT INTO `accounts` (`".implode('`,`',$subquery_keys['accounts'])."`) VALUES (".$values.")",$sql_params);
			
			//Get the new account ID
			$new_account_id = $this->db->insert_id();
			
			//Save the profile
			if(!empty($subquery_keys['account_profiles'])){
			
				$sql_params = $subquery_values['account_profiles'];
				$values = "";
				foreach($sql_params as $sql){
					$values .= "?,";
				}
				$values = substr($values, 0, -1);
				$sql_params[] = $new_account_id;			
				$query = $this->db->query("INSERT INTO `account_profiles` (`".implode('`,`',$subquery_keys['account_profiles'])."`,`account_id`) VALUES (".$values.",?)",$sql_params);
					
			}
			
			//Insert account roles
			if(is_array($account_roles) && count($account_roles) > 0){
				foreach($account_roles as $role_id){
					$query = $this->db->query("INSERT INTO `account_permissions`(`account_id`, `role_id`) VALUES(?,?)", array($new_account_id, $role_id));
				}
			}
			
			//If no errors, commit transaction
			if(!$this->db->error()){
				$this->db->commit();
			}else{
				throw new Exception($this->db->error());
			}
			
			//Create a new Account Secret
			$this->reset_secret($new_account_id);

			//If requested, user will receive an email to verify their account
			if($confirm_email){
				try{
					$this->confirm_email($new_account_id,(!$frontend_user ? ltrim($this->path, '/') : '').'includes/emailtemplates/confirmation.htm');
				}catch(Exception $e){
					throw new Exception($e->getMessage());
				}
				$this->set_status('Pending', $new_account_id);
			}else{
				$this->set_status('Active', $new_account_id);
			}

			//If no verfication is required, and send_welcome is true, then send welcome email
			if(!$confirm_email && $send_welcome){
				try{
					$this->send_welcome($new_account_id,(!$frontend_user ? ltrim($this->path, '/') : '').'includes/emailtemplates/welcome.htm');
				}catch(Exception $e){
					throw new Exception($e->getMessage());
				}
			}

			//If no verfication is required, automatic login is enabled then log user in
			if(!$confirm_email && $autologin){
				try{
					$this->login($credentials);
				}catch(Exception $e){
					throw new Exception($e->getMessage());
				}
			}

			//Return newly created account ID
			return $new_account_id;
			
		//Throw the errors
		}else{
			throw new Exception($response['error'][0]);
		}
	}
	
	/*-----------------------------------/
	* Activates a new account via public_key
	*
	* @author	Pixel Army
	* @param	key		The public_key sent in confirmation email
	* @return	boolean		True upon success
	* @throws	Exception
	*/
	public function activate_account($key){
		
		//Check public key
		$account_id = $this->validate_public_key($key);
		if($account_id){
			
			//Update status
			try{
				$this->update_profile(array(
					array('param' => 'status', 'value' => 'Active', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false)
				), $account_id);
				
				//Delete public key
				$this->clear_public_key($key);
				
			}catch(Exception $e){
				throw new Exception($e->getMessage());
			}
		}else{
			throw new Exception('Activation code has expired. Please register again.');	
		}
		
	}

	/*-----------------------------------/
	* Checks users credentials, if passed user is granted a session access to account
	*
	* Usage:
	* $credentials = Array(
	* 		'unique_id' = Array(
	* 			'param' => 'username',
	* 			'value' => 'pixelarmy'
	* 		),
	* 		'password' = Array(
	* 			'param' => 'password',
	* 			'value' => 'testing123'
	* 		)
	* )
	*
	* @author	Pixel Army
	* @param	$credentials	Array that holds username/email and password
	* @return	boolean			True if login is successful
	* @throws	Exception
	*/
	public function login($credentials, $remember_me=false){
		
		//Check to see if there's an active session
		if(!$this->login_status() || $this->secure_ip == false){

			//Check to see if the user exits
			$user_query = "SELECT * FROM `accounts` WHERE `".$credentials['unique_id']['param']."` = ?";
			
			$params = array($credentials['unique_id']['value'],"Active");
			$query = $this->db->query($user_query." AND `status` = ?",$params);
			
			if($query && !$this->db->error() && $this->db->num_rows() > 0){
				$response = $this->db->fetch_array();
				$account = $response[0];
				
				//Set the account_id to the matching account
				$this->account_id = $account['account_id'];

				//If secure_ip is disabled, check to see if there is an active login
				if($this->login_status() && $this->secure_ip == false){
					$this->clear_user_sessions($this->account_id, true);	
				}

				//Check password matching
				$user_password = $this->sha1_salt($credentials['password']['value'],$account[$credentials['password']['param'].'_salt']);
				if($user_password['encrypt'] == $account[$credentials['password']['param']]){

					//Check user role
					if($this->account_has_role($this->account_role, $this->account_id)){

						//Clear all the previous sessions from this machine and connection
						$this->clear_user_sessions($this->account_id);
	
						//Set the session vars
						$_SESSION['auth']['login_id'] = $this->generate_unique_id('account_session_log', 'login_id');
	
						//Save this login session into the log
						if(!$this->log_session($account, $_SESSION['auth']['login_id'])){
							trigger_error('Could not log user session.', E_USER_WARNING);
						}
	
						//If Remember Me is enabled, then update table
						if($remember_me){
							try{
								$this->remember_me();
							}catch(Exception $e){
								throw new Exception($e->getMessage());
							}
						}else{
							setcookie('auth[reme_id]','--',time()-3600,'/');	
						}
	
						return true;
						
					}else{
						throw new Exception('Invalid user permissions.');	
					}

				}else{
					throw new Exception('Invalid username/password.');
				}

			} else {
				//Check to see if account is not Active
				$params = array($credentials['unique_id']['value'],"Active");
				$query = $this->db->query($user_query." AND `status` <> ?",$params);
				
				if($query && !$this->db->error() && $this->db->num_rows() > 0){
								
					//Get the account row
					$response = $this->db->fetch_array();
					$account = $response[0];

					//Set the account_id to the matching account
					$this->account_id = $account['account_id'];

					throw new Exception('Cannot login. Account is "'.$this->get_db_param('accounts', 'status').'".');

				//Otherwise, throw exception 'not found'
				}else{
					throw new Exception('Invalid username/password.');
				}
			}
			
		}else{
			throw new Exception('An active user is already logged in. Please logout first.');
		}
	}

	/*-----------------------------------/
	* Ends this user session
	*
	* @author	Pixel Army
	* @return	boolean		True upon success
	*/
	public function logout($login_id=NULL){

		//If user is logged in properly
		if($this->login_status()){

			//If the login_id is not provided, default to the current login_id
			if(is_null($login_id)){
				$login_id = $_SESSION['auth']['login_id'];
			}

			//If the query has updated the account_session_log
			$params = array("Inactive",$login_id);
			$query = $this->db->query("UPDATE `account_session_log` SET `status` = ? WHERE `login_id` = ?",$params);
			
			if($query && !$this->db->error()){
				//successfull
			} else {
				throw new Exception($this->db->error());
			}
			
			unset($_SESSION['auth']);
			setcookie('auth[reme_id]','--',time()-3600,'/');
			return true;
			
		}
		return false;
	}

	/*-----------------------------------/
	* Get login status
	*
	* @author	Pixel Army
	* @return	mixed		False if not logged in, account_id if logged in
	*/
	public function login_status(){
		
		//Login status
		$status = false;
		$status_query = "";
		
		//Find an Active session with the current session_id, IP, Hostname, and login_id/reme_id
		$params = array();

		//If either reme_id or login_id are set, expand query conditions
		if(!empty($_COOKIE['auth']['reme_id']) || !empty($_SESSION['auth']['login_id'])){
			
			$params[] = "Active";
			$status_query = "SELECT `login_id`, `account_id` FROM `account_session_log` WHERE `status` = ?";

			if($this->secure_ip){
				$params[] = $this->get_ip();
				$params[] = gethostbyaddr($this->get_ip());
				$status_query .= " AND `ip_address` = ? AND `hostname` = ?";
			}
			
			//If reme_id cookie is set, then find session based on reme_id otherwise use login_id
			if(!empty($_COOKIE['auth']['reme_id']) && empty($_SESSION['auth']['login_id'])){
				$params[] = $_COOKIE['auth']['reme_id'];
				$status_query .= " AND `reme_id` = ?";
			}else if(isset($_SESSION['auth']['login_id']) && $_SESSION['auth']['login_id'] != ''){
				$params[] = session_id();
				$params[] = $_SESSION['auth']['login_id'];
				$status_query .= " AND `session_id` = ? AND `login_id` = ?";
			}
		}

		if($status_query != '') {
			
			//Check if user is found
			$query = $this->db->query($status_query,$params);
			if($query && !$this->db->error() && $this->db->num_rows() > 0){
				$response = $this->db->fetch_array();
				$status_sql = $response[0];
				
				//Check user permissions
				if($this->account_has_role($this->account_role, $status_sql['account_id'])){
				
					$_SESSION['auth']['login_id'] = $status_sql['login_id'];
					$status = intval($status_sql['account_id']);
	
					//Update the session_id of this login
					$this->update_log_session($_SESSION['auth']['login_id']);
					
				}
				
			}else{
				//If authentication has failed, use either login_id or reme_id to obtain account_id
				//and deactivate all session for that user
				
				$params[] = (isset($_SESSION['auth']['login_id']) ? $_SESSION['auth']['login_id'] : '');
				$params[] = "";
				$params[] = (isset($_COOKIE['auth']['reme_id']) ? $_COOKIE['auth']['reme_id'] : '');
				$params[] = "";
				$query = $this->db->query($status_query." AND ((`login_id` = ? AND `login_id` <> ?) OR (`reme_id` = ? AND `reme_id` <> ?))",$params);
				
				if($query && !$this->db->error() && $this->db->num_rows() > 0){
				
					$response = $this->db->fetch_array();
					$inactive_user_sql = $response[0];
					
					$inactive_account_id = $inactive_user_sql['account_id'];
					$this->clear_user_sessions($inactive_account_id, true);
				}
			}
		}
		
		return $status;
	}
	
	/*-----------------------------------/
	* Validates user against a role
	*
	* @author	Pixel Army
	* @param	$account_role	Name or id of role
	* @param	$account_id		User account_id
	* @return	Boolean			True/false on valid or not
	*/
	public function account_has_role($account_role=NULL, $account_id=NULL){
		
		$account_role = is_null($account_role) ? $this->account_role : $account_role;
		$account_id = is_null($account_id) ? $this->account_id : $account_id;
		
		//Role has been specified
		if(!is_null($account_role)){
		
			$field = (is_numeric($account_role) ? 'role_id' : 'role_name');
			
			$query = $this->db->query("SELECT `account_roles`.`$field` FROM `account_roles` LEFT JOIN `account_permissions` ON `account_roles`.`role_id` = `account_permissions`.`role_id` WHERE `account_roles`.`$field` = ? && `account_permissions`.`account_id` = ?", array($account_role, $account_id));
			if($query && !$this->db->error() && $this->db->num_rows() > 0){
				return true;
			}else{
				return false;	
			}
			
		//No role restrictions
		}else{
			return true;
		}
	}

	/*-----------------------------------/
	* Returns active user account_id
	*
	* @author	Pixel Army
	* @return	int			Active user account_id
	*/
	public function get_active_account_id(){
		if($this->login_status()){
			
			$params = array($_SESSION['auth']['login_id']);
			$query = $this->db->query("SELECT `account_id` FROM `account_session_log` WHERE `login_id` = ?",$params);
			if($query && !$this->db->error()){
				
				$response = $this->db->fetch_array();
				$account_sql = $response[0];
				
				return intval($account_sql['account_id']);
				
			}
			
		} else {
			return false;
		}
	}

	/*-----------------------------------/
	* Logs the current login session
	*
	* @author	Pixel Army
	* @param	$account	Array of account info (Required: account_id)
	* @param	$login_id	Unique login ID
	* @return	boolean		True/false upon success/failure
	*/
	private function log_session($account, $login_id){
		$params = array($this->account_id,$login_id,session_id(),$this->get_ip(),gethostbyaddr($this->get_ip()));
		return $this->db->query("INSERT INTO `account_session_log` (`account_id`,`login_id`,`session_id`,`ip_address`,`hostname`) VALUES (?,?,?,?,?)",$params);
	}

	/*-----------------------------------/
	* Updates the session_id of the current login (triggered by Remeber Me ONLY)
	*
	* @author	Pixel Army
	* @param	$login_id	Unique login ID
	* @return	boolean		True/false upon success/failure
	*/
	private function update_log_session($login_id){
		$params = array(session_id(),$login_id);
		return $this->db->query("UPDATE `account_session_log` SET `session_id` = ? WHERE login_id = ?",$params);
	}

	/*-----------------------------------/
	* Clears all login sessions created for this user. This action is performed
	* when login_id Session value or reme_id Cookie value fail to authenticate.
	* This is an added security measure, forces user to create new authentication
	* login session
	*
	* @author	Pixel Army
	* @param	$account_id	The account_id to clear all sessions for
	* @return	boolean		True/false upon success status
	*/
	public function clear_user_sessions($account_id, $all_connections=false){
		$params = array("Inactive",$account_id);
		
		if(!$all_connections){
			$params[] = $this->get_ip();
			$params[] = gethostbyaddr($this->get_ip());
			$connection_query = " AND `ip_address` = ? AND `hostname` = ?";
		} else {
			$connection_query = "";
		}
		
		return $this->db->query("UPDATE `account_session_log` SET `status` = ? WHERE `account_id` = ?".$connection_query,$params);
	}

	/*-----------------------------------/
	* Update account parameters
	*
	* Usage:
	* The params array will be in the following format
	* Array( [0] =>
	* 		Array(
	* 			'param' => 'first_name', 'value' => 'John', 'label', => 'First name', 'required' => false, 'unique' => true, 'validate' => false, 'hash' => false
	* 		)
	* )
	*
	* 'validate' param can be false or type of validation needed (Supported: email, phone, postalcode, zipcode, profanity) that's built in
	* or an actual Regular Expression to validate against custom rules
	*
	* WARNING: 'hash' is only set to true for passwords, any param set with hash = true will get the value hashed using sha1_salt
	* 			Hashed values cannot be obtained again!
	*
	* @author	Pixel Army
	* @param	$params		Array of options (i.e. username, password, email, etc.)
	* @return	boolean		True upon successful update
	* @throws	Exception
	*/
	public function update_profile($params, $account_id=NULL){
		$response = array();
		$subquery = array();
		$sql_params = array();

		//Check for an active user
		if($this->login_status() || !is_null($account_id)){

			//Assign the account_id
			$account_id = is_null($account_id) ? $this->account_id : $account_id;

			//Go through each param and validate
			foreach($params as $key => $param){

				//The database table the param is in
				if(in_array($param['param'],$this->get_fields('accounts')))
					$param_tbl = 'accounts';
				else if(in_array($param['param'],$this->get_fields('account_profiles')))
					$param_tbl = 'account_profiles';

				//If param was found in either accounts or profile table
				if(isset($param_tbl)){

					//Check to see if param value is not empty
					if((!is_array($param['value']) && $param['value'] != '') || (is_array($param['value']) && !empty($param['value']))){

						//If this is a unique value then check to see if it exists in the accounts table
						if($param['unique'] === true && $this->is_unique($param_tbl, $param['param'], $param['value'], $account_id) != 0){
							$response['error'][] = "The $param[param] `$param[value]` already exists. Please choose another.";
						}

						//If needs validation then apply regex
						if($param['validate'] !== false && $param['validate'] != ''){

							//Filter profanity
							if($param['validate'] == 'profanity'){
								
								if($this->filter_phrase($param['value'])){
									$response['error'][] = "Value entered for `$param[label]` is unavailable. Please choose another.";	
								}
								
							}else{

								//If passed a custom regex
								if(substr($param['validate'],0,1) == '/' && substr($param['validate'],-1) == '/'){
	
									$regex_match = preg_match($param['validate'], $param['value']);
	
								//If passed a predefined regex key
								}else{
	
									try{
										$regex_match = $this->validate($param['value'], $param['validate']);
									}catch(Exception $e){
										$response['error'][] = $e->getMessage();
									}
								}
	
								if(!$regex_match){
									$response['error'][] = "The value entered for `$param[label]` is invalid.";
								}
							}
						}

					}else if($param['required'] === true){
						$response['error'][] = "`$param[label]` is a required field and cannot be empty.";
					}
				}else{
					$response['error'][] = "`$param[label]` does not exist in the accounts or the profiles tables.";
				}

				//Compile the query string
				if($param['hash'] === true){
					
					$hash = $this->sha1_salt($param['value']);
					$param['value'] = $hash['encrypt'];

					$subquery[$param_tbl][] = '`'.$param['param']."` = ?";
					$subquery[$param_tbl][] = '`'.$param['param']."_salt` = ?";
					$sql_params[$param_tbl][] = $param['value'];
					$sql_params[$param_tbl][] = $hash['salt'];

				}else{
					$subquery[$param_tbl][] = '`'.$param['param']."` = ?";
					$sql_params[$param_tbl][] = $param['value'];
				}
			}

			//Only if no errors occured, update account
			if(empty($response['error'])){

				$update_status = true;

				//Update user row in table
				if(!empty($subquery['accounts'])){
					$sql_params['accounts'][] = $account_id;
					$query = $this->db->query("UPDATE `accounts` SET ".implode(',',$subquery['accounts'])." WHERE `account_id` = ?",$sql_params['accounts']);
					if(!$query || $this->db->error()){
						$update_status = false;
					}
				}
				
				//Update profile info
				if(!empty($subquery['account_profiles'])){
					$sql_params['account_profiles'][] = $account_id;
					$query = $this->db->query("UPDATE `account_profiles` SET ".implode(',',$subquery['account_profiles'])." WHERE `account_id` = ?",$sql_params['account_profiles']);
					if(!$query || $this->db->error()){
						$update_status = false;
					}
				}

				if($update_status){

					//Load the profile again (only if current account is the one being updated)
					if($this->account_id == $account_id){
						if($this->login_status()) {
							try{
								$this->load_profile();
							}catch(Exception $e){
								throw new Exception($e->getMessage());
							}
						}
					}
					return true;
				}else{
					throw new Exception($this->db->error());
				}

			//Throw the errors
			}else{
				throw new Exception($response['error'][0]);
			}

		//If there is no active user, then throw exception (active user is required)
		}else{
			throw new Exception('There are no current active sessions.');
		}
	}
	
	/*-----------------------------------/
	* Update account roles
	* @author	Pixel Army
	* @param	$roles		Array of role ids or role names
	* @return	boolean		True upon successful update
	* @throws	Exception
	*/
	public function update_account_roles($roles=array(), $account_id=NULL){
		$account_id = !is_null($account_id) ? $account_id : $this->account_id;
		
		//Roles provided
		if(is_array($roles) && count($roles) > 0){
		
			//Start new transaction
			$this->db->new_transaction();
			
			//Delete old roles
			$delete = $this->db->query("DELETE FROM `account_permissions` WHERE `account_id` = ?", array($account_id));
			
			//Check if roles are valid
			foreach($roles as $role){
				$query = $this->db->query("SELECT `role_id` FROM `account_roles` WHERE " .(is_numeric($role) ? "`role_id`" : "`role_name`"). " = ?", array($role));
				if($this->db->num_rows() > 0){
					$result = $this->db->fetch_array();
					$insert = $this->db->query("INSERT INTO `account_permissions`(`role_id`,`account_id`) VALUES(?,?)", array($result[0]['role_id'], $account_id));
				}else{
					$response['error'][] = "Account role `$role` is not a valid role type.";
				}
				
			}
			
			//If no errors
			if(empty($response['error']) && !$this->db->error()){
				$this->db->commit();
				return true;
				
			}else if($this->db->error()){
				throw new Exception($this->db->error());	
			}else{
				throw new Exception($response['error'][0]);	
			}
		
		//Roles missing	
		}else{
			throw new Exception('Unable to update account roles: No roles provided.');	
		}
	
	}

	/*-----------------------------------/
	* Send a confirmation email to validate account's email address
	*
	* @author	Pixel Army
	* @return	boolean			True upon success
	* @throws	Exception
	*/
	private function confirm_email($account_id=NULL, $template='includes/emailtemplates/confirmation.htm', $stylesheet='css/typography.css'){
		
		$account_id = !is_null($account_id) ? $account_id : $this->account_id;
		
		//Get the random ID to be sent in emails
		$random_id = $this->generate_public_key($account_id, '+30 days');

		//Check to see if Emogrifier is included
		if(class_exists('Emogrifier')){
			
			$recipient = array('name' => $this->get_db_param('account_profiles', 'first_name', $account_id).' '.$this->get_db_param('account_profiles', 'last_name', $account_id), 'email' => $this->get_db_param('accounts', 'email', $account_id));
			
			//Replace the content
			$global = $this->global_settings();
			
			$emailMessage = file_get_contents($_SERVER['DOCUMENT_ROOT'].$this->root.$template);
			$emailMessage = str_replace("[WEBSITE URL]", "http://".$_SERVER['HTTP_HOST'].$this->path, $emailMessage);
			$emailMessage = str_replace("[COMPANY NAME]", $global['company_name'], $emailMessage);
			$emailMessage = str_replace("[NAME]", $recipient['name'], $emailMessage);
			$emailMessage = str_replace("[PUBLIC KEY]", $random_id, $emailMessage);
			
			//used for CMS account creation
			$emailMessage = str_replace("[USERNAME]", $_POST['username'], $emailMessage);
			$emailMessage = str_replace("[PASSWORD]", $_POST['password'], $emailMessage);

			$emailMessage = str_replace("[YEAR]", date("Y"), $emailMessage);
			$emailMessage = str_replace("[COMPANY PHONE]", $global['contact_phone'], $emailMessage);
			$emailMessage = str_replace("[COMPANY FAX]", $global['contact_fax'], $emailMessage);
			$emailMessage = str_replace("[COMPANY EMAIL]", $global['contact_email'], $emailMessage);
			
			//Add Styles
			$emogrifier = new Emogrifier($emailMessage, $_SERVER['DOCUMENT_ROOT']."/".$stylesheet);
			$emailMessage = $emogrifier->emogrify();

			//Check to see if mail function exists
			if(function_exists('smtpEmail') && smtpEmail($recipient['email'], 'Account Confirmation', $emailMessage)){
				//mail sent
			}else{
				trigger_error('Function SMTP not found. System mail used instead.', E_USER_NOTICE);
				$this->send_mail($recipient, 'Account Confirmation', $emailMessage);
			}

			return $random_id;

		}else{
			throw new Exception('Emogrifier class not found.');
		}

	}

	/*-----------------------------------/
	* Send a welcome email to active account email address
	*
	* @author	Pixel Army
	* @return	boolean			True upon success
	* @throws	Exception
	*/
	private function send_welcome($account_id=NULL, $template='includes/emailtemplates/welcome.htm', $stylesheet='css/typography.css'){
	
		$account_id = !is_null($account_id) ? $account_id : $this->account_id;
		
		//Check to see if Emogrifier is included
		if(class_exists('Emogrifier')){
			
			$recipient = array('name' => $this->get_db_param('account_profiles', 'first_name',$account_id).' '.$this->get_db_param('account_profiles', 'last_name',$account_id), 'email' => $this->get_db_param('accounts', 'email',$account_id));

			//Replace the content
			$global = $this->global_settings();
			
			$emailMessage = file_get_contents($_SERVER['DOCUMENT_ROOT'].$this->root.$template);
			$emailMessage = str_replace("[WEBSITE URL]", "http://".$_SERVER['HTTP_HOST'].$this->path, $emailMessage);
			$emailMessage = str_replace("[COMPANY NAME]", $global['company_name'], $emailMessage);
			$emailMessage = str_replace("[NAME]", $recipient['name'], $emailMessage);
			$emailMessage = str_replace("[YEAR]", date("Y"), $emailMessage);
			$emailMessage = str_replace("[COMPANY PHONE]", $global['contact_phone'], $emailMessage);
			$emailMessage = str_replace("[COMPANY FAX]", $global['contact_fax'], $emailMessage);
			$emailMessage = str_replace("[COMPANY EMAIL]", $global['contact_email'], $emailMessage);
			
			//Apply Styles
			$emogrifier = new Emogrifier($emailMessage,$_SERVER['DOCUMENT_ROOT']."/".$stylesheet);
			$emailMessage = $emogrifier->emogrify();

			//Check to see if mail function exists
			if(function_exists('smtpEmail') && smtpEmail($recipient['email'], 'Account Confirmation', $emailMessage)){
				//mail sent
			}else{
				trigger_error('Function SMTP not found. System mail used instead.', E_USER_NOTICE);
				$this->send_mail($recipient, 'Account Confirmation', $emailMessage);
			}

		}else{
			throw new Exception('Emogrifier class not found.');
		}
	}

	/*-----------------------------------/
	* Create the needed information for a successful Remember Me
	*
	* @author	Pixel Army
	* @return	boolean		True upon success
	* @throws	Exception
	*/
	private function remember_me(){

		//Ensure user is properly logged in
		if($this->login_status()){
			$reme_id = $this->generate_unique_id('account_session_log','reme_id');
			
			$params = array($reme_id,$_SESSION['auth']['login_id']);
			$query = $this->db->query("UPDATE `account_session_log` SET `reme_id` = ? WHERE `login_id` = ?",$params);
	
			if($query && !$this->db->error()){
				setcookie('auth[reme_id]',$reme_id,time()+(60*60*24*30),'/');
				return true;
			} else {
				trigger_error('Could not generate Remember me ID.', E_USER_WARNING);
			}			

		}else{
			throw new Exception('User not logged in, cannot set Remember me ID.');
		}
	}
	
	/*-----------------------------------/
	* Allows user to change their account password
	*
	* @author	Pixel Army
	* @param	old_pass		Current account password
	* @param	new_pass		Requested password
	* @param	confirm_pass	Must match new_pass
	* @return	boolean			True/false upon success status
	* @throws	Exception
	*/
	public function change_password($old_pass, $new_pass, $confirm_pass, $account_id=NULL){
		
		//Check for an active user
		if($this->login_status() || !is_null($account_id)){

			//Assign the account_id
			$account_id = is_null($account_id) ? $this->account_id : $account_id;
			
			//Make sure passwords are not blank
			if(!empty($old_pass) && !empty($new_pass) && !empty($confirm_pass)){
				
				//Make sure old password is correct
				$encrypt = $this->sha1_salt($old_pass, $this->password_salt);
				$old_pass = $encrypt['encrypt'];
		
				if($old_pass == $this->password){
					
					//Make sure new passwords match
					if($new_pass == $confirm_pass){
						
						//Update password
						try{
							$this->update_profile(array(
								array('param' => 'password', 'value' => $new_pass, 'required' => true, 'unique' => false, 'validate' => false, 'hash' => true)
							), $account_id);
						}catch(Exception $e){
							throw new Exception($e->getMessage());
						}
						
						
					}else{
						throw new Exception('The passwords you entered do not match. Please try again.');	
					}
					
				}else{
					throw new Exception('The old password you entered is invalid. Please try again.');
				}
				
			}else{
				throw new Exception('Please fill out all the required fields.');	
			}
		
		//Not logged in	
		}else{
			throw new Exception('User not logged in, cannot change password.');
		}
	}

	/*-----------------------------------/
	* Handles the account when "Forgot Password" is requested
	*
	* @author	Pixel Army
	* @return	String		Unique ID
	* @throws	Exception
	*/
	public function forgot_password($email, $template='includes/emailtemplates/forgotpassword.htm', $stylesheet='css/typography.css'){

		//Get the information of the user via email address
		$params = array($email,"Active");
		$query = $this->db->query("SELECT account_id FROM `accounts` WHERE `email` = ? && `status` = ?",$params);
		
		if($query && !$this->db->error() && $this->db->num_rows() > 0 && $email != ""){
			$account_sql = $this->db->fetch_array();
			
			//Get the account information
			$account = $account_sql[0];
			
			//Get the random ID to be sent in emails
			$random_id = $this->generate_public_key($account['account_id']);

			//Check to see if Emogrifier is included
			if(class_exists('Emogrifier')){
				
				$recipient = array('name' => $this->get_db_param('account_profiles', 'first_name', $account['account_id']).' '.$this->get_db_param('account_profiles', 'last_name', $account['account_id']), 'email' => $this->get_db_param('accounts', 'email', $account['account_id']));
				
				//Replace the content
				$global = $this->global_settings();
				
				$emailMessage = file_get_contents($_SERVER['DOCUMENT_ROOT'].$this->root.$template);
				$emailMessage = str_replace("[WEBSITE URL]", "http://".$_SERVER['HTTP_HOST'].$this->path, $emailMessage);
				$emailMessage = str_replace("[COMPANY NAME]", $global['company_name'], $emailMessage);
				$emailMessage = str_replace("[PUBLIC KEY]", $random_id, $emailMessage);
				$emailMessage = str_replace("[YEAR]", date("Y"), $emailMessage);
				$emailMessage = str_replace("[USERNAME]", $this->get_db_param('account_profiles', 'first_name', $account['account_id']), $emailMessage);
				$emailMessage = str_replace("[COMPANY PHONE]", $global['contact_phone'], $emailMessage);
				$emailMessage = str_replace("[COMPANY FAX]", $global['contact_fax'], $emailMessage);
				$emailMessage = str_replace("[COMPANY EMAIL]", $global['contact_email'], $emailMessage);
				
				//Add Styles
				$emogrifier = new Emogrifier($emailMessage, $_SERVER['DOCUMENT_ROOT']."/".$stylesheet);
				$emailMessage = $emogrifier->emogrify();

				//Check to see if mail function exists
				if(function_exists('smtpEmail') && smtpEmail($recipient['email'], 'Password Reset Request', $emailMessage)){
					//mail sent
				}else{
					trigger_error('Function SMTP not found. System mail used instead.', E_USER_NOTICE);
					$this->send_mail($recipient, 'Password Reset Request', $emailMessage);
				}

				return $random_id;

			}else{
				throw new Exception('Emogrifier class not found.');
			}
			
		}else{
			throw new Exception('Account not found for `' .$email. '`.');
		}
	}
	
	/*-----------------------------------/
	* Allows user to reset their password
	*
	* @author	Pixel Army
	* @return	boolean 	True/false upon success status
	* @throws	Exception
	*/
	public function reset_password($new_pass, $confirm_pass, $account_id){
		
		//Make sure passwords are not blank
		if(!empty($new_pass) && !empty($confirm_pass)){
			
			//Make sure new passwords match
			if($new_pass == $confirm_pass){
				
				//Update password
				try{
					$this->update_profile(array(
						array('param' => 'password', 'value' => $new_pass, 'required' => true, 'unique' => false, 'validate' => false, 'hash' => true)
					), $account_id);
				}catch(Exception $e){
					throw new Exception($e->getMessage());
				}
			}else{
				throw new Exception('Your passwords do not match. Please try again.');	
			}	
		}else{
			throw new Exception('Please fill out all the fields.');	
		}
	}

	/*-----------------------------------/
	* Generates the public_key and returns the unique ID to be used in emails
	*
	* @author	Pixel Army
	* @return	String		Unique ID
	* @throws	Exception
	*/
	private function generate_public_key($account_id, $expiry='+24 hours'){

		//If account secret is empty, create one
		if(empty($secret)){
			$this->reset_secret($account_id);
		}

		//Get the account's secret
		$secret = $this->get_db_param('accounts', 'secret', $account_id);

		//Create a random id
		$random_id = $this->generate_unique_id('account_public_keys','key',5);

		//Create Public Key
		$public_key = md5($random_id.$secret);
		try{
			$this->update_profile(array(
				array('param' => 'public_key', 'value' => $public_key, 'required' => false, 'unique' => true, 'validate' => false, 'hash' => false)
			), $account_id);

			//Save code expiry
			$params = array($random_id,date('Y-m-d H:i:s', strtotime($expiry)));
			$query = $this->db->query("INSERT INTO `account_public_keys` (`key`, `expiry`) VALUES (?, ?)",$params);

		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}

		return $random_id;
	}

	/*-----------------------------------/
	* Validates the Public Key of an account
	*
	* @author	Pixel Army
	* @param 	String 		The Public Key to validate
	* @return	boolean		The account_id of the Public Key, or false on failure
	* @throws	Exception
	*/
	public function validate_public_key($key){
		
		//Get the public key info
		$key_query = "SELECT * FROM `accounts` LEFT JOIN `account_public_keys` ON `account_public_keys`.`key` = ? WHERE `accounts`.`public_key` = MD5(CONCAT(?,`accounts`.`secret`))";
		
		$params = array($key,$key);
		$query = $this->db->query($key_query." AND `account_public_keys`.`expiry` >= NOW()",$params);
		$response = false;
		
		if($query && !$this->db->error() && $this->db->num_rows() > 0){
			//Only if provided key validates to proper non-expired Public Key
			$key_sql = $this->db->fetch_array();
			$key_sql = $key_sql[0];
			$response = $key_sql['account_id'];
		} else {
			$query = $this->db->query($key_query,$params);
			if($query && !$this->db->error() && $this->db->num_rows() > 0){
				Account::clear_public_key($key);
			}
		}		

		//Clean up the keys table
		Account::clear_expired_keys();

		return $response;
	}
	
	/*-----------------------------------/
	* Clears given Public Key
	*
	* @author	Pixel Army
	* @return	boolean		True/false upon success status
	*/
	public function clear_public_key($key){
		$params = array($key);
		return $this->db->query("DELETE FROM `account_public_keys` WHERE `key` = ?",$params);
	}

	/*-----------------------------------/
	* Clears all the keys for Public Keys that are expired
	*
	* @author	Pixel Army
	* @return	boolean		True/false upon success status
	*/
	private function clear_expired_keys(){
		return $this->db->query("DELETE FROM `account_public_keys` WHERE `expiry` < NOW()");
	}

	/*-----------------------------------/
	* Reset the accounts Secret
	*
	* @author	Pixel Army
	* @return	boolean		True/flase upon success status
	* @throws	Exception
	*/
	private function reset_secret($account_id){
		$secret = $this->generate_unique_id('accounts', 'secret');
		try{
			return $this->update_profile(array(
							array('param' => 'secret', 'value' => $secret, 'required' => false, 'unique' => true, 'validate' => false, 'hash' => false)
						), $account_id);
		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	/*-----------------------------------/
	* Uses the sha1 algorithm to encypt values passed
	*
	* @author	Pixel Army
	* @param	$value	The value to be encrypted
	* @param	$salt	The salt used to encrypt the value
	* @return	Array	'encrypt' => encrypted hash, 'salt' => salt used in hashing
	*/
	private function sha1_salt($value='', $salt=''){
		$hash['encrypt'] = '';
		$hash['salt'] = array();
		
		//Generate password
		if($value == ''){
			$value = $this->generate_password(10);
		}

		//Use hash stored in db
		if($salt != ''){
			$hash['salt'] = $salt;
			
		}else{

			//Generate new hash
			$chars = str_split('~`!@#$%^&*()[]{}-_\/|\'";:,.+=<>?');
			$keys = array_rand($chars, 10);

			foreach($keys as $key){
				$hash['salt'][] .= $chars[$key];
			}

			$hash['salt'] = implode('', $hash['salt']);
			$hash['salt'] = sha1($hash['salt']);
		}

		$hash['encrypt'] = sha1($hash['salt'].$value.$hash['salt']);
		return $hash;
	}

	/*-----------------------------------/
	* Generates new random password
	*
	* @author	Pixel Army
	* @param	$length		Length of the password characters
	* @param	$special	Boolean whether or not to include special characters
	* @return	String		The random password
	*/
	private function generate_password($length, $special=false){
		$pass = '';
		if($special){
			$chars = str_split('abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%^&*()_+=-~');
		}else{
			$chars = str_split('abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789');
		}

		$keys = array_rand($chars,$length);

		foreach($keys as $key){
			$pass .= $chars[$key];
		}
		return $pass;
	}

	/*-----------------------------------/
	* Generates a unique ID per given table
	*
	* @author	Pixel Army
	* @param	$length		The character length of ID
	* @return	String		The random ID
	*/
	private function generate_unique_id($table, $field, $length=32){
		$query = $this->db->query("SELECT * FROM `$table`");
		if($query && !$this->db->error() && $this->db->num_rows() == 0){
			$unique_id = md5(rand());
		} else {
			$params = array("rand_id","");
			$query = $this->db->query("SELECT MD5(RAND()) AS rand_id FROM `$table` WHERE ? NOT IN (SELECT `$field` FROM `$table` WHERE `$field` IS NOT NULL AND `$field` <> ?) LIMIT 1",$params);
			
			if($query && !$this->db->error()){
				$response = $this->db->fetch_array();
				$response = $response[0];
			} else {
				throw new Exception($this->db->error());
			}
			
			$unique_id = $response['rand_id'];
		}
		return substr($unique_id,0,$length);
	}

	/*-----------------------------------/
	* Get the user's IP address
	*
	* @author	Pixel Army
	* @return	String		IP Address
	*/
	public function get_ip(){
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
			if (array_key_exists($key, $_SERVER) === true){
				foreach (explode(',', $_SERVER[$key]) as $ip){
					if (filter_var($ip, FILTER_VALIDATE_IP) !== false){
						return $ip;
					}
				}
			}
		}
	}

	/*-----------------------------------/
	* Sends an email using the built-in mail funtionn, as a fallback for the SMTP Mail
	*
	* @author	Pixel Army
	* @param	$to			Receipient email address
	* @param	$subject	The subject
	* @param	$message	The body of the message
	* @return	boolean		True/false based on the success of the mail()
	*/
	protected function send_mail($to, $subject, $message){

		//Format the headers
		$company = $this->global_settings();
		$to = mb_encode_mimeheader("$to[name] <$to[email]>");
		$from = mb_encode_mimeheader("$company[company_name] <".$company['contact_email'].">");

		$headers = array(
			'MIME-Version: 1.0',
			'Content-Type: text/html; charset="UTF-8";',
			'Content-Transfer-Encoding: 7bit',
			'Date: ' . date('r', $_SERVER['REQUEST_TIME']),
			'Message-ID: <' . $_SERVER['REQUEST_TIME'] . md5($_SERVER['REQUEST_TIME']) . '@' . $_SERVER['SERVER_NAME'] . '>',
			'From: ' . $from,
			'Reply-To: ' . $from,
			'Return-Path: ' . $from,
			'X-Mailer: PHP v' . phpversion(),
			'X-Originating-IP: ' . $_SERVER['SERVER_ADDR']
		);

		return mail($to, $subject, $message, implode("\r\n", $headers));
	}

	/*-----------------------------------/
	* Filter given argument against a list of prohibited words
	*
	* @author	Pixel Army
	* @param	$value		The value to be filtered
	* @return	boolean		True if value contains prohibited language
	*/
	protected function filter_phrase($value){
	
		$query = $this->db->query("SELECT * FROM `word_filters`");
		
		if($query && !$this->db->error()){
			$profanity = $this->db->fetch_array();
		}
		
		foreach($profanity as $word){
			if(strstr(strtolower($value), $word['word'])){
				return true;
			}
		}
		return false;
	}
	
	/*-----------------------------------/
	* Display formatted alert for given text
	*
	* @author	Pixel Army
	* @param	$text		Content to appear in alert
	* @param	$alert		True if success, false if error
	* @return	boolean		True if value contains prohibited language
	*/
	public function alert($text, $alert){
		if($alert){
			$string = "<div class='success'><p><b>Success!</b> ";
		}else{
			$string = "<div class='error'><p><b>Error!</b> ";
		} 
		$string .= $text;
		$string .= "</p></div>";
		
		return $string;
	}
	
	/*-----------------------------------/
	* Return global website settings from db
	*
	* @author	Pixel Army
	* @return	Array of global data
	*/
	public function global_settings(){
		$response = array();
		
		$params = array(1);
		$query = $this->db->query("SELECT * FROM `global_settings` WHERE id = ?",$params);
		
		if($query && !$this->db->error()){
			$response = $this->db->fetch_array();
			$response = $response[0];
		}
				
		return $response;
	}
	
}
	
?>